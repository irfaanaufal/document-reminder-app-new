<?php

namespace App\Console\Commands;

use App\Models\DocumentReminder;
use App\Models\ReminderNotificationLog;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class QueueDocumentReminders extends Command
{
    protected $signature = 'reminders:queue {--date= : Override today date (Y-m-d) for testing} {--reminder-id= : Limit to a single document reminder id} {--phone= : Override target phone number for testing}';

    protected $description = 'Prepare pending reminder logs for documents that need notification';

    public function handle(FonnteService $fonnteService): int
    {
        $today = $this->option('date')
            ? Carbon::parse((string) $this->option('date'))->startOfDay()
            : Carbon::today()->startOfDay();

        $documents = DocumentReminder::query()
            ->with('internalPics')
            ->when($this->option('reminder-id'), function ($query) {
                $query->whereKey((int) $this->option('reminder-id'));
            })
            ->get();

        $createdCount = 0;
        $existingCount = 0;

        foreach ($documents as $document) {
            foreach ($this->buildScheduleDates($document) as $scheduledFor => $ruleLabel) {
                if ($scheduledFor !== $today->toDateString()) {
                    continue;
                }

                // Collect all recipients: internal PICs from pivot table
                $recipients = [];

                if ($document->internalPics->isNotEmpty()) {
                    foreach ($document->internalPics as $pic) {
                        $phone = $this->option('phone')
                            ? $fonnteService->normalizePhoneForWhatsapp((string) $this->option('phone'))
                            : $fonnteService->normalizePhoneForWhatsapp($pic->pivot->no_telpon ?? $pic->no_telpon);

                        $recipients[] = [
                            'phone' => $phone,
                            'name' => $pic->pivot->nama ?? $pic->nama,
                        ];
                    }
                } else {
                    // Fallback to legacy primary PIC fields
                    $phone = $this->option('phone')
                        ? $fonnteService->normalizePhoneForWhatsapp((string) $this->option('phone'))
                        : $fonnteService->normalizePhoneForWhatsapp($document->pic_telpon);

                    $recipients[] = [
                        'phone' => $phone,
                        'name' => trim((string) $document->pic_nama),
                    ];
                }

                foreach ($recipients as $recipient) {
                    $log = ReminderNotificationLog::firstOrCreate([
                        'document_reminder_id' => $document->id,
                        'recipient_phone' => $recipient['phone'],
                        'scheduled_for' => $scheduledFor,
                    ], [
                        'recipient_name' => $recipient['name'],
                        'reminder_rule' => $ruleLabel,
                        'status' => 'pending',
                        'attempt_count' => 0,
                    ]);

                    if ($log->wasRecentlyCreated) {
                        $createdCount++;
                        $this->info("Queued {$document->no_dokumen} for {$scheduledFor} -> {$recipient['name']} ({$recipient['phone']}).");
                        continue;
                    }

                    $existingCount++;
                    $this->line("Already queued {$document->no_dokumen} for {$scheduledFor} -> {$recipient['name']}.");
                }
            }
        }

        $this->info("Done. Created: {$createdCount}, existing: {$existingCount}.");

        return self::SUCCESS;
    }

    /**
     * @return array<string, string>
     */
    private function buildScheduleDates(DocumentReminder $document): array
    {
        $expired = Carbon::parse($document->tanggal_expired)->startOfDay();
        $monthlyStart = $expired->copy()->subMonthsNoOverflow((int) $document->reminder_bulan)->startOfDay();
        $monthlyEnd = $expired->copy()->subMonthNoOverflow()->startOfDay();

        $schedule = [];

        for ($date = $monthlyStart->copy(); $date->lte($monthlyEnd); $date->addMonthNoOverflow()) {
            $schedule[$date->toDateString()] = 'monthly';
        }

        $schedule[$expired->copy()->subDays(14)->toDateString()] = 'h-14';
        $schedule[$expired->copy()->subDays(7)->toDateString()] = 'h-7';
        $schedule[$expired->toDateString()] = 'h-0';

        return $schedule;
    }
}