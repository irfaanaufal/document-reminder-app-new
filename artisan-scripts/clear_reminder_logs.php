<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$count = DB::table('reminder_notification_logs')->count();
if ($count === 0) {
    echo "No reminder logs to delete.\n";
    exit(0);
}

DB::table('reminder_notification_logs')->delete();

echo "Deleted: {$count} reminder log(s)\n";
