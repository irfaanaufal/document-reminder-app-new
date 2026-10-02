<?php

use App\Models\Application;
use App\Models\DocumentReminder;
use App\Models\DocumentType;
use App\Models\Role;
use App\Models\User;
use App\Models\UserApplication;

function accessSetup(int $level): User
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

function accessMakeReminder(User $owner): DocumentReminder
{
    $type = DocumentType::create([
        'nama_jenis' => 'Sertifikat Access',
        'status' => 'active',
        'created_by' => $owner->id,
        'tipe_form' => 'default',
    ]);

    return DocumentReminder::create([
        'user_id' => $owner->id,
        'nama_dokumen' => 'Dokumen Access Matrix',
        'no_dokumen' => 'ACC/2026/001',
        'jenis_dokumen' => $type->id,
        'pic_nama' => 'PIC Test',
        'pic_email' => 'pic@example.com',
        'penerbit_tujuan' => 'Instansi Test',
        'tanggal_terbit' => now()->subMonths(6)->toDateString(),
        'tanggal_expired' => now()->addMonths(3)->toDateString(),
        'reminder_bulan' => 3,
        'attachment_path' => 'document-reminders/access-test.pdf',
        'attachment_name' => 'access-test.pdf',
    ]);
}

function accessUpdatePayload(DocumentReminder $reminder, string $newName): array
{
    return [
        'nama_dokumen' => $newName,
        'no_dokumen' => $reminder->no_dokumen,
        'jenis_dokumen' => (int) $reminder->jenis_dokumen,
        'penerbit_tujuan' => $reminder->penerbit_tujuan,
        'tanggal_terbit' => $reminder->tanggal_terbit?->toDateString(),
        'tanggal_expired' => $reminder->tanggal_expired?->toDateString(),
        'reminder_bulan' => $reminder->reminder_bulan,
    ];
}

test('level 5 can view all documents including other owners', function () {
    $owner = accessSetup(5);
    $other = accessSetup(8);
    accessMakeReminder($owner);

    $response = $this->actingAs($other)->get('/dokumen');

    $response->assertOk();
    $response->assertSee('Dokumen Access Matrix');
});

test('level 5 can show other owner document detail', function () {
    $owner = accessSetup(5);
    $viewer = accessSetup(8);
    $reminder = accessMakeReminder($owner);

    $this->actingAs($viewer)
        ->get(route('doc.show', $reminder))
        ->assertOk();
});

test('level 5 cannot update other owner document', function () {
    $owner = accessSetup(5);
    $other = accessSetup(8);
    $reminder = accessMakeReminder($owner);

    $this->actingAs($other)
        ->patch(route('doc.update', $reminder), accessUpdatePayload($reminder, 'Hack'))
        ->assertForbidden();
});

test('level 5 can update own document', function () {
    $owner = accessSetup(5);
    $reminder = accessMakeReminder($owner);

    $this->actingAs($owner)
        ->patch(route('doc.update', $reminder), accessUpdatePayload($reminder, 'Own Edit'))
        ->assertRedirect();
    $this->assertDatabaseHas('document_reminders', [
        'id' => $reminder->id,
        'nama_dokumen' => 'Own Edit',
    ]);
});

test('level 5 cannot access logs', function () {
    $user = accessSetup(5);

    $this->actingAs($user)->get('/logs')->assertForbidden();
});

test('level 5 cannot access doc types', function () {
    $user = accessSetup(5);

    $this->actingAs($user)->get('/doc-type')->assertForbidden();
});

test('level 7 can access logs', function () {
    $user = accessSetup(7);

    $this->actingAs($user)->get('/logs')->assertOk();
});

test('level 7 can update other owner document', function () {
    $owner = accessSetup(5);
    $qa = accessSetup(7);
    $reminder = accessMakeReminder($owner);

    $this->actingAs($qa)
        ->patch(route('doc.update', $reminder), accessUpdatePayload($reminder, 'QA Edit'))
        ->assertRedirect();
    $this->assertDatabaseHas('document_reminders', [
        'id' => $reminder->id,
        'nama_dokumen' => 'QA Edit',
    ]);
});

test('level 7 can access doc types', function () {
    $user = accessSetup(7);

    $this->actingAs($user)->get('/doc-type')->assertOk();
});

test('level 4 can manage all documents logs and doc types', function () {
    $owner = accessSetup(5);
    $hrd = accessSetup(4);
    $reminder = accessMakeReminder($owner);

    $this->actingAs($hrd)->get('/logs')->assertOk();
    $this->actingAs($hrd)->get('/doc-type')->assertOk();
    $this->actingAs($hrd)
        ->patch(route('doc.update', $reminder), accessUpdatePayload($reminder, 'HRD Edit'))
        ->assertRedirect();
    $this->assertDatabaseHas('document_reminders', [
        'id' => $reminder->id,
        'nama_dokumen' => 'HRD Edit',
    ]);
});

test('level 10 can access dashboard only', function () {
    $user = accessSetup(10);

    $this->actingAs($user)->get('/dashboard')->assertOk();
    $this->actingAs($user)->get('/dokumen')->assertForbidden();
    $this->actingAs($user)->get('/logs')->assertForbidden();
    $this->actingAs($user)->get('/doc-type')->assertForbidden();
    $this->actingAs($user)->get('/api/notifications')->assertNotFound();
});

test('level 10 dashboard does not link to documents', function () {
    $user = accessSetup(10);

    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertOk();
    $response->assertDontSee('/dokumen?', false);
});

test('level 8 cannot update other owner document', function () {
    $owner = accessSetup(5);
    $qc = accessSetup(8);
    $reminder = accessMakeReminder($owner);

    $this->actingAs($qc)
        ->patch(route('doc.update', $reminder), accessUpdatePayload($reminder, 'Nope'))
        ->assertForbidden();
});

test('policy view allows any app user', function () {
    $owner = accessSetup(5);
    $viewer = accessSetup(9);
    $reminder = accessMakeReminder($owner);

    expect($viewer->can('view', $reminder))->toBeTrue();
});

test('policy update requires manage all or owner', function () {
    $owner = accessSetup(5);
    $other = accessSetup(8);
    $qa = accessSetup(7);
    $reminder = accessMakeReminder($owner);

    expect($owner->can('update', $reminder))->toBeTrue();
    expect($qa->can('update', $reminder))->toBeTrue();
    expect($other->can('update', $reminder))->toBeFalse();
});
