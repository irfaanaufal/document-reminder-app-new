<x-split-auth-layout title="Masuk — {{ config('app.name', 'Reminder App') }}">
    <div class="flex h-screen flex-col overflow-hidden bg-black font-['-apple-system',_BlinkMacSystemFont,_'Segoe_UI',_sans-serif] lg:grid lg:grid-cols-2">

        {{-- Panel hitam: kiri (desktop) / atas (mobile) --}}
        <div class="relative flex h-[35vh] shrink-0 items-center justify-center bg-black lg:order-2 lg:h-auto">
            <div class="absolute top-5 right-5 text-[13px] font-semibold tracking-[0.3em] text-white/90 lg:top-10 lg:right-10">REMINDER-APP</div>
            <img src="{{ asset('images/login.png') }}" alt="{{ config('app.name', 'Reminder App') }}" class="oc-anim h-[60%] w-[60%] object-contain" style="animation: oc-float 6s ease-in-out infinite, oc-glow 4s ease-in-out infinite" />
            <style>
                @keyframes oc-float {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-12px); }
                }
                @keyframes oc-glow {
                    0%, 100% { filter: drop-shadow(0 0 12px rgba(255,255,255,0.08)); }
                    50% { filter: drop-shadow(0 0 24px rgba(255,255,255,0.18)); }
                }
                @media (prefers-reduced-motion: reduce) {
                    .oc-anim { animation: none !important; filter: none !important; }
                }
            </style>
        </div>

        {{-- Panel form: kanan (desktop) / bawah (mobile) --}}
        <div class="flex min-h-0 flex-1 items-start justify-start overflow-y-auto rounded-tl-[48px] bg-white px-6 pt-7 lg:order-1 lg:items-center lg:justify-center lg:rounded-tl-none lg:px-20">
            <div class="w-full max-w-[360px] pb-10 lg:pb-0">
                <h1 class="text-center text-[27px] text-neutral-900" style="font-family: 'Newsreader', Georgia, serif">Masuk</h1>

                @if (session('status'))
                    <div class="mt-6 text-center text-[13px] font-medium text-emerald-600">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-3">
                    @csrf

                    <div>
                        <label for="username" class="sr-only">Email atau username</label>
                        <input
                            id="username"
                            type="text"
                            name="username"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Email atau username"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-none outline-none transition-colors placeholder-gray-400 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
                        />
                        <x-input-error :messages="$errors->get('username')" class="mt-1.5" />
                        <x-input-error :messages="$errors->get('activation_needed')" class="mt-1.5" />
                    </div>

                    <div class="relative">
                        <label for="password" class="sr-only">Kata Sandi</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Kata Sandi"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 pr-11 text-sm text-gray-900 shadow-none outline-none transition-colors placeholder-gray-400 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
                        />
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute top-1/2 right-3.5 -translate-y-1/2 text-gray-400 transition hover:text-gray-600"
                            aria-label="Tampilkan kata sandi"
                        >
                            <svg id="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path d="M2 12c1-3 5-7 10-7s9 4 10 7c-1 3-5 7-10 7s-9-4-10-7z" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <svg id="eye-closed" class="hidden" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path d="M3 3l18 18M10.6 10.7a2.5 2.5 0 003.5 3.5M9.3 5.5A10.4 10.4 0 0112 5c5 0 9 4 10 7a12.5 12.5 0 01-3.1 4.2M6.2 6.6C4.3 8 2.9 10 2 12c1 3 5 7 10 7 1.4 0 2.7-.3 3.9-.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex cursor-pointer items-center gap-2 text-[12px] text-gray-500">
                            <input type="checkbox" name="remember" class="h-3.5 w-3.5 rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                            Ingat saya
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="mt-2 w-full justify-center rounded-full border-0 bg-gray-900 py-3 text-[13px] font-semibold tracking-wide text-white shadow-none transition-colors hover:bg-gray-800 focus:ring-2 focus:ring-gray-400 focus:ring-offset-0"
                    >
                        Masuk
                    </button>
                </form>

                <div class="mt-5 flex items-center justify-center gap-3 text-[12px] text-gray-500">
                    <span class="text-gray-300">·</span>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="transition hover:text-gray-700">Lupa password?</a>
                    @endif
                    <a href="{{ route('register') }}" class="transition hover:text-gray-700">Daftar</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePassword() {
            var input = document.getElementById('password');
            var open = document.getElementById('eye-open');
            var closed = document.getElementById('eye-closed');
            var showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            open.classList.toggle('hidden', !showing);
            closed.classList.toggle('hidden', showing);
        }

        // SweetAlert khusus gerbang aktivasi reminder (tetap berlaku)
        @if($errors->has('activation_needed'))
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Akun Belum Diaktifkan',
                    text: '{{ $errors->first("activation_needed") }}',
                    confirmButtonText: 'Hubungi Tim IT',
                    confirmButtonColor: '#6366f1',
                });
            });
        @endif
    </script>
</x-split-auth-layout>
