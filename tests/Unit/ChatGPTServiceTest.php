<?php

use App\Services\ChatGPTService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

uses(TestCase::class);

test('sendToChatGPT returns success content on first attempt', function () {
    Http::fake([
        'api.openai.com/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Success response'
                    ]
                ]
            ]
        ], 200)
    ]);

    config([
        'services.chatgpt.api_key' => 'fake-api-key',
        'services.chatgpt.model' => 'gpt-4o-mini',
        'services.chatgpt.base_url' => 'https://api.openai.com/v1'
    ]);

    $service = new ChatGPTService();
    $result = $service->sendToChatGPT('Hello');

    expect($result)->toBe('Success response');
});

test('sendToChatGPT falls back to next model immediately on error', function () {
    Http::fake(function ($request) {
        $body = json_decode($request->body(), true);
        $model = $body['model'] ?? '';

        if ($model === 'gpt-4o-mini') {
            return Http::response(['error' => ['message' => '503 error']], 503);
        }

        if ($model === 'gpt-4o') {
            return Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Success on fallback gpt-4o'
                        ]
                    ]
                ]
            ], 200);
        }

        return Http::response(['error' => ['message' => 'Not found']], 404);
    });

    config([
        'services.chatgpt.api_key' => 'fake-api-key',
        'services.chatgpt.model' => 'gpt-4o-mini',
        'services.chatgpt.base_url' => 'https://api.openai.com/v1'
    ]);

    Log::shouldReceive('warning')->once(); // Should log once for the gpt-4o-mini fallback

    $service = new ChatGPTService();
    $result = $service->sendToChatGPT('Hello');

    expect($result)->toBe('Success on fallback gpt-4o');
});

test('sendToChatGPT returns final error if all models fail', function () {
    Http::fake([
        'api.openai.com/v1/*' => Http::response(['error' => ['message' => '503 error']], 503)
    ]);

    config([
        'services.chatgpt.api_key' => 'fake-api-key',
        'services.chatgpt.model' => 'gpt-4o-mini',
        'services.chatgpt.base_url' => 'https://api.openai.com/v1'
    ]);

    Log::shouldReceive('warning')->times(3); // 3 unique models (gpt-4o-mini, gpt-4o, gpt-3.5-turbo), 1 attempt each

    $service = new ChatGPTService();
    $result = $service->sendToChatGPT('Hello');

    expect($result)->toContain('Maaf, terjadi kesalahan saat menghubungi ChatGPT API')
        ->toContain('Status: 503');
});
