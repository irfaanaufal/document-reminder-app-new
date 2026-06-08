<?php

use App\Models\User;
use App\Models\DocumentReminder;
use Illuminate\Support\Facades\Http;

test('guest can access chatbot index page', function () {
    $response = $this->get('/chatbot');
    $response->assertStatus(200);
});

test('guest sendMessage returns response with general prompt', function () {
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'Halo! Ini adalah jawaban umum.']
                        ]
                    ]
                ]
            ]
        ], 200)
    ]);

    $response = $this->postJson('/chatbot/send', [
        'message' => 'Bagaimana cara menggunakan aplikasi?'
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Halo! Ini adalah jawaban umum.'
        ]);
});

test('user without chatbot permission receives without-access response', function () {
    $user = User::factory()->create([
        'can_use_chatbot' => false
    ]);

    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'Anda tidak memiliki izin untuk mengakses data dokumen.']
                        ]
                    ]
                ]
            ]
        ], 200)
    ]);

    $response = $this->actingAs($user)->postJson('/chatbot/send', [
        'message' => 'Berapa banyak dokumen saya?'
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Anda tidak memiliki izin untuk mengakses data dokumen.'
        ]);
});

test('user with chatbot permission receives data context response', function () {
    $user = User::factory()->create([
        'can_use_chatbot' => true
    ]);

    // Create a dummy document reminder
    DocumentReminder::create([
        'user_id' => $user->id,
        'nama_dokumen' => 'Dokumen Test Penting',
        'no_dokumen' => '123/TEST/2026',
        'jenis_dokumen' => 'sertifikat',
        'pic_nama' => 'John Doe',
        'pic_telpon' => '08123456789',
        'penerbit_tujuan' => 'Instansi Test',
        'tanggal_terbit' => '2026-01-01',
        'tanggal_expired' => '2026-12-31',
        'reminder_bulan' => 3,
        'attachment_path' => 'test.pdf',
        'attachment_name' => 'test.pdf',
    ]);

    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'Ditemukan dokumen: Dokumen Test Penting.']
                        ]
                    ]
                ]
            ]
        ], 200)
    ]);

    $response = $this->actingAs($user)->postJson('/chatbot/send', [
        'message' => 'Tampilkan data dokumen saya'
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Ditemukan dokumen: Dokumen Test Penting.'
        ]);
});
