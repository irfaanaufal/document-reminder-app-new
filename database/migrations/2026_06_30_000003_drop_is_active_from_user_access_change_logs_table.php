<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('user_access_change_logs', 'old_is_active')) {
            Schema::table('user_access_change_logs', function (Blueprint $table) {
                $table->dropColumn(['old_is_active', 'new_is_active']);
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('user_access_change_logs', 'old_is_active')) {
            Schema::table('user_access_change_logs', function (Blueprint $table) {
                $table->boolean('old_is_active')->nullable()->after('new_role');
                $table->boolean('new_is_active')->nullable()->after('old_is_active');
            });
        }
    }
};
