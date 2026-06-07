<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run reminder sending task every day at 08:00 AM
        $schedule->command('reminders:send')
            ->dailyAt('08:00')
            ->name('send-document-reminders')
            ->description('Send daily document reminders to PIC via WhatsApp');

        // Optional: Run every 6 hours for more frequent reminders
        // $schedule->command('reminders:send')
        //     ->everyMinutes(360)
        //     ->name('send-document-reminders')
        //     ->description('Send document reminders to PIC via WhatsApp');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
