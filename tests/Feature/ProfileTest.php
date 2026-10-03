<?php

use App\Models\User;
use App\Models\Application;
use App\Models\UserApplication;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'nama' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->nama);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('username and phone are read-only and cannot be updated via profile form', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'nama' => 'Test User',
            'email' => $user->email,
            'username' => 'hackedname',
            'no_telpon' => '089999999999',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertNotSame('hackedname', $user->username);
    $this->assertNotSame('089999999999', $user->no_telpon);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'nama' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('delete profile route no longer exists', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response->assertStatus(405);
    $this->assertNotNull($user->fresh());
});

test('user can deactivate own application access', function () {
    $user = User::factory()->create();
    $app = Application::firstOrCreate(
        ['slug' => 'reminder'],
        ['name' => 'Reminder', 'description' => 'Sistem pengingat dokumen.']
    );
    $userApp = UserApplication::firstOrCreate(
        ['user_id' => $user->id, 'application_id' => $app->id],
        ['is_active' => true, 'approved_by' => $user->id, 'approved_at' => now()]
    );

    $response = $this
        ->actingAs($user)
        ->patch('/profile/access');

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $userApp->refresh();
    $this->assertFalse($userApp->is_active);
    $this->assertSame($user->id, $userApp->approved_by);
    $this->assertNotNull($userApp->approved_at);
});

test('user cannot reactivate own application access while logged in', function () {
    $user = User::factory()->create();
    $app = Application::firstOrCreate(
        ['slug' => 'reminder'],
        ['name' => 'Reminder', 'description' => 'Sistem pengingat dokumen.']
    );
    $userApp = UserApplication::firstOrCreate(
        ['user_id' => $user->id, 'application_id' => $app->id],
        ['is_active' => false]
    );

    $this->actingAs($user)
        ->patch('/profile/access')
        ->assertRedirect('/profile')
        ->assertSessionHasErrors('access');

    $this->assertFalse($userApp->fresh()->is_active);
});

test('toggle access creates pending row without activating', function () {
    $user = User::factory()->create();
    $app = Application::firstOrCreate(
        ['slug' => 'reminder'],
        ['name' => 'Reminder', 'description' => 'Sistem pengingat dokumen.']
    );

    $this->actingAs($user)
        ->patch('/profile/access')
        ->assertRedirect('/profile')
        ->assertSessionHasErrors('access');

    $userApp = UserApplication::where('user_id', $user->id)
        ->where('application_id', $app->id)
        ->first();

    $this->assertNotNull($userApp);
    $this->assertFalse($userApp->is_active);
});
