<?php

namespace App\Http\Controllers;

use App\Services\ChatGPTService;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    protected $chatGPTService;

    public function __construct(ChatGPTService $chatGPTService)
    {
        $this->chatGPTService = $chatGPTService;
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        if (!$user || !$user->canUseChatbot()) {
            abort(403, 'Anda tidak memiliki hak akses ke chatbot.');
        }

        $messages = [];
        $session = ChatSession::where('user_id', $user->id)->first();
        if ($session) {
            $messages = $session->messages()->orderBy('created_at', 'asc')->get();
        }

        return view('chatbot.index', compact('messages'));
    }

    public function sendMessage(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->canUseChatbot()) {
            abort(403, 'Anda tidak memiliki hak akses ke chatbot.');
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');
        
        // 1. Build System Prompt (authenticated & authorized user)
        $systemPrompt = $this->chatGPTService->buildAuthenticatedPromptWithAccess();

        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ]
        ];

        // 2. Simpan pesan baru dan ambil riwayat obrolan (Sliding Window: 8 pesan terakhir)
        // Sesi obrolan unik per user
        $session = ChatSession::firstOrCreate(
            ['user_id' => $user->id],
            ['title' => 'Percakapan Dora']
        );

        // Simpan pesan user
        $session->messages()->create([
            'sender' => 'user',
            'message' => $userMessage,
        ]);

        // Ambil 8 pesan terakhir untuk dikirim sebagai konteks obrolan
        $historyMessages = $session->messages()
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get()
            ->reverse();

        foreach ($historyMessages as $msg) {
            $messages[] = [
                'role' => $msg->sender === 'user' ? 'user' : 'assistant',
                'content' => $msg->message
            ];
        }

        // 3. Kirim ke ChatGPT API
        $response = $this->chatGPTService->sendChatRequest($messages);

        // 4. Simpan respon bot ke database
        $session->messages()->create([
            'sender' => 'bot',
            'message' => $response,
        ]);

        return response()->json([
            'success' => true,
            'message' => $response,
        ]);
    }
}
