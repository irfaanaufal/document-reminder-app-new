<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('reminders:queue')
    ->dailyAt('09:55')
    ->days([1,2,3,4,5,6])
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('reminders:send')
    ->dailyAt('10:00')
    ->days([1,2,3,4,5,6])
    ->withoutOverlapping()
    ->onOneServer();
