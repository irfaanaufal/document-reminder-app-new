<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$date = '2026-05-30';
$rows = DB::table('reminder_notification_logs')->whereDate('scheduled_for', $date)->get();

if ($rows->isEmpty()) {
    echo "No rows for {$date}\n";
    exit(0);
}

foreach ($rows as $l) {
    echo sprintf("%s | %s | %s | %s | %s\n", $l->id, $l->document_reminder_id ?? '-', $l->status ?? '-', $l->recipient_phone ?? '-', $l->attempt_count ?? 0);
}
