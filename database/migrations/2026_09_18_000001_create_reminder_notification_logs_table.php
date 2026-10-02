<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('reminder_notification_logs');

        Schema::create('reminder_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_reminder_id')->constrained()->cascadeOnDelete();
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->date('scheduled_for');
            $table->string('reminder_rule', 20)->nullable();
            $table->string('status', 20)->default('pending');
            $table->unsignedSmallInteger('attempt_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->longText('provider_response')->nullable();
            $table->timestamps();

            $table->unique(['document_reminder_id', 'recipient_email', 'scheduled_for'], 'reminder_notif_unique');
            $table->index(['status', 'scheduled_for'], 'reminder_notif_status_sched_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_notification_logs');
    }
};
