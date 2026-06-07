<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Services\FonnteService;
use Carbon\Carbon;

$svc = app(FonnteService::class);
$today = Carbon::parse('2026-05-30')->startOfDay();

$logs = DB::table('reminder_notification_logs')
    ->whereDate('scheduled_for', $today->toDateString())
    ->get();

if ($logs->isEmpty()) {
    echo "No reminder logs for {$today->toDateString()}\n";
    exit(0);
}

foreach ($logs as $log) {
    $doc = DB::table('document_reminders')->where('id', $log->document_reminder_id)->first();
    $sisa = null;
    if ($doc && $doc->tanggal_expired) {
        $sisa = $today->diffInDays(Carbon::parse($doc->tanggal_expired), false);
    }

    $data = [
        'pic_nama' => $doc->pic_nama ?? null,
        'nama_dokumen' => $doc->nama_dokumen ?? null,
        'no_dokumen' => $doc->no_dokumen ?? null,
        'penerbit_tujuan' => $doc->penerbit_tujuan ?? null,
        'tanggal_expired' => $doc->tanggal_expired ? Carbon::parse($doc->tanggal_expired)->format('d-m-Y') : null,
        'reminder_bulan' => $doc->reminder_bulan ?? null,
        'reminder_rule' => $log->reminder_rule ?? null,
        'sisa_hari' => $sisa,
    ];

    echo "--- Log {$log->id} | Document: " . ($doc->no_dokumen ?? '-') . " ---\n";
    echo $svc->buildReminderMessage($data) . "\n\n";
}
