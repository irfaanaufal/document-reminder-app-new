<?php

namespace App\Console\Commands;

use App\Models\ReminderNotificationLog;
use App\Services\ReminderMailService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDocumentReminders extends Command
{
    protected $signature = 'reminders:send {--dry-run : Simulate sending without calling mail} {--date= : Override today date (Y-m-d) for testing} {--reminder-id= : Limit to a single document reminder id} {--email= : Override target email for testing}';

    protected $description = 'Send reminder notifications for documents to internal PIC via email';

    public function handle(ReminderMailService $mailService): int
    {
        $today = $this->option('date')
            ? Carbon::parse((string) $this->option('date'))->startOfDay()
            : Carbon::today()->startOfDay();

        $dryRun = (bool) $this->option('dry-run');
        $logsQuery = ReminderNotificationLog::query()
            ->with(['documentReminder:id,no_dokumen,nama_dokumen,pic_nama,pic_email,penerbit_tujuan,tanggal_expired,tanggal_terbit,reminder_bulan,attachment_path,attachment_name'])
            ->where('status', 'pending')
            ->whereDate('scheduled_for', '<=', $today->toDateString())
            ->when($this->option('reminder-id'), function ($query) {
                $query->where('document_reminder_id', (int) $this->option('reminder-id'));
            })
            ->when($this->option('email'), function ($query) {
                $query->where('recipient_email', (string) $this->option('email'));
            });

        $logs = $logsQuery->get();

        if ($logs->isEmpty()) {
            $this->info('No pending reminder logs found to send.');

            return self::SUCCESS;
        }

        $sentCount = 0;
        $skippedCount = 0;
        $failedLogIds = collect();

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
                $failedLogIds->push($log->id);

                $this->warn("Skipped log {$log->id}: related document missing.");
                continue;
            }

            $email = $this->option('email')
                ? $this->option('email')
                : $log->recipient_email;

            $recipientName = trim((string) ($log->recipient_name ?: $document->pic_nama));

            if (empty($email)) {
                $skippedCount++;
                $log->increment('attempt_count');
                $log->update([
                    'status' => 'failed',
                    'provider_response' => [
                        'message' => 'PIC internal email is empty.',
                    ],
                ]);
                $failedLogIds->push($log->id);

                $this->warn("Skipped {$document->no_dokumen}: PIC internal email is empty.");
                continue;
            }

            $daysLeft = null;
            if ($document->tanggal_expired !== null) {
                $daysLeft = (int) $today->diffInDays($document->tanggal_expired, false);
            }

            $reminderRule = strtolower(trim((string) ($log->reminder_rule ?? '')));

            $closingLine = 'Demikian informasi ini kami sampaikan. Atas perhatian dan tindak lanjutnya, kami ucapkan terima kasih.';
            if ($daysLeft !== null && $daysLeft === 0) {
                $closingLine = 'Berkaitan dengan batas waktu yang berakhir hari ini, mohon dokumen ini menjadi prioritas utama untuk segera diproses. Terima kasih atas perhatian dan kerjasamanya.';
            } elseif ($daysLeft !== null && $daysLeft < 0) {
                $closingLine = 'Dokumen ini telah melewati batas waktu. Mohon segera dilakukan evaluasi dan penanganan untuk menghindari konsekuensi lebih lanjut. Terima kasih.';
            }

            $intro = 'Kami informasikan bahwa dokumen berikut telah memasuki masa pemantauan dan memerlukan persiapan penanganan:';
            if ($reminderRule === 'h-7') {
                $intro = 'Peringatan: Dokumen berikut akan jatuh tempo dalam 7 hari. Mohon segera dilakukan pengecekan dan persiapan perpanjangan atau pemrosesan:';
            } elseif ($reminderRule === 'h-0') {
                $intro = 'URGENT: Dokumen berikut mencapai batas waktu hari ini. Mohon segera diproses untuk menghindari risiko kedaluwarsa:';
            } elseif ($reminderRule === 'h-14') {
                $intro = 'Dokumen berikut akan mencapai tanggal jatuh tempo dalam 14 hari ke depan. Mohon dapat dipersiapkan tindak lanjut yang diperlukan:';
            }

            $sisaWaktuDisplay = '-';
            if ($daysLeft !== null) {
                if ($daysLeft === 0) {
                    $sisaWaktuDisplay = 'Hari ini';
                } elseif ($daysLeft > 0) {
                    $sisaWaktuDisplay = $daysLeft . ' hari';
                } else {
                    $sisaWaktuDisplay = 'LEWAT ' . abs($daysLeft) . ' hari';
                }
            }

            $viewData = [
                'pic_nama' => $recipientName,
                'nama_dokumen' => $document->nama_dokumen,
                'no_dokumen' => $document->no_dokumen,
                'penerbit_tujuan' => $document->penerbit_tujuan,
                'tanggal_expired' => optional($document->tanggal_expired)->format('d-m-Y'),
                'sisa_waktu' => $sisaWaktuDisplay,
                'sisa_hari' => $daysLeft,
                'intro' => $intro,
                'closing' => $closingLine,
                'attachment_name' => $document->attachment_name,
            ];

            $subject = $mailService->buildSubject([
                'nama_dokumen' => $document->nama_dokumen,
                'reminder_rule' => (string) $log->reminder_rule,
                'sisa_hari' => $daysLeft,
            ]);

            try {
                if ($dryRun) {
                    $body = view('emails.reminder', $viewData)->render();
                    $sentCount++;
                    $this->info("[DRY-RUN] Would send reminder for {$document->no_dokumen} to {$email}.");
                    continue;
                }

                $log->increment('attempt_count');
                $response = $mailService->sendHtmlEmail(
                    $email,
                    $subject,
                    'emails.reminder',
                    $viewData,
                    $document->attachment_path,
                    $document->attachment_name
                );

                $log->update([
                    'status' => $response['ok'] ? 'sent' : 'failed',
                    'sent_at' => $response['ok'] ? now() : null,
                    'provider_response' => $response,
                    'recipient_email' => $email,
                    'recipient_name' => $recipientName,
                ]);

                if ($response['ok']) {
                    $sentCount++;
                    $this->info("Sent reminder for {$document->no_dokumen} to {$email}.");
                } else {
                    $failedLogIds->push($log->id);
                    $this->error("Failed reminder for {$document->no_dokumen} to {$email}.");
                }
            } catch (\Throwable $throwable) {
                $log->update([
                    'status' => 'failed',
                    'provider_response' => [
                        'message' => $throwable->getMessage(),
                    ],
                ]);
                $failedLogIds->push($log->id);

                $this->error("Error sending {$document->no_dokumen}: {$throwable->getMessage()}");
            }
        }

        $this->info("Done. Sent: {$sentCount}, skipped: {$skippedCount}." . ($dryRun ? ' (dry-run: status unchanged)' : ''));

        if (! $dryRun && $failedLogIds->isNotEmpty()) {
            $failedLogs = ReminderNotificationLog::whereIn('id', $failedLogIds)->get();
            $mailService->sendFailureNotification($failedLogs, $sentCount);
            $this->info("Failure notification sent to IT team.");
        }

        return self::SUCCESS;
    }
}
