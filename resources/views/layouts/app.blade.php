<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

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
    </body>
</html>