<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create(['is_active' => true]);

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->post('/login', [
        'username' => $user->username,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('inactive users can not authenticate', function () {
    $user = User::factory()->create(['is_active' => false]);

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors(['username' => 'Akun Anda perlu diaktifkan. Silahkan hubungi Team IT untuk diaktifkan.']);
});

test('users can logout', function () {
    $user = User::factory()->create(['is_active' => true]);

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
