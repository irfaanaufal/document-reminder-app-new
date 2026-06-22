<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_reminders', function (Blueprint $table) {
            $table->date('tanggal_expired')->nullable()->change();
            $table->unsignedTinyInteger('reminder_bulan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_reminders', function (Blueprint $table) {
            $table->date('tanggal_expired')->nullable(false)->change();
            $table->unsignedTinyInteger('reminder_bulan')->nullable(false)->change();
        });
    }
};
