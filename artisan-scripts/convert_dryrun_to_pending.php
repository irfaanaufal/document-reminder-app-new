<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$date = '2026-05-30';
$affected = DB::table('reminder_notification_logs')
    ->whereDate('scheduled_for', $date)
    ->where('status', 'dry_run')
    ->update(['status' => 'pending']);

echo "Updated: {$affected}\n";
