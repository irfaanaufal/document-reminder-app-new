<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom pic_email NOT NULL — set nilai non-email (sisa rename pic_telpon) ke string kosong.
        DB::table('document_reminders')
            ->whereNotNull('pic_email')
            ->where('pic_email', 'not like', '%@%')
            ->update(['pic_email' => '']);
    }

    public function down(): void
    {
        // Data tidak dapat dikembalikan (nilai lama sudah di-reset ke '').
    }
};
