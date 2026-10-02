<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_reminders', function (Blueprint $table) {
            $table->unsignedTinyInteger('reminder_bulan')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('document_reminders', function (Blueprint $table) {
            $table->unsignedTinyInteger('reminder_bulan')->nullable(false)->change();
        });
    }
};
