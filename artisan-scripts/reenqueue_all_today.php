<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$date = '2026-05-30';

$affected = DB::table('reminder_notification_logs')
    ->whereDate('scheduled_for', $date)
    ->whereIn('status', ['sent','failed','dry_run'])
    ->update([
        'status' => 'pending',
        'attempt_count' => 0,
        'sent_at' => null,
        'provider_response' => null,
    ]);

echo "Re-queued {$affected} log(s) for {$date}\n";
