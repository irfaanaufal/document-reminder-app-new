<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'no_telpon')) {
                $table->string('no_telpon', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(false)->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'reset_otp')) {
                $table->string('reset_otp', 6)->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'reset_otp_expires_at')) {
                $table->timestamp('reset_otp_expires_at')->nullable()->after('reset_otp');
            }
            if (!Schema::hasColumn('users', 'can_use_chatbot')) {
                $table->boolean('can_use_chatbot')->default(false)->after('reset_otp_expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['no_telpon', 'is_active', 'reset_otp', 'reset_otp_expires_at', 'can_use_chatbot'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
