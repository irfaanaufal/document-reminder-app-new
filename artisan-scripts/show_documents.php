<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$ids = [1,13];
$rows = DB::table('document_reminders')->whereIn('id', $ids)->get();

if ($rows->isEmpty()) {
    echo "No documents found for ids: " . implode(',', $ids) . "\n";
    exit(0);
}

foreach ($rows as $r) {
    echo sprintf("id=%s | no_dokumen=%s | penerbit_tujuan=%s | tanggal_expired=%s | pic_nama=%s | pic_telpon=%s\n",
        $r->id,
        $r->no_dokumen ?? '-',
        $r->penerbit_tujuan ?? '-',
        $r->tanggal_expired ?? '-',
        $r->pic_nama ?? '-',
        $r->pic_telpon ?? '-'
    );
}
