<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FonnteService;

/** @var FonnteService $svc */
$svc = app(FonnteService::class);

$cases = [
    ['pic_nama'=>'Budi','nama_dokumen'=>'Kontrak A','no_dokumen'=>'SLO-001','penerbit_tujuan'=>'PT Contoh','tanggal_expired'=>'30-05-2026','sisa_hari'=>3,'reminder_rule'=>'h-3'],
    ['pic_nama'=>'Siti','nama_dokumen'=>'Kontrak B','no_dokumen'=>'SLO-002','penerbit_tujuan'=>'PT Contoh','tanggal_expired'=>'30-05-2026','sisa_hari'=>0,'reminder_rule'=>'h-0'],
];

foreach ($cases as $c) {
    echo "--- Message for {$c['no_dokumen']} (sisa_hari={$c['sisa_hari']}) ---\n";
    echo $svc->buildReminderMessage($c) . "\n\n";
}
