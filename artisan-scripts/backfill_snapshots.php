<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$logs = DB::table('reminder_notification_logs')
    ->whereNull('snapshot_no_dokumen')
    ->orWhere('snapshot_no_dokumen', '=','')
    ->get();

if ($logs->isEmpty()) {
    echo "No logs to backfill\n";
    exit(0);
}

$updated = 0;
foreach ($logs as $log) {
    $doc = DB::table('document_reminders')->where('id', $log->document_reminder_id)->first();
    if (! $doc) continue;
    DB::table('reminder_notification_logs')->where('id', $log->id)->update([
        'snapshot_no_dokumen' => $doc->no_dokumen,
        'snapshot_nama_dokumen' => $doc->nama_dokumen,
        'snapshot_penerbit' => $doc->penerbit_tujuan,
        'snapshot_tanggal_expired' => $doc->tanggal_expired,
    ]);
    $updated++;
}

echo "Updated snapshots for {$updated} log(s)\n";
