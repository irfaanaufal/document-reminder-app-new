<?php

use App\Models\User;
use App\Models\DocumentReminder;
use App\Models\DocumentType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('document validation fails when pic_external_telpon is non-numeric', function () {
    $user = User::factory()->create(['no_telpon' => '081234567890']);
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
        'pic_telpon' => '08123456789',
        'pic_external_nama' => 'External PIC',
        'pic_external_telpon' => 'abc12345', // non-numeric characters
        'penerbit_tujuan' => 'Instansi Test',
        'tanggal_terbit' => '2026-01-01',
        'tanggal_expired' => '2026-12-31',
        'reminder_bulan' => 3,
        'attachment' => UploadedFile::fake()->create('document.pdf', 100),
    ]);

    $response->assertSessionHasErrors(['pic_external_telpon']);
});

test('document validation fails when pic_external_telpon is more than 15 digits', function () {
    $user = User::factory()->create(['no_telpon' => '081234567890']);
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
        'pic_telpon' => '08123456789',
        'pic_external_nama' => 'External PIC',
        'pic_external_telpon' => '1234567890123456', // 16 digits
        'penerbit_tujuan' => 'Instansi Test',
        'tanggal_terbit' => '2026-01-01',
        'tanggal_expired' => '2026-12-31',
        'reminder_bulan' => 3,
        'attachment' => UploadedFile::fake()->create('document.pdf', 100),
    ]);

    $response->assertSessionHasErrors(['pic_external_telpon']);
});

test('document validation passes when pic_external_telpon is valid and max 15 digits', function () {
    $user = User::factory()->create(['no_telpon' => '081234567890']);
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
        'pic_telpon' => '08123456789',
        'pic_external_nama' => 'External PIC',
        'pic_external_telpon' => '08987654321', // 11 digits, only numbers
        'penerbit_tujuan' => 'Instansi Test',
        'tanggal_terbit' => '2026-01-01',
        'tanggal_expired' => '2026-12-31',
        'reminder_bulan' => 3,
        'attachment' => UploadedFile::fake()->create('document.pdf', 100),
    ]);

    $response->assertSessionHasNoErrors();
});

test('document validation passes when tanggal_expired and reminder_bulan are null', function () {
    $user = User::factory()->create(['no_telpon' => '081234567890']);
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
