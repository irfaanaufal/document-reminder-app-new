<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('document_reminders', function (Blueprint $table) {
            $table->string('pic_nama')->after('nama_dokumen');
            $table->string('pic_telpon')->after('pic_nama');
            $table->string('penerbit_tujuan')->after('pic_telpon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_reminders', function (Blueprint $table) {
            $table->dropColumn(['pic_nama', 'pic_telpon', 'penerbit_tujuan']);
        });
    }
};
