<?php

namespace App\Console\Commands;

use App\Models\DocumentReminder;
use App\Models\ReminderNotificationLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class QueueDocumentReminders extends Command
{
    protected $signature = 'reminders:queue {--date= : Override today date (Y-m-d) for testing} {--reminder-id= : Limit to a single document reminder id} {--email= : Override target email for testing}';

    protected $description = 'Prepare pending reminder logs for documents that need notification';

    public function handle(): int
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
        $skippedCount = 0;
        $lookback = $today->copy()->subDays(3)->toDateString();

        foreach ($documents as $document) {
            foreach ($this->buildScheduleDates($document) as $scheduledFor => $ruleLabel) {
                if ($scheduledFor > $today->toDateString()) {
                    continue;
                }
                if ($scheduledFor < $lookback) {
                    continue;
                }

                $recipients = [];

                if ($document->internalPics->isNotEmpty()) {
                    foreach ($document->internalPics as $pic) {
                        $email = $this->option('email')
                            ? $this->option('email')
                            : $pic->email;

                        if (empty($email)) {
                            $skippedCount++;
                            $this->warn("Skipped {$document->no_dokumen}: PIC {$pic->name} has no email.");
                            continue;
                        }

                        $recipients[] = [
                            'email' => $email,
                            'name' => $pic->pivot->nama ?? $pic->name,
                        ];
                    }
                } else {
                    $this->warn("No internal PICs for {$document->no_dokumen}, skipping.");
                    continue;
                }

                foreach ($recipients as $recipient) {
                    $log = ReminderNotificationLog::firstOrCreate([
                        'document_reminder_id' => $document->id,
                        'recipient_email' => $recipient['email'],
                        'scheduled_for' => $scheduledFor,
                    ], [
                        'recipient_name' => $recipient['name'],
                        'reminder_rule' => $ruleLabel,
                        'status' => 'pending',
                        'attempt_count' => 0,
                    ]);

                    if ($log->wasRecentlyCreated) {
                        $createdCount++;
                        $this->info("Queued {$document->no_dokumen} for {$scheduledFor} -> {$recipient['name']} ({$recipient['email']}).");
                        continue;
                    }

                    if ($log->status === 'dry_run') {
                        $log->update(['status' => 'pending']);
                        $this->info("Requeued dry-run log for {$document->no_dokumen} on {$scheduledFor} -> {$recipient['email']}.");
                        continue;
                    }

                    $existingCount++;
                    $this->line("Already queued {$document->no_dokumen} for {$scheduledFor} -> {$recipient['name']}.");
                }
            }
        }

        $this->info("Done. Created: {$createdCount}, existing: {$existingCount}, skipped: {$skippedCount}.");

        return self::SUCCESS;
    }

    /**
     * @return array<string, string>
     */
    private function buildScheduleDates(DocumentReminder $document): array
    {
        if (is_null($document->tanggal_expired) || is_null($document->reminder_bulan)) {
            return [];
        }

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
