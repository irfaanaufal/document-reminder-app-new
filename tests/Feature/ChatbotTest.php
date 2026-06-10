<?php

use App\Models\User;
use App\Models\DocumentReminder;
use Illuminate\Support\Facades\Http;

test('guest is redirected from chatbot index page', function () {
    $response = $this->get('/chatbot');
    $response->assertRedirect('/login');
});

test('guest sendMessage returns unauthorized status', function () {
    $response = $this->postJson('/chatbot/send', [
        'message' => 'Bagaimana cara menggunakan aplikasi?'
    ]);
    $response->assertStatus(401);
});

test('user without chatbot permission is forbidden to access index page', function () {
    $user = User::factory()->create([
        'can_use_chatbot' => false
    ]);

    $response = $this->actingAs($user)->get('/chatbot');
    $response->assertStatus(403);
});

test('user without chatbot permission is forbidden to send message', function () {
    $user = User::factory()->create([
        'can_use_chatbot' => false
    ]);

    $response = $this->actingAs($user)->postJson('/chatbot/send', [
        'message' => 'Berapa banyak dokumen saya?'
    ]);
    $response->assertStatus(403);
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

    config([
        'services.chatgpt.api_key' => 'fake-api-key',
        'services.chatgpt.model' => 'gpt-4o-mini',
        'services.chatgpt.base_url' => 'https://api.openai.com/v1'
    ]);

    Http::fake([
        'api.openai.com/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Ditemukan dokumen: Dokumen Test Penting.'
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

test('user messages are stored in database chat history', function () {
    $user = User::factory()->create([
        'can_use_chatbot' => true
    ]);

    config([
        'services.chatgpt.api_key' => 'fake-api-key',
        'services.chatgpt.model' => 'gpt-4o-mini',
        'services.chatgpt.base_url' => 'https://api.openai.com/v1'
    ]);

    Http::fake([
        'api.openai.com/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Jawaban Dora dari DB.'
                    ]
                ]
            ]
        ], 200)
    ]);

    $response = $this->actingAs($user)->postJson('/chatbot/send', [
        'message' => 'Pertanyaan uji coba DB'
    ]);

    $response->assertStatus(200);

    // Verify session was created
    $this->assertDatabaseHas('chat_sessions', [
        'user_id' => $user->id,
    ]);

    // Verify messages were created
    $this->assertDatabaseHas('chat_messages', [
        'sender' => 'user',
        'message' => 'Pertanyaan uji coba DB',
    ]);

    $this->assertDatabaseHas('chat_messages', [
        'sender' => 'bot',
        'message' => 'Jawaban Dora dari DB.',
    ]);
});
