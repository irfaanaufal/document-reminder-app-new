<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('log_notifikasi')) {
            return;
        }

        Schema::create('log_notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('ticket_id')->nullable();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name')->nullable();
            $table->string('recipient_type', 20);
            $table->string('action', 40)->nullable();
            $table->string('title');
            $table->text('message');
            $table->string('status')->nullable();
            $table->boolean('visible_in_bell')->default(true);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        // Tabel shared dengan MySQL produksi — jangan drop di environment yang sudah punya data.
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::dropIfExists('log_notifikasi');
        }
    }
};
