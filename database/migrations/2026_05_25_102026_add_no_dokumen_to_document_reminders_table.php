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
            $table->string('no_dokumen')->nullable()->after('nama_dokumen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_reminders', function (Blueprint $table) {
            $table->dropColumn('no_dokumen');
        });
    }
};
