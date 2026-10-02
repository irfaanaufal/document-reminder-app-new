<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        DB::table('roles')->updateOrInsert(
            ['id' => 10],
            ['name' => 'Users Baru', 'level' => 10, 'created_at' => now(), 'updated_at' => now()]
        );

        if (Schema::hasTable('users')) {
            DB::table('users')->whereNull('role_id')->update(['role_id' => 10]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasTable('roles')) {
            return;
        }

        DB::table('users')->where('role_id', 10)->update(['role_id' => null]);
        DB::table('roles')->where('id', 10)->delete();
    }
};
