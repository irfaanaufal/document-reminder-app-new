<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
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
    ]);
});
