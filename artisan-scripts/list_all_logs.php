<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
$rows = DB::table('reminder_notification_logs')->get();
if ($rows->isEmpty()) { echo "No logs at all\n"; exit(0); }
foreach ($rows as $r) {
    echo sprintf("id=%s | doc_id=%s | scheduled_for=%s | status=%s | recipient_phone=%s\n", $r->id, $r->document_reminder_id, $r->scheduled_for, $r->status, $r->recipient_phone ?? '-');
}
