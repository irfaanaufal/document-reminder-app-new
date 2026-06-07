<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

$rows = DB::table('reminder_notification_logs')->orderBy('id','desc')->limit(20)->get();
if ($rows->isEmpty()) { echo "No logs\n"; exit(0); }
foreach ($rows as $r) {
    echo "---\n";
    echo sprintf("id=%s | doc=%s | scheduled=%s | status=%s | phone=%s | created=%s | sent_at=%s\n", $r->id, $r->document_reminder_id, $r->scheduled_for, $r->status, $r->recipient_phone ?? '-', $r->created_at, $r->sent_at ?? '-');
    echo "provider_response:\n";
    if ($r->provider_response) {
        if (is_string($r->provider_response)) {
            echo $r->provider_response . "\n";
        } else {
            echo json_encode($r->provider_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        }
    } else {
        echo "(empty)\n";
    }
}
