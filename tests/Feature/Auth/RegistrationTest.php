<?php

use App\Models\Karyawan;
use App\Models\Role;
use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    // Penerima notifikasi: pemegang hak akses Kelola Permintaan (level 1).
    $itRole = Role::firstOrCreate(['name' => 'IT'], ['level' => 1]);
    $admin = User::factory()->create(['role_id' => $itRole->id]);

    Karyawan::create([
        'fid' => 'FID-TEST-001',
        'nama_karyawan' => 'Test User',
        'divisi' => 'IT',
        'jabatan' => 'Staff',
        'status' => 'aktif',
    ]);

    $response = $this->post('/register', [
        'fid' => 'FID-TEST-001',
        'nama' => 'Test User',
        'username' => 'testuser',
        'email' => 'test@example.com',
        'no_telpon' => '081234567890',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status', 'Registrasi berhasil. Silakan hubungi admin untuk aktivasi akun.');

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'role_id' => 10,
    ]);

    // Aturan seragam: 1 baris permintaan akses (inactive) + notifikasi
    // "Permintaan akses baru" ke level 1,2,3,4,7.
    $newUser = User::where('email', 'test@example.com')->firstOrFail();
    $this->assertTrue(
        $newUser->userApplications()
            ->where('is_active', false)
            ->whereHas('application', fn ($q) => $q->where('slug', 'reminder'))
            ->exists()
    );

    $this->assertDatabaseHas('log_notifikasi', [
        'actor_user_id' => $newUser->id,
        'user_id' => $admin->id,
        'action' => 'new_access_request',
        'title' => 'Permintaan akses baru',
    ]);
});
