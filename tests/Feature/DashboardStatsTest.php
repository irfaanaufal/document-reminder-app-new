<?php

use App\Models\Application;
use App\Models\DocumentReminder;
use App\Models\DocumentType;
use App\Models\Role;
use App\Models\User;
use App\Models\UserApplication;

function dashStatsSetup(int $level = 5): User
{
    $role = Role::firstOrCreate(
        ['id' => $level],
        ['name' => 'Level ' . $level, 'level' => $level]
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

function dashStatsType(User $owner, string $nama): DocumentType
{
    return DocumentType::firstOrCreate(
        ['nama_jenis' => $nama],
        ['status' => 'active', 'created_by' => $owner->id, 'tipe_form' => 'default']
    );
}

function dashStatsDoc(User $owner, DocumentType $type, array $overrides = []): DocumentReminder
{
    return DocumentReminder::create(array_merge([
        'user_id' => $owner->id,
        'nama_dokumen' => 'Dokumen Stats',
        'no_dokumen' => 'ST/2026/' . uniqid(),
        'jenis_dokumen' => $type->id,
        'pic_nama' => 'PIC Test',
        'pic_email' => 'pic@example.com',
        'penerbit_tujuan' => 'Instansi Test',
        'tanggal_terbit' => now()->subMonths(6)->toDateString(),
        'tanggal_expired' => now()->addMonths(3)->toDateString(),
        'reminder_bulan' => 3,
        'attachment_path' => 'document-reminders/stats-test.pdf',
        'attachment_name' => 'stats-test.pdf',
    ], $overrides));
}

function dashStatsSeedFixtures(User $owner): array
{
    $typeSertifikat = dashStatsType($owner, 'Sertifikat Halal');
    $typeWajib = dashStatsType($owner, 'Wajib Lapor Tahunan');
    $typeLain = dashStatsType($owner, 'Dokumen Lain');

    $expired = dashStatsDoc($owner, $typeSertifikat, [
        'nama_dokumen' => 'Sertifikat Expired',
        'tanggal_expired' => now()->subDay()->toDateString(),
    ]);
    $soon = dashStatsDoc($owner, $typeSertifikat, [
        'nama_dokumen' => 'Sertifikat Akan Expired',
        'tanggal_expired' => now()->addDays(3)->toDateString(),
    ]);
    $wajib = dashStatsDoc($owner, $typeWajib, [
        'nama_dokumen' => 'SPT Tahunan',
        'tanggal_expired' => now()->addDays(10)->toDateString(),
    ]);
    $lifetime = dashStatsDoc($owner, $typeLain, [
        'nama_dokumen' => 'Dokumen Lifetime',
        'tanggal_expired' => null,
        'reminder_bulan' => null,
    ]);
    $far = dashStatsDoc($owner, $typeLain, [
        'nama_dokumen' => 'Dokumen Jauh',
        'tanggal_expired' => now()->addYear()->toDateString(),
    ]);

    return compact('expired', 'soon', 'wajib', 'lifetime', 'far');
}

test('dashboard card totals match SQL aggregates', function () {
    $user = dashStatsSetup(5);
    dashStatsSeedFixtures($user);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertViewHas('totalDocuments', 5);
    $response->assertViewHas('totalExpired', 1);
    $response->assertViewHas('totalSertifikat', 2);
    $response->assertViewHas('totalWajibLapor', 1);
});

test('totalExpired ignores lifetime null tanggal_expired', function () {
    $user = dashStatsSetup(5);
    $type = dashStatsType($user, 'Dokumen Lain');
    dashStatsDoc($user, $type, ['tanggal_expired' => null, 'reminder_bulan' => null]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertViewHas('totalExpired', 0);
});

test('expiringSoon excludes expired and lifetime, sorted ascending max 9', function () {
    $user = dashStatsSetup(5);
    $type = dashStatsType($user, 'Sertifikat Base');
    dashStatsDoc($user, $type, [
        'nama_dokumen' => 'Expired Baru',
        'tanggal_expired' => now()->subDay()->toDateString(),
    ]);
    $b = dashStatsDoc($user, $type, [
        'nama_dokumen' => 'Sooner B',
        'tanggal_expired' => now()->addDays(5)->toDateString(),
    ]);
    $a = dashStatsDoc($user, $type, [
        'nama_dokumen' => 'Sooner A',
        'tanggal_expired' => now()->addDays(2)->toDateString(),
    ]);
    for ($i = 0; $i < 10; $i++) {
        dashStatsDoc($user, $type, [
            'nama_dokumen' => 'Bulk ' . $i,
            'tanggal_expired' => now()->addDays(20 + $i)->toDateString(),
        ]);
    }
    dashStatsDoc($user, $type, [
        'nama_dokumen' => 'Lifetime X',
        'tanggal_expired' => null,
        'reminder_bulan' => null,
    ]);

    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertOk();

    $soon = collect($response->viewData('expiringSoon'));
    expect($soon)->toHaveCount(9);
    expect($soon->pluck('nama_dokumen')->all())->not->toContain('Expired Baru', 'Lifetime X');
    expect($soon->first()['id'])->toBe($a->id);
    expect($soon->pluck('id')->all())->toContain($b->id);
    $days = $soon->pluck('days_left')->all();
    expect($days)->toBe(collect($days)->sort()->values()->all());
});

test('dashboard renders for active level 10 user', function () {
    $user = dashStatsSetup(10);

    $this->actingAs($user)->get('/dashboard')->assertOk();
});
