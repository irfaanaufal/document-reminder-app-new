<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Reminder App') }}</title>
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
</body>
</html>