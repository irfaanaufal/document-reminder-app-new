<?php

use App\Models\Application;
use App\Models\DocumentReminder;
use App\Models\DocumentType;
use App\Models\Role;
use App\Models\User;
use App\Models\UserApplication;

function sortTestSetup(int $level = 5): User
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

function sortTestMakeReminder(User $owner, string $noDokumen, ?string $tanggalExpired): DocumentReminder
{
    $type = DocumentType::firstOrCreate(
        ['nama_jenis' => 'Sertifikat SortTest'],
        ['status' => 'active', 'created_by' => $owner->id, 'tipe_form' => 'default']
    );

    return DocumentReminder::create([
        'user_id' => $owner->id,
        'nama_dokumen' => 'Dokumen SortTest '.$noDokumen,
        'no_dokumen' => $noDokumen,
        'jenis_dokumen' => $type->id,
        'pic_nama' => 'PIC SortTest',
        'pic_email' => 'pic-sort@example.com',
        'penerbit_tujuan' => 'Instansi SortTest',
        'tanggal_terbit' => now()->subMonths(6)->toDateString(),
        'tanggal_expired' => $tanggalExpired,
        'reminder_bulan' => $tanggalExpired !== null ? 3 : null,
        'attachment_path' => 'document-reminders/sorttest.pdf',
        'attachment_name' => 'sorttest.pdf',
    ]);
}

test('/dokumen lists most urgent expiry first and null expiry last', function () {
    $user = sortTestSetup(5);

    sortTestMakeReminder($user, 'SORT/NONE/004', null);
    sortTestMakeReminder($user, 'SORT/FAR/003', now()->addMonths(6)->toDateString());
    sortTestMakeReminder($user, 'SORT/NEAR/002', now()->addDays(7)->toDateString());
    sortTestMakeReminder($user, 'SORT/PAST/001', now()->subDay()->toDateString());

    $this->actingAs($user)
        ->get('/dokumen?jenis=semua')
        ->assertOk()
        ->assertSeeInOrder([
            'SORT/PAST/001',
            'SORT/NEAR/002',
            'SORT/FAR/003',
            'SORT/NONE/004',
        ]);
});

test('/dokumen places newly created near-expiry document above older far-expiry documents', function () {
    $user = sortTestSetup(5);

    sortTestMakeReminder($user, 'SORT/FAR/101', now()->addMonths(6)->toDateString());
    sortTestMakeReminder($user, 'SORT/NEAR/102', now()->addDays(7)->toDateString());

    $this->actingAs($user)
        ->get('/dokumen?jenis=semua')
        ->assertOk()
        ->assertSeeInOrder([
            'SORT/NEAR/102',
            'SORT/FAR/101',
        ]);
});
