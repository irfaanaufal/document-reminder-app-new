<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    $user = User::factory()->create();

    $response = $this->post('/forgot-password', ['email' => $user->email]);

    $response->assertRedirect(route('password.otp.show'));
    $response->assertSessionHas('reset_password_email', $user->email);
    
    $user->refresh();
    $this->assertNotNull($user->reset_otp);
    $this->assertNotNull($user->reset_otp_expires_at);
});

test('reset password screen can be rendered after OTP verification', function () {
    $user = User::factory()->create();
    $otp = '123456';
    $user->update([
        'reset_otp' => $otp,
        'reset_otp_expires_at' => now()->addMinutes(15),
    ]);

    // Simulating session values
    $response = $this->withSession(['otp_verified_email' => $user->email])
        ->get('/reset-password/otp?email='.$user->email);

    $response->assertStatus(200);
});

test('password can be reset with valid OTP verified session', function () {
    $user = User::factory()->create();

    $response = $this->withSession(['otp_verified_email' => $user->email])
        ->post('/reset-password', [
            'token' => 'otp',
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('success', 'Kata sandi Anda berhasil diubah. Silakan masuk.');

    $user->refresh();
    $this->assertTrue(Hash::check('newpassword', $user->password));
});
