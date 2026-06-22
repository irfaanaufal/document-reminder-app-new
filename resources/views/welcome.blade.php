<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 antialiased selection:bg-blue-500 selection:text-white">

    <div class="relative flex min-h-screen flex-col overflow-hidden">

        {{-- Background Image --}}
        <div
            class="absolute inset-0 bg-cover bg-center bg-no-repeat"
            style="background-image: url('{{ asset('images/background.jpg') }}');"
        ></div>

        {{-- Dark Overlay --}}
        <div class="absolute inset-0 bg-black/40 transition-colors duration-300"></div>

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

        {{-- Hero Section (Sempurna di Tengah untuk Mobile & Desktop) --}}
        <main class="relative z-10 flex flex-1 items-center justify-center px-6 text-center sm:px-8">
            <div class="mx-auto max-w-6xl">

                <h1 class="text-[clamp(2.8rem,12vw,8rem)] font-black leading-none tracking-[-0.06em] text-white drop-shadow-[0_10px_30px_rgba(0,0,0,0.8)]">
                    <span id="welcome-typing-text">Reminder</span>
                    <span
                        class="ml-0.5 inline-block animate-pulse align-baseline text-yellow-300"
                        aria-hidden="true"
                    >|</span>
                </h1>

                <p class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-white/90 sm:text-lg sm:leading-7">
                    Sistem pengingat dokumen yang dibuat lebih rapi, profesional,
                    dan mudah dipantau untuk membantu Anda menjaga setiap masa berlaku
                    tetap terkendali.
                </p>

            </div>
        </main>

    </div>

    <script>
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
    </script>

    @if (Auth::user()?->canUseChatbot())
    <a
        href="{{ route('chatbot.index') }}"
        id="chatbot-toggle"
        aria-label="Open Chatbot"
        class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-white text-gray-700 shadow-xl border border-gray-100 transition-all duration-300 hover:scale-110 hover:bg-gray-50 hover:text-blue-600 active:scale-95"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-robot" viewBox="0 0 16 16">
            <path d="M6 12.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5ZM3 8.062C3 6.76 4.235 5.765 5.53 5.886a26.6 26.6 0 0 0 4.94 0C11.765 5.765 13 6.76 13 8.062v1.157a.93.93 0 0 1-.765.935c-.845.147-2.34.346-4.235.346s-3.39-.2-4.235-.346A.93.93 0 0 1 3 9.219zm4.542-.827a.25.25 0 0 0-.217.068l-.92.9a25 25 0 0 1-1.871-.183.25.25 0 0 0-.068.495c.55.076 1.232.149 2.02.193a.25.25 0 0 0 .189-.071l.754-.736.847 1.71a.25.25 0 0 0 .404.062l.932-.97a25 25 0 0 0 1.922-.188.25.25 0 0 0-.068-.495c-.538.074-1.207.145-1.98.189a.25.25 0 0 0-.166.076l-.754.785-.842-1.7a.25.25 0 0 0-.182-.135"/>
            <path d="M8.5 1.866a1 1 0 1 0-1 0V3h-2A4.5 4.5 0 0 0 1 7.5V8a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1v-.5A3.5 3.5 0 0 0 10.5 3h-2zM14 7.5V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7.5A3.5 3.5 0 0 1 5.5 4h5A3.5 3.5 0 0 1 14 7.5"/>
        </svg>
    </a>
    @endif
</body>
</html>