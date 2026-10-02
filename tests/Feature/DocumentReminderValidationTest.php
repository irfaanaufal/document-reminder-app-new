<?php

use App\Models\Application;
use App\Models\DocumentReminder;
use App\Models\DocumentType;
use App\Models\Role;
use App\Models\User;
use App\Models\UserApplication;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function grantReminderAccess(User $user, int $level = 5): User
{
    $role = Role::firstOrCreate(
        ['id' => $level],
        ['name' => 'Level '.$level, 'level' => $level]
    );
    $user->forceFill(['role_id' => $role->id])->save();

    $appId = (int) config('app.application_id');
    $app = $appId
        ? (Application::find($appId) ?? Application::create([
            'id' => $appId,
            'name' => 'Reminder',
            'slug' => 'reminder',
            'description' => 'Sistem pengingat dokumen.',
        ]))
        : Application::firstOrCreate(
            ['slug' => 'reminder'],
            ['name' => 'Reminder', 'description' => 'Sistem pengingat dokumen.']
        );

    UserApplication::firstOrCreate(
        ['user_id' => $user->id, 'application_id' => $app->id],
        ['is_active' => true]
    );

    return $user->fresh();
}

test('document validation fails when pic_external_telpon is non-numeric', function () {
    $user = grantReminderAccess(User::factory()->create(['no_telpon' => '081234567890']));
    $documentType = DocumentType::create([
        'nama_jenis' => 'Sertifikat Default',
        'status' => 'active',
        'created_by' => $user->id,
        'tipe_form' => 'default',
    ]);

    Storage::fake('public');

    $response = $this->actingAs($user)->post('/document-reminders', [
        'nama_dokumen' => 'Dokumen Test Validation',
        'no_dokumen' => '123/TEST/2026',
        'jenis_dokumen' => $documentType->id,
        'pic_nama' => 'John Doe',
        'pic_email' => 'pic@example.com',
        'pic_external_nama' => 'External PIC',
        'pic_external_telpon' => 'abc12345',
        'penerbit_tujuan' => 'Instansi Test',
        'tanggal_terbit' => '2026-01-01',
        'tanggal_expired' => '2026-12-31',
        'reminder_bulan' => 3,
        'attachment' => UploadedFile::fake()->create('document.pdf', 100),
    ]);

    $response->assertSessionHasErrors(['pic_external_telpon']);
});

test('document validation fails when pic_external_telpon is more than 15 digits', function () {
    $user = grantReminderAccess(User::factory()->create(['no_telpon' => '081234567890']));
    $documentType = DocumentType::create([
        'nama_jenis' => 'Sertifikat Default 2',
        'status' => 'active',
        'created_by' => $user->id,
        'tipe_form' => 'default',
    ]);

    Storage::fake('public');

    $response = $this->actingAs($user)->post('/document-reminders', [
        'nama_dokumen' => 'Dokumen Test Validation',
        'no_dokumen' => '123/TEST/2026',
        'jenis_dokumen' => $documentType->id,
        'pic_nama' => 'John Doe',
        'pic_email' => 'pic@example.com',
        'pic_external_nama' => 'External PIC',
        'pic_external_telpon' => '1234567890123456',
        'penerbit_tujuan' => 'Instansi Test',
        'tanggal_terbit' => '2026-01-01',
        'tanggal_expired' => '2026-12-31',
        'reminder_bulan' => 3,
        'attachment' => UploadedFile::fake()->create('document.pdf', 100),
    ]);

    $response->assertSessionHasErrors(['pic_external_telpon']);
});

test('document validation passes when pic_external_telpon is valid and max 15 digits', function () {
    $user = grantReminderAccess(User::factory()->create(['no_telpon' => '081234567890']));
    $documentType = DocumentType::create([
        'nama_jenis' => 'Sertifikat Default 3',
        'status' => 'active',
        'created_by' => $user->id,
        'tipe_form' => 'default',
    ]);

    Storage::fake('public');

    $response = $this->actingAs($user)->post('/document-reminders', [
        'nama_dokumen' => 'Dokumen Test Validation',
        'no_dokumen' => '123/TEST/2026',
        'jenis_dokumen' => $documentType->id,
        'pic_nama' => 'John Doe',
        'pic_email' => 'pic@example.com',
        'pic_external_nama' => 'External PIC',
        'pic_external_telpon' => '08987654321',
        'penerbit_tujuan' => 'Instansi Test',
        'tanggal_terbit' => '2026-01-01',
        'tanggal_expired' => '2026-12-31',
        'reminder_bulan' => 3,
        'attachment' => UploadedFile::fake()->create('document.pdf', 100),
    ]);

    $response->assertSessionHasNoErrors();
});

