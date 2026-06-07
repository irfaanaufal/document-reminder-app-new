<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('reminder_notification_logs')->get();

if ($rows->isEmpty()) {
    echo "No reminder logs\n";
    exit(0);
}

foreach ($rows as $r) {
    echo sprintf("id=%s | doc_id=%s | status=%s | snap_no=%s | snap_nama=%s | snap_penerbit=%s | snap_expired=%s\n",
        $r->id,
        $r->document_reminder_id,
        $r->status,
        $r->snapshot_no_dokumen ?? '-',
        $r->snapshot_nama_dokumen ?? '-',
        $r->snapshot_penerbit ?? '-',
        $r->snapshot_tanggal_expired ?? '-'
    );
}
