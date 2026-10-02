<?php

use App\Models\User;
use App\Models\Application;
use App\Models\UserApplication;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();
    $app = Application::firstOrCreate(
        ['slug' => 'reminder'],
        ['name' => 'Reminder', 'description' => 'Sistem pengingat dokumen.']
    );
    UserApplication::create([
        'user_id' => $user->id,
        'application_id' => $app->id,
        'is_active' => true,
    ]);

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'username' => $user->username,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users without active application access can not authenticate', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $this->assertGuest();

    $response->assertSessionHasErrors(['activation_needed' => 'Akun belum diaktifkan. Hubungi tim IT']);
});

test('users with role but without active row are still blocked (no auto-activate)', function () {
    // Perilaku lama: role ≠ 10 → baris dibuat aktif saat login (auto-aktif).
    // Aturan seragam: SEMUA baris baru inactive — aktivasi via Kelola Permintaan.
    $role = \App\Models\Role::firstOrCreate(['name' => 'HRD'], ['level' => 4]);
    $user = User::factory()->create(['role_id' => $role->id]);

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors(['activation_needed' => 'Akun belum diaktifkan. Hubungi tim IT']);

    $this->assertDatabaseHas('user_applications', [
        'user_id' => $user->id,
        'is_active' => false,
    ]);
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