test('document validation passes when tanggal_expired and reminder_bulan are null', function () {
    $user = grantReminderAccess(User::factory()->create(['no_telpon' => '081234567890']));
    $documentType = DocumentType::create([
        'nama_jenis' => 'Sertifikat Lifetime',
        'status' => 'active',
        'created_by' => $user->id,
        'tipe_form' => 'default',
    ]);

    Storage::fake('public');

    $response = $this->actingAs($user)->post('/document-reminders', [
        'nama_dokumen' => 'Dokumen Test Lifetime',
        'no_dokumen' => '123/LIFETIME/2026',
        'jenis_dokumen' => $documentType->id,
        'penerbit_tujuan' => 'Instansi Test',
        'tanggal_terbit' => '2026-01-01',
        'tanggal_expired' => null,
        'reminder_bulan' => null,
        'attachment' => UploadedFile::fake()->create('document.pdf', 100),
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('document_reminders', [
        'nama_dokumen' => 'Dokumen Test Lifetime',
        'tanggal_expired' => null,
        'reminder_bulan' => null,
    ]);
});

test('document validation fails when pic_email is invalid', function () {
    $user = grantReminderAccess(User::factory()->create(['no_telpon' => '081234567890']));
    $documentType = DocumentType::create([
        'nama_jenis' => 'Sertifikat Invalid Email',
        'status' => 'active',
        'created_by' => $user->id,
        'tipe_form' => 'default',
    ]);

    Storage::fake('public');

    $response = $this->actingAs($user)->post('/document-reminders', [
        'nama_dokumen' => 'Dokumen Test Invalid Email',
        'no_dokumen' => '123/INVALID/2026',
        'jenis_dokumen' => $documentType->id,
        'pic_nama' => 'John Doe',
        'pic_email' => 'not-an-email',
        'penerbit_tujuan' => 'Instansi Test',
        'tanggal_terbit' => '2026-01-01',
        'tanggal_expired' => '2026-12-31',
        'reminder_bulan' => 3,
        'attachment' => UploadedFile::fake()->create('document.pdf', 100),
    ]);

    $response->assertSessionHasErrors(['pic_email']);
});

test('document validation passes when pic_email is valid or empty', function () {
    $user = grantReminderAccess(User::factory()->create(['no_telpon' => '081234567890']));
    $documentType = DocumentType::create([
        'nama_jenis' => 'Sertifikat Valid Email',
        'status' => 'active',
        'created_by' => $user->id,
        'tipe_form' => 'default',
    ]);

    Storage::fake('public');

    $response = $this->actingAs($user)->post('/document-reminders', [
        'nama_dokumen' => 'Dokumen Test Valid Email',
        'no_dokumen' => '123/VALID/2026',
        'jenis_dokumen' => $documentType->id,
        'pic_nama' => 'John Doe',
        'pic_email' => 'john@example.com',
        'penerbit_tujuan' => 'Instansi Test',
        'tanggal_terbit' => '2026-01-01',
        'tanggal_expired' => '2026-12-31',
        'reminder_bulan' => 3,
        'attachment' => UploadedFile::fake()->create('document.pdf', 100),
    ]);

    $response->assertSessionHasNoErrors();

    $response2 = $this->actingAs($user)->post('/document-reminders', [
        'nama_dokumen' => 'Dokumen Test Empty Email',
        'no_dokumen' => '123/EMPTY/2026',
        'jenis_dokumen' => $documentType->id,
        'pic_nama' => 'John Doe',
        'pic_email' => '',
        'penerbit_tujuan' => 'Instansi Test',
        'tanggal_terbit' => '2026-01-01',
        'tanggal_expired' => '2026-12-31',
        'reminder_bulan' => 3,
        'attachment' => UploadedFile::fake()->create('document.pdf', 100),
    ]);

    $response2->assertSessionHasNoErrors();
});
