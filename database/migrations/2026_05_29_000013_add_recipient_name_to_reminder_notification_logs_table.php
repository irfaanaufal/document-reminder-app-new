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
        Schema::table('reminder_notification_logs', function (Blueprint $table) {
            $table->string('recipient_name', 255)->nullable()->after('recipient_phone');
            $table->index('recipient_name', 'reminder_notification_recipient_name_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminder_notification_logs', function (Blueprint $table) {
            $table->dropIndex('reminder_notification_recipient_name_idx');
            $table->dropColumn('recipient_name');
        });
    }
};
