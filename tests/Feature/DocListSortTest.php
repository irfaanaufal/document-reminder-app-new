<?php

use App\Models\Application;
use App\Models\DocumentReminder;
use App\Models\DocumentType;
use App\Models\Role;
use App\Models\User;
use App\Models\UserApplication;
use PHPUnit\Framework\Assert;

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
        'nama_dokumen' => 'Dokumen SortTest',
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

/**
 * Assertion berbasis posisi kemunculan PERTAMA tiap marker di HTML.
 * Loop kartu mobile dirender sebelum loop tabel, jadi kemunculan pertama
 * = urutan sebenarnya dari $reminders. Kebal terhadap render ganda yang
 * membuat assertSeeInOrder bisa lolos meski urutan salah.
 */
function assertFirstOccurrenceOrder(string $html, array $markers): void
{
    $positions = [];

    foreach ($markers as $marker) {
        $pos = strpos($html, $marker);
        Assert::assertNotFalse($pos, "Marker [{$marker}] tidak ditemukan pada response.");
        $positions[] = $pos;
    }

    $sorted = $positions;
    sort($sorted);

    Assert::assertSame(
        $sorted,
        $positions,
        'Urutan dokumen tidak sesuai ekspektasi: '.implode(' lalu ', $markers)
    );
}

test('/dokumen lists most urgent expiry first and null expiry last', function () {
    $user = sortTestSetup(5);

    sortTestMakeReminder($user, 'SORT/NONE/004', null);
    sortTestMakeReminder($user, 'SORT/FAR/003', now()->addMonths(6)->toDateString());
    sortTestMakeReminder($user, 'SORT/NEAR/002', now()->addDays(7)->toDateString());
    sortTestMakeReminder($user, 'SORT/PAST/001', now()->subDay()->toDateString());

    $response = $this->actingAs($user)
        ->get('/dokumen?jenis=semua')
        ->assertOk();

    assertFirstOccurrenceOrder($response->getContent(), [
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

    $response = $this->actingAs($user)
        ->get('/dokumen?jenis=semua')
        ->assertOk();

    assertFirstOccurrenceOrder($response->getContent(), [
        'SORT/NEAR/102',
        'SORT/FAR/101',
    ]);
});

test('/dokumen sorts strictly by expiry even when created in scrambled order', function () {
    $user = sortTestSetup(5);

    sortTestMakeReminder($user, 'SORT/A/401', now()->addDays(40)->toDateString());
    sortTestMakeReminder($user, 'SORT/A/402', now()->addDays(10)->toDateString());
    sortTestMakeReminder($user, 'SORT/A/403', now()->addDays(30)->toDateString());
    sortTestMakeReminder($user, 'SORT/A/404', now()->addDays(20)->toDateString());
    sortTestMakeReminder($user, 'SORT/A/405', now()->addDays(50)->toDateString());

    $response = $this->actingAs($user)
        ->get('/dokumen?jenis=semua')
        ->assertOk();

    assertFirstOccurrenceOrder($response->getContent(), [
        'SORT/A/402',
        'SORT/A/404',
        'SORT/A/403',
        'SORT/A/401',
        'SORT/A/405',
    ]);
});

test('/dokumen tie-breaks equal expiry dates with newest document first', function () {
    $user = sortTestSetup(5);

    sortTestMakeReminder($user, 'SORT/TIE/301', now()->addDays(45)->toDateString());
    sortTestMakeReminder($user, 'SORT/TIE/302', now()->addDays(45)->toDateString());

    $response = $this->actingAs($user)
        ->get('/dokumen?jenis=semua')
        ->assertOk();

    assertFirstOccurrenceOrder($response->getContent(), [
        'SORT/TIE/302',
        'SORT/TIE/301',
    ]);
});
