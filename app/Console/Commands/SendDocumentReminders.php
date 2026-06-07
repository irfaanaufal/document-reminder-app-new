<?php

namespace App\Console\Commands;

use App\Models\ReminderNotificationLog;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDocumentReminders extends Command
{
    protected $signature = 'reminders:send {--dry-run : Simulate sending without calling Fonnte} {--date= : Override today date (Y-m-d) for testing} {--reminder-id= : Limit to a single document reminder id} {--phone= : Override target phone number for testing}';

    protected $description = 'Send reminder notifications for documents to internal PIC via Fonnte';

    public function handle(FonnteService $fonnteService): int
    {
        $today = $this->option('date')
            ? Carbon::parse((string) $this->option('date'))->startOfDay()
            : Carbon::today()->startOfDay();

        $dryRun = (bool) $this->option('dry-run');
        $logsQuery = ReminderNotificationLog::query()
            ->with(['documentReminder:id,no_dokumen,nama_dokumen,pic_nama,pic_telpon,penerbit_tujuan,tanggal_expired,tanggal_terbit,reminder_bulan'])
            ->where('status', 'pending')
            ->whereDate('scheduled_for', '<=', $today->toDateString())
            ->when($this->option('reminder-id'), function ($query) {
                $query->where('document_reminder_id', (int) $this->option('reminder-id'));
            })
            ->when($this->option('phone'), function ($query) {
                $query->where('recipient_phone', (string) $this->option('phone'));
            });

        $logs = $logsQuery->get();

        if ($logs->isEmpty()) {
            $this->info('No pending reminder logs found to send.');

            return self::SUCCESS;
        }

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($logs as $log) {
            $document = $log->documentReminder;

            if (! $document) {
                $skippedCount++;
                $log->increment('attempt_count');
                $log->update([
                    'status' => 'failed',
                    'provider_response' => [
                        'message' => 'Dokumen terkait log tidak ditemukan.',
                    ],
                ]);

                $this->warn("Skipped log {$log->id}: related document missing.");
                continue;
            }

            $phone = $this->option('phone')
                ? $fonnteService->normalizePhoneForWhatsapp((string) $this->option('phone'))
                : $fonnteService->normalizePhoneForWhatsapp($log->recipient_phone ?: $document->pic_telpon);

            $recipientName = trim((string) ($log->recipient_name ?: $document->pic_nama));

            if ($phone === '') {
                $skippedCount++;
                $log->increment('attempt_count');
                $log->update([
                    'status' => 'failed',
                    'provider_response' => [
                        'message' => 'PIC internal phone is empty.',
                    ],
                ]);

                $this->warn("Skipped {$document->no_dokumen}: PIC internal phone is empty.");
                continue;
            }

            $message = $fonnteService->buildReminderMessage([
                'pic_nama' => $recipientName,
                'nama_dokumen' => $document->nama_dokumen,
                'no_dokumen' => $document->no_dokumen,
                'penerbit_tujuan' => $document->penerbit_tujuan,
                'tanggal_terbit' => optional($document->tanggal_terbit)->format('d-m-Y'),
                'tanggal_expired' => optional($document->tanggal_expired)->format('d-m-Y'),
                'reminder_bulan' => $document->reminder_bulan,
                'reminder_rule' => (string) $log->reminder_rule,
                'sisa_hari' => $today->diffInDays($document->tanggal_expired, false),
            ]);

            $log->increment('attempt_count');

            try {
                $response = $dryRun
                    ? [
                        'ok' => true,
                        'status' => 200,
                        'body' => [
                            'dry_run' => true,
                            'target' => $phone,
                            'message' => $message,
                        ],
                    ]
                    : $fonnteService->sendMessage($phone, $message);

                $log->update([
                    'status' => $dryRun ? 'dry_run' : ($response['ok'] ? 'sent' : 'failed'),
                    'sent_at' => $response['ok'] && ! $dryRun ? now() : null,
                    'provider_response' => $response,
                    'recipient_phone' => $phone,
                    'recipient_name' => $recipientName,
                ]);

                if ($response['ok']) {
                    $sentCount++;
                    $this->info(($dryRun ? '[DRY-RUN] Would send' : 'Sent') . " reminder for {$document->no_dokumen} to {$phone}.");
                } else {
                    $this->error("Failed reminder for {$document->no_dokumen} to {$phone}. Status: {$response['status']}");
                }
            } catch (\Throwable $throwable) {
                $log->update([
                    'status' => 'failed',
                    'provider_response' => [
                        'message' => $throwable->getMessage(),
                    ],
                ]);

                $this->error("Error sending {$document->no_dokumen}: {$throwable->getMessage()}");
            }
        }

        $this->info("Done. Sent: {$sentCount}, skipped: {$skippedCount}.");

        return self::SUCCESS;
    }
}