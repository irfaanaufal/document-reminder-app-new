<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function index(): View
    {
        return view('chatbot.index');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');
        $user = $request->user();
        $prompt = '';

        if (!$user) {
            // User belum login
            $prompt = $this->geminiService->buildGeneralPrompt($userMessage);
        } else {
            // User sudah login
            if ($user->canUseChatbot()) {
                $prompt = $this->geminiService->buildAuthenticatedPromptWithAccess($userMessage);
            } else {
                $prompt = $this->geminiService->buildAuthenticatedPromptWithoutAccess($userMessage);
            }
        }

        $response = $this->geminiService->sendToGemini($prompt);

        return response()->json([
            'success' => true,
            'message' => $response,
        ]);
    }
}
