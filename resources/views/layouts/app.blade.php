<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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

        <script>
            (function () {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (stored === 'dark' || (!stored && prefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-900 dark:bg-black dark:text-zinc-100 transition-colors overflow-x-hidden">
        <div class="min-h-screen bg-gray-100 dark:bg-black transition-colors" x-data="{ sidebarOpen: false, isDark: document.documentElement.classList.contains('dark'), toggleTheme() { this.isDark = !this.isDark; document.documentElement.classList.toggle('dark', this.isDark); localStorage.setItem('theme', this.isDark ? 'dark' : 'light'); } }">
            @include('layouts.navigation')

            <div class="min-h-screen flex flex-col transition-all duration-200 lg:pl-64">
                @isset($header)
                    <header class="bg-white dark:bg-zinc-900 shadow dark:shadow-black/40 transition-colors">
                        <div class="w-full py-4 sm:py-6 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="min-w-0">{{ $header }}</div>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                @if (Auth::user()?->isSuperAdmin())
                                    <div class="relative" x-data="{ open: false }">
                                        @php
                                            $pendingOtpsCount = \App\Models\User::whereNotNull('reset_otp')->where('reset_otp_expires_at', '>', now())->count();
                                        @endphp
                                        <button @click="open = !open" type="button" aria-label="View notifications" class="relative p-2 text-gray-500 hover:text-gray-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-full transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-lock" viewBox="0 0 16 16">
                                                <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m0 5.996V14H3s-1 0-1-1 1-4 6-4q.845.002 1.544.107a4.5 4.5 0 0 0-.803.918A11 11 0 0 0 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664zM9 13a1 1 0 0 1 1-1v-1a2 2 0 1 1 4 0v1a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1zm3-3a1 1 0 0 0-1 1v1h2v-1a1 1 0 0 0-1-1"/>
                                            </svg>
                                            
                                            @if ($pendingOtpsCount > 0)
                                                <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                                </span>
                                            @endif
                                        </button>
                                        
                                        <div
                                            x-cloak
                                            x-show="open"
                                            @click.outside="open = false"
                                            x-transition:enter="transition ease-out duration-150"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-100"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="absolute right-[-90px] md:right-0 z-50 mt-2 w-80 max-w-[calc(100vw-2rem)] overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-black/40"
                                        >
                                            <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">
                                                <p class="text-sm font-semibold text-gray-800 dark:text-zinc-100">Permohonan Reset Password</p>
                                                <p class="text-xs text-gray-500 dark:text-zinc-400">Hubungi user terkait untuk verifikasi langsung.</p>
                                            </div>

                                            <div class="max-h-60 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800">
                                                @php
                                                    $pendingUsers = \App\Models\User::whereNotNull('reset_otp')->where('reset_otp_expires_at', '>', now())->get();
                                                @endphp
                                                @forelse ($pendingUsers as $pendingUser)
                                                    <div class="p-3 hover:bg-zinc-50 dark:hover:bg-zinc-850">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <div class="min-w-0">
                                                                <p class="text-xs font-semibold text-gray-900 dark:text-zinc-100 truncate">{{ $pendingUser->nama }}</p>
                                                                <p class="text-[10px] text-gray-500 dark:text-zinc-400 truncate">{{ $pendingUser->email }}</p>
                                                            </div>
                                                            <div class="flex-shrink-0 text-right">
                                                                <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-bold text-amber-700 dark:bg-amber-900/20 dark:text-amber-300">
                                                                    OTP: {{ $pendingUser->reset_otp }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="p-4 text-center text-xs text-gray-500 dark:text-zinc-400">
                                                        Tidak ada permohonan reset password aktif.
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <button @click="toggleTheme()" type="button" aria-label="Toggle dark mode" class="p-2 text-gray-500 hover:text-amber-500 dark:text-zinc-400 dark:hover:text-amber-400 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-full transition-colors">
                                    <svg x-show="!isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 6a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 14.536a1 1 0 10-1.414 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707zM4 11a1 1 0 100-2H3a1 1 0 000 2h1zm1.757-7.657a1 1 0 00-1.414 0l-.707.707A1 1 0 105.05 5.464l.707-.707a1 1 0 000-1.414z" />
                                    </svg>
                                    <svg x-show="isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" style="display: none;">
                                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                                    </svg>
                                </button>
                                
                                <button @click="sidebarOpen = !sidebarOpen" type="button" aria-label="Open navigation menu" class="inline-flex md:hidden items-center justify-center rounded-md border border-gray-200 bg-white p-2 text-gray-600 shadow-sm transition-colors hover:bg-gray-50 hover:text-gray-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </header>
                @endisset

                <main class="p-6">
                    @if (session('success'))
                        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" class="fixed top-5 right-5 z-50 w-[22rem] rounded-md border border-green-200 dark:border-green-900 bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-700 dark:text-green-300 shadow-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        @if (Auth::user()?->canUseChatbot())
        <!-- Chatbot Floating Button -->
        <button
            id="chatbot-toggle"
            aria-label="Open Chatbot"
            class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 shadow-xl border border-gray-100 dark:border-zinc-700 transition-all duration-300 hover:scale-110 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-blue-600 dark:hover:text-blue-400 active:scale-95 focus:outline-none"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-robot" viewBox="0 0 16 16">
                <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z"/>
                <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z"/>
            </svg>
        </button>

        <!-- Chatbot Window Popup -->
        <div id="chatbot-window" class="fixed bottom-24 right-6 z-50 w-[calc(100vw-3rem)] max-w-sm md:max-w-md bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] dark:shadow-black/65 overflow-hidden flex flex-col h-[480px] md:h-[530px] transition-all duration-300 transform translate-y-4 opacity-0 pointer-events-none border border-zinc-200 dark:border-zinc-800">
            <!-- Chat Window Header -->
            <div class="bg-[#5f6063] dark:bg-zinc-800 px-5 py-4 flex items-center justify-between text-white select-none">
                <div class="flex items-center gap-3">
                    <!-- Profile Picture -->
                    <div class="relative w-11 h-11 bg-slate-500 rounded-full border-2 border-white/20 flex items-center justify-center shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="white" class="bi bi-robot" viewBox="0 0 16 16">
                            <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z"/>
                            <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z"/>
                        </svg>
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-[#5f6063] dark:border-zinc-800 rounded-full"></span>
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
                <button id="close-chatbot" class="text-white/80 hover:text-white transition-colors duration-200 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Chat Message Thread -->
            <div id="chat-messages" class="flex-1 overflow-y-auto p-5 space-y-4 bg-white dark:bg-zinc-900 scrollbar-thin">
                <!-- Bot Greeting -->
                <div class="flex gap-2.5 items-start">
                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" class="bi bi-robot" viewBox="0 0 16 16">
                            <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z"/>
                            <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z"/>
                        </svg>
                    </div>
                    <div class="bg-slate-100 dark:bg-zinc-800 text-slate-800 dark:text-zinc-200 rounded-2xl rounded-tl-none px-4 py-2.5 text-sm max-w-[80%] leading-relaxed shadow-sm">
                        Halo 👋 Ada yang bisa saya bantu terkait dokumen atau reminder?
                    </div>
                </div>
            </div>

            <!-- Footer (Message Input) -->
            <div class="px-5 py-4 border-t border-slate-100 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                <form id="chat-form" class="flex items-center justify-between gap-3">
                    <input
                        type="text"
                        id="message-input"
                        placeholder="Reply to Minbot....."
                        class="flex-1 italic placeholder-gray-400 text-sm outline-none bg-transparent text-slate-800 dark:text-zinc-200"
                        autocomplete="off"
                    >
                    <button
                        type="submit"
                        id="send-button"
                        class="text-slate-400 hover:text-slate-700 dark:hover:text-zinc-300 transition-colors duration-200 focus:outline-none"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                            <path d="M3.478 2.404a.75.75 0 0 0-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chatbotToggle = document.getElementById('chatbot-toggle');
                const chatbotWindow = document.getElementById('chatbot-window');
                const closeChatbot = document.getElementById('close-chatbot');

                const chatForm = document.getElementById('chat-form');
                const messageInput = document.getElementById('message-input');
                const chatMessages = document.getElementById('chat-messages');
                const sendButton = document.getElementById('send-button');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                function openChatbotPopup() {
                    chatbotWindow.classList.remove('translate-y-4', 'opacity-0', 'pointer-events-none');
                    chatbotToggle.classList.add('scale-0', 'opacity-0', 'pointer-events-none');
                    setTimeout(() => messageInput.focus(), 150);
                }

                function closeChatbotPopup() {
                    chatbotWindow.classList.add('translate-y-4', 'opacity-0', 'pointer-events-none');
                    chatbotToggle.classList.remove('scale-0', 'opacity-0', 'pointer-events-none');
                }

                // Click floating action button
                chatbotToggle.addEventListener('click', openChatbotPopup);

                // Click Close button
                closeChatbot.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeChatbotPopup();
                });

                // Bind all sidebar AI Assistant trigger buttons
                document.querySelectorAll('.chatbot-trigger').forEach(trigger => {
                    trigger.addEventListener('click', function(e) {
                        e.preventDefault();
                        openChatbotPopup();
                    });
                });

                // Chat logic
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
                        : `<div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" class="bi bi-robot" viewBox="0 0 16 16">
                                    <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z"/>
                                    <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z"/>
                                </svg>
                            </div>`;

                    const bubbleClass = sender === 'user' 
                        ? 'bg-blue-600 text-white rounded-2xl rounded-tr-none' 
                        : 'bg-slate-100 dark:bg-zinc-800 text-slate-800 dark:text-zinc-200 rounded-2xl rounded-tl-none';
                    
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
                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" class="bi bi-robot" viewBox="0 0 16 16">
                                <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.58 26.58 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.933.933 0 0 1-.765.935c-.845.147-2.34.346-4.235.346-1.895 0-3.39-.2-4.235-.346A.933.933 0 0 1 3 9.219V8.062Zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a24.767 24.767 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25.286 25.286 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135Z"/>
                                <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A4.5 4.5 0 0 0 10.5 3h-2V1.866ZM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5Z"/>
                            </svg>
                        </div>
                        <div class="bg-slate-100 dark:bg-zinc-800 text-slate-800 dark:text-zinc-200 rounded-2xl rounded-tl-none px-4 py-2.5 text-sm shadow-sm flex items-center justify-center">
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

                function formatMarkdown(text) {
                    let escaped = escapeHtml(text);
                    
                    // Format Bold (**text**)
                    escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                    
                    // Format Italic (*text* or _text_)
                    escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');
                    escaped = escaped.replace(/_(.*?)_/g, '<em>$1</em>');
                    return escaped;
                }
            });
        </script>
        @endif
    </body>
</html>