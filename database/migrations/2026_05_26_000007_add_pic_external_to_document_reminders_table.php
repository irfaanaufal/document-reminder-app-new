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
            $table->string('pic_external_nama')->nullable()->after('pic_telpon');
            $table->string('pic_external_telpon')->nullable()->after('pic_external_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_reminders', function (Blueprint $table) {
            $table->dropColumn(['pic_external_nama', 'pic_external_telpon']);
        });
    }
};