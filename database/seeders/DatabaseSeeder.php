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
            'username' => 'irfaanaufal',
        ], [
            'nama' => 'Irfaan',
            'email' => 'irfaanaufal04@gmail.com',
            'password' => Hash::make('password'),
            'no_telpon' => '082353575812',
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        User::updateOrCreate([
            'username' => 'hendi',
        ], [
            'nama' => 'Hendi',
            'email' => 'hendi@gmail.com',
            'password' => Hash::make('password'),
            'no_telpon' => '081902588715',
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
    }
}
