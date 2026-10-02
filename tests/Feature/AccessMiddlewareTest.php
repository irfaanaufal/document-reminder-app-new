<?php

use App\Models\Application;
use App\Models\User;
use App\Models\UserApplication;

test('inactive access mid-session forces logout and login error', function () {
    $user = User::factory()->create();
    $app = Application::firstOrCreate(
        ['slug' => 'reminder'],
        ['name' => 'Reminder', 'description' => 'Sistem pengingat dokumen.']
    );
    UserApplication::create([
        'user_id' => $user->id,
        'application_id' => $app->id,
        'is_active' => false,
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $this->assertGuest();
    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('activation_needed');
});

test('active access can access dashboard', function () {
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

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $this->assertAuthenticated();
});
