<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Chatbot - {{ config('app.name', 'Laravel') }}</title>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.4);
            border-radius: 9999px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.6);
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 antialiased selection:bg-blue-500 selection:text-white relative flex flex-col overflow-hidden">

    {{-- Background Image --}}
    <div
        class="absolute inset-0 bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('images/background.jpg') }}');"
    ></div>

    {{-- Dark Overlay --}}
    <div class="absolute inset-0 bg-black/45 transition-colors duration-300"></div>

    {{-- Additional Soft Gradient --}}
    <div class="absolute inset-0 bg-gradient-to-b from-black/10 via-black/35 to-black/60 transition-colors duration-300"></div>

    {{-- Header (Navbar - Hanya Tombol Masuk / Dashboard) --}}
    <header class="relative z-10 flex shrink-0 items-center justify-end gap-3 px-5 py-5 sm:px-8">
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}"
                   class="inline-flex h-10 items-center rounded-full bg-white px-5 text-sm font-semibold text-slate-950 shadow-lg transition hover:bg-slate-100">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex h-10 items-center rounded-full bg-white px-5 text-sm font-semibold text-slate-950 shadow-lg transition hover:bg-slate-100">
                    Masuk
                </a>
            @endauth
        @endif
    </header>

    {{-- Main Section split between Hero & Chat Widget --}}
    <main class="relative z-10 flex flex-1 flex-col lg:flex-row items-center justify-between px-6 md:px-16 lg:px-24">
        
        {{-- Left: Hero Section --}}
        <div class="flex flex-col items-center lg:items-start text-center lg:text-left justify-center flex-1 max-w-2xl lg:pr-8 py-8">
            <h1 class="text-[clamp(2.8rem,10vw,6.5rem)] font-black leading-none tracking-[-0.06em] text-white drop-shadow-[0_10px_30px_rgba(0,0,0,0.8)]">
                <span id="welcome-typing-text">Reminder</span>
                <span
                    class="ml-0.5 inline-block animate-pulse align-baseline text-yellow-300"
                    aria-hidden="true"
                >|</span>
            </h1>

            <p class="mx-auto lg:mx-0 mt-4 max-w-2xl text-sm leading-6 text-white/95 sm:text-lg sm:leading-7 drop-shadow-[0_2px_10px_rgba(0,0,0,0.5)]">
                Sistem pengingat dokumen yang dibuat lebih rapi, profesional,
                dan mudah dipantau untuk membantu Anda menjaga setiap masa berlaku
                tetap terkendali.
            </p>
        </div>

        {{-- Right: Chatbot Window --}}
        <div class="w-full max-w-md bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden flex flex-col h-[520px] md:h-[560px] mb-8 lg:mb-0 lg:ml-auto">
            <!-- Chat Window Header -->
            <div class="bg-[#5f6063] px-5 py-4 flex items-center justify-between text-white select-none">
                <div class="flex items-center gap-3">
                    <!-- Profile Picture -->
                    <div class="relative w-11 h-11 bg-slate-500 rounded-full border-2 border-white/20 flex items-center justify-center shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="white" class="bi bi-robot" viewBox="0 0 16 16">
                            <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z"/>
                            <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z"/>
                        </svg>
                        <!-- Status indicator dot -->
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-[#5f6063] rounded-full"></span>
                    </div>
                    <div>
                        <h2 class="font-bold text-base leading-tight">Minbot</h2>
                        <span class="text-[11px] text-white/80 flex items-center gap-1 select-none">
                            <span class="w-1.5 h-1.5 bg-[#22c55e] rounded-full inline-block animate-pulse"></span>
                            Online Now
                        </span>
                    </div>
                </div>
                <!-- Close/Exit button -->
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="text-white/80 hover:text-white transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>

            <!-- Chat Message Thread -->
            <div id="chat-messages" class="flex-1 overflow-y-auto p-5 space-y-4 bg-white scrollbar-thin">
                <!-- Bot Greeting -->
                <div class="flex gap-2.5 items-start">
                    <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" class="bi bi-robot" viewBox="0 0 16 16">
                            <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z"/>
                            <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z"/>
                        </svg>
                    </div>
                    <div class="bg-slate-100 text-slate-800 rounded-2xl rounded-tl-none px-4 py-2.5 text-sm max-w-[80%] leading-relaxed shadow-sm">
                        Halo. Ada yang bisa saya bantu terkait dokumen atau reminder?
                    </div>
                </div>

                @php
                    if (!function_exists('formatMarkdownPhp')) {
                        function formatMarkdownPhp($text) {
                            $escaped = e($text);
                            $escaped = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $escaped);
                            $escaped = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $escaped);
                            $escaped = preg_replace('/_(.*?)_/', '<em>$1</em>', $escaped);
                            return $escaped;
                        }
                    }
                @endphp

                @foreach ($messages as $msg)
                    @php
                        $isUser = is_array($msg) ? ($msg['sender'] === 'user') : ($msg->sender === 'user');
                        $msgText = is_array($msg) ? $msg['message'] : $msg->message;
                    @endphp
                    <div class="flex gap-2.5 items-start {{ $isUser ? 'flex-row-reverse' : '' }}">
                        @if (!$isUser)
                            <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" class="bi bi-robot" viewBox="0 0 16 16">
                                    <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z"/>
                                    <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="{{ $isUser ? 'bg-blue-600 text-white rounded-2xl rounded-tr-none' : 'bg-slate-100 text-slate-800 rounded-2xl rounded-tl-none' }} px-4 py-2.5 text-sm max-w-[80%] leading-relaxed shadow-sm">
                            <div class="whitespace-pre-wrap">{!! $isUser ? e($msgText) : formatMarkdownPhp($msgText) !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Footer (Message Input) -->
            <div class="px-5 py-4 border-t border-slate-100 bg-white">
                <form id="chat-form" class="flex items-center justify-between gap-3">
                    <input
                        type="text"
                        id="message-input"
                        placeholder="Reply to Minbot....."
                        class="flex-1 italic placeholder-gray-400 text-sm outline-none bg-transparent"
                        autocomplete="off"
                    >
                    <button
                        type="submit"
                        id="send-button"
                        class="text-slate-400 hover:text-slate-700 transition-colors duration-200 focus:outline-none"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path d="M3.478 2.404a.75.75 0 0 0-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </main>

    <script>
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const chatMessages = document.getElementById('chat-messages');
        const sendButton = document.getElementById('send-button');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (!message) return;

            // Add user message
            addMessage(message, 'user');
            messageInput.value = '';
            
            // Show typing indicator
            showTypingIndicator();
            sendButton.disabled = true;
            messageInput.disabled = true;

            try {
                const response = await fetch('{{ route('chatbot.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ message }),
                });

                const data = await response.json();
                
                removeTypingIndicator();
                addMessage(data.message, 'bot');
            } catch (error) {
                removeTypingIndicator();
                addMessage('Maaf, terjadi kesalahan. Silakan coba lagi nanti.', 'bot');
            } finally {
                sendButton.disabled = false;
                messageInput.disabled = false;
                messageInput.focus();
            }
        });

        function addMessage(text, sender) {
            const div = document.createElement('div');
            div.className = 'flex gap-2.5 items-start ' + (sender === 'user' ? 'flex-row-reverse' : '');
            
            const avatar = sender === 'user' 
                ? '' 
                : `<div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" class="bi bi-robot" viewBox="0 0 16 16">
                            <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z"/>
                            <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z"/>
                        </svg>
                   </div>`;

            const bubbleClass = sender === 'user' 
                ? 'bg-blue-600 text-white rounded-2xl rounded-tr-none' 
                : 'bg-slate-100 text-slate-800 rounded-2xl rounded-tl-none';
            
            const content = sender === 'user' ? escapeHtml(text) : formatMarkdown(text);
            
            div.innerHTML = `
                ${avatar}
                <div class="${bubbleClass} px-4 py-2.5 text-sm max-w-[80%] leading-relaxed shadow-sm">
                    <div class="whitespace-pre-wrap">${content}</div>
                </div>
            `;

            chatMessages.appendChild(div);
            scrollToBottom();
        }

        function showTypingIndicator() {
            const div = document.createElement('div');
            div.id = 'typing-indicator';
            div.className = 'flex gap-2.5 items-start';
            div.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" class="bi bi-robot" viewBox="0 0 16 16">
                        <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z"/>
                        <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z"/>
                    </svg>
                </div>
                <div class="bg-slate-100 text-slate-800 rounded-2xl rounded-tl-none px-4 py-2.5 text-sm shadow-sm flex items-center justify-center">
                    <div class="typing-indicator flex gap-1.5 py-1">
                        <span class="w-1.5 h-1.5 bg-slate-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-1.5 h-1.5 bg-slate-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-1.5 h-1.5 bg-slate-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                </div>
            `;
            chatMessages.appendChild(div);
            scrollToBottom();
        }

        function removeTypingIndicator() {
            const indicator = document.getElementById('typing-indicator');
            if (indicator) {
                indicator.remove();
            }
        }

        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };

            return text.replace(/[&<>"]/g, function(m) { return map[m]; });
        }

        // Typing text animation
        (function () {
            const typingText = document.getElementById('welcome-typing-text');
            const typingTarget = 'Reminder';

            function startTypingAnimation() {
                if (!typingText) return;

                let index = 0;
                let deleting = false;

                function tick() {
                    if (!deleting) {
                        typingText.textContent = typingTarget.slice(0, index + 1);
                        index++;

                        if (index === typingTarget.length) {
                            deleting = true;
                            setTimeout(tick, 1500);
                            return;
                        }
                    } else {
                        typingText.textContent = typingTarget.slice(0, index - 1);
                        index--;

                        if (index === 0) {
                            deleting = false;
                            setTimeout(tick, 500);
                            return;
                        }
                    }

                    setTimeout(tick, deleting ? 80 : 120);
                }

                typingText.textContent = '';
                setTimeout(tick, 400);
            }

            startTypingAnimation();
        })();

        function formatMarkdown(text) {
            let escaped = escapeHtml(text);
            
            // Format Bold (**text**)
            escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            
            // Format Italic (*text* or _text_)
            escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');
            escaped = escaped.replace(/_(.*?)_/g, '<em>$1</em>');
            
            return escaped;
        }

        // Auto scroll to bottom on page load
        scrollToBottom();
    </script>
</body>
</html>
