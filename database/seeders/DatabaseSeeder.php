<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate([
            'email' => 'irfaan@gmail.com',
        ], [
            'nama' => 'Irfaan',
            'password' => Hash::make('password'),
            'no_telpon' => '082353575812',
            'role' => User::ROLE_SUPER_ADMIN,
            'is_active' => true,
        ]);

        User::updateOrCreate([
            'email' => 'hadi@gmail.com',
        ], [
            'nama' => 'Hadi',
            'password' => Hash::make('password'),
            'no_telpon' => '083817422225',
            'role' => User::ROLE_SUPER_ADMIN,
            'is_active' => true,
        ]);

        User::updateOrCreate([
            'email' => 'hendi@gmail.com',
        ], [
            'nama' => 'Hendi',
            'password' => Hash::make('password'),
            'no_telpon' => '081902588715',
            'role' => User::ROLE_SUPER_ADMIN,
            'is_active' => true,
        ]);
        
        User::updateOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'nama' => 'Admin Demo',
            'password' => Hash::make('password'),
            'no_telpon' => '087712733183',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        User::updateOrCreate([
            'email' => 'user@gmail.com',
        ], [
            'nama' => 'User Demo',
            'password' => Hash::make('password'),
            'no_telpon' => '087712733183',
            'role' => User::ROLE_USER,
            'is_active' => true,
        ]);

        $this->call(DocumentReminderSeeder::class);
    }
}
