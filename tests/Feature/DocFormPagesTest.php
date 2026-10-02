<?php

use App\Models\Application;
use App\Models\DocumentReminder;
use App\Models\DocumentType;
use App\Models\Role;
use App\Models\User;
use App\Models\UserApplication;

function docFormSetup(int $level = 5): User
{
    $role = Role::firstOrCreate(
        ['id' => $level],
        ['name' => 'Level '.$level, 'level' => $level]
    );
    $user = User::factory()->create(['no_telpon' => '08123456789']);
    $user->forceFill(['role_id' => $role->id])->save();

    $app = Application::firstOrCreate(
        ['slug' => 'reminder'],
        ['name' => 'Reminder', 'description' => 'Sistem pengingat dokumen.']
    );
    UserApplication::firstOrCreate(
        ['user_id' => $user->id, 'application_id' => $app->id],
        ['is_active' => true]
    );

    return $user->fresh();
}

function docFormMakeReminder(User $owner): DocumentReminder
{
    $type = DocumentType::create([
        'nama_jenis' => 'Sertifikat DocForm',
        'status' => 'active',
        'created_by' => $owner->id,
        'tipe_form' => 'default',
    ]);

    return DocumentReminder::create([
        'user_id' => $owner->id,
        'nama_dokumen' => 'Dokumen DocForm Render',
        'no_dokumen' => 'DF/2026/001',
        'jenis_dokumen' => $type->id,
        'pic_nama' => 'PIC Test',
        'pic_email' => 'pic@example.com',
        'penerbit_tujuan' => 'Instansi Test',
        'tanggal_terbit' => now()->subMonths(6)->toDateString(),
        'tanggal_expired' => now()->addMonths(3)->toDateString(),
        'reminder_bulan' => 3,
        'attachment_path' => 'document-reminders/docform-test.pdf',
        'attachment_name' => 'docform-test.pdf',
    ]);
}

test('GET create document form renders without parse error', function () {
    $user = docFormSetup(5);

    $this->actingAs($user)
        ->get('/dokumen/create')
        ->assertOk()
        ->assertSee('let selectedUsers =', false);
});

test('GET edit document form renders without parse error', function () {
    $owner = docFormSetup(5);
    $reminder = docFormMakeReminder($owner);
    $reminder->internalPics()->attach(
        User::factory()->create()->id,
        ['nama' => 'PIC Internal', 'email' => 'internal@example.com']
    );

    $this->actingAs($owner)
        ->get(route('doc.edit', $reminder))
        ->assertOk()
        ->assertSee('let selectedUsers =', false);
});

test('GET edit document form renders when redirected with old input', function () {
    $owner = docFormSetup(5);
    $pic = User::factory()->create();
    $reminder = docFormMakeReminder($owner);

    $this->actingAs($owner)
        ->withSession(['_old_input' => ['pic_internal_user_ids' => [$pic->id]]])
        ->get(route('doc.edit', $reminder))
        ->assertOk();
});
