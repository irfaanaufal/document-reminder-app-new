<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'IT', 'level' => 1],
            ['id' => 2, 'name' => 'Direktur Utama', 'level' => 2],
            ['id' => 3, 'name' => 'Head Admin', 'level' => 3],
            ['id' => 4, 'name' => 'HRD', 'level' => 4],
            ['id' => 5, 'name' => 'Admin', 'level' => 5],
            ['id' => 6, 'name' => 'Teknisi', 'level' => 6],
            ['id' => 7, 'name' => 'QA', 'level' => 7],
            ['id' => 8, 'name' => 'QC', 'level' => 8],
            ['id' => 9, 'name' => 'Ekspedisi', 'level' => 9],
            ['id' => 10, 'name' => 'Users Baru', 'level' => 10],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }

        User::updateOrCreate([
            'username' => 'irfaanaufal',
        ], [
            'name' => 'Irfaan',
            'email' => 'irfaanaufal04@gmail.com',
            'password' => Hash::make('password'),
            'no_telpon' => '082353575812',
            'role_id' => 1,
        ]);

        User::updateOrCreate([
            'username' => 'hendi',
        ], [
            'name' => 'Hendi',
            'email' => 'hendi@gmail.com',
            'password' => Hash::make('password'),
            'no_telpon' => '081902588715',
            'role_id' => 1,
        ]);
    }
}
