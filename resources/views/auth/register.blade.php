<x-split-auth-layout title="Daftar — {{ config('app.name', 'Reminder App') }}">
    <div class="flex h-screen flex-col overflow-hidden bg-black font-['-apple-system',_BlinkMacSystemFont,_'Segoe_UI',_sans-serif] lg:grid lg:grid-cols-2">

        {{-- Panel hitam: kiri (desktop) / atas (mobile) --}}
        <div class="relative flex h-[22vh] shrink-0 items-center justify-center bg-black lg:order-2 lg:h-auto">
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
        <div class="flex min-h-0 flex-1 items-start justify-start overflow-y-auto rounded-tl-[48px] bg-white px-6 pt-6 lg:order-1 lg:items-center lg:justify-center lg:rounded-tl-none lg:px-20">
            <div class="w-full max-w-[380px] pb-10 lg:pb-0">
                <h1 class="text-center text-[27px] text-neutral-900" style="font-family: 'Newsreader', Georgia, serif">Buat akun</h1>

                <form method="POST" action="{{ route('register') }}" class="mt-8" onsubmit="return handleRegisterSubmit(event)">
                    @csrf

                    {{-- Langkah 1: Cek FID --}}
                    <div id="fid-group" class="space-y-3">
                        <div>
                            <label for="fid_input" class="sr-only">Fingerprint ID (FID) Karyawan</label>
                            <input
                                type="text"
                                name="fid"
                                id="fid_input"
                                value="{{ old('fid') }}"
                                required
                                placeholder="Fingerprint ID — contoh: 309"
                                class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-center text-lg font-semibold tracking-widest text-gray-900 shadow-none outline-none transition-colors placeholder-gray-400/70 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
                            />
                            <p class="mt-2 text-center text-[11px] text-gray-400">FID wajib diisi. Pendaftaran tanpa FID tidak diperbolehkan.</p>
                            <div id="fid_result" class="mt-2 text-center text-[12px]"></div>
                            <x-input-error :messages="$errors->get('fid')" class="mt-1.5 text-center" />
                        </div>

                        <button
                            type="button"
                            id="check_fid_btn"
                            onclick="checkFid()"
                            class="mt-2 w-full justify-center rounded-full border-0 bg-gray-900 py-3 text-[13px] font-semibold tracking-wide text-white shadow-none transition-colors hover:bg-gray-800 focus:ring-2 focus:ring-gray-400 focus:ring-offset-0 disabled:opacity-60"
                        >
                            <svg id="check_spinner" class="mr-2 hidden h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg><span id="check_btn_text">Periksa FID Karyawan</span>
                        </button>
                    </div>

                    {{-- Langkah 2: Form pendaftaran (muncul setelah FID terverifikasi) --}}
                    <div id="register_fields" class="hidden space-y-3">
                        {{-- Kartu data karyawan terdeteksi --}}
                        <div class="space-y-3 rounded-xl border border-gray-200 bg-white p-4 transition-colors">
                            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                                <span class="text-[10px] font-semibold tracking-widest text-gray-500 uppercase">Data Karyawan Terdeteksi</span>
                                <button
                                    type="button"
                                    onclick="resetFid()"
                                    class="text-[10px] font-semibold tracking-wider text-red-600 uppercase hover:text-red-700 hover:underline"
                                >
                                    Ubah FID
                                </button>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-xs">
                                <div>
                                    <span class="mb-0.5 block font-medium text-gray-500">FID</span>
                                    <span id="card_fid" class="font-semibold text-gray-900">#</span>
                                </div>
                                <div class="col-span-2">
                                    <span class="mb-0.5 block font-medium text-gray-500">Nama Karyawan</span>
                                    <span id="card_nama" class="font-semibold text-gray-900"></span>
                                </div>
                                <div class="col-span-3 mt-1">
                                    <span class="mb-0.5 block font-medium text-gray-500">Divisi</span>
                                    <span id="card_divisi" class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1 text-[10px] font-semibold text-gray-700">—</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('fid')" class="mt-1.5" />
                        </div>

                        <div>
                            <label for="nama_input" class="sr-only">Nama Lengkap</label>
                            <input
                                type="text"
                                name="nama"
                                id="nama_input"
                                value="{{ old('nama') }}"
                                required
                                readonly
                                placeholder="Nama lengkap"
                                class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 opacity-60 shadow-none outline-none transition-colors placeholder-gray-400 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
                            />
                            <x-input-error :messages="$errors->get('nama')" class="mt-1.5" />
                        </div>

                        <div>
                            <label for="username" class="sr-only">Username</label>
                            <input
                                type="text"
                                name="username"
                                id="username"
                                value="{{ old('username') }}"
                                required
                                autocomplete="username"
                                placeholder="Username"
                                class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-none outline-none transition-colors placeholder-gray-400 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
                            />
                            <x-input-error :messages="$errors->get('username')" class="mt-1.5" />
                        </div>

                        <div>
                            <label for="email" class="sr-only">Alamat Email</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                placeholder="example@gmail.com"
                                class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-none outline-none transition-colors placeholder-gray-400 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>

                        <div>
                            <label for="no_telpon" class="sr-only">Nomor Telepon</label>
                            <input
                                type="tel"
                                name="no_telpon"
                                id="no_telpon"
                                value="{{ old('no_telpon') }}"
                                required
                                maxlength="15"
                                placeholder="08xxxxxxxxxx"
                                class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-none outline-none transition-colors placeholder-gray-400 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
                            />
                            <x-input-error :messages="$errors->get('no_telpon')" class="mt-1.5" />
                        </div>

                        <div>
                            <label for="password" class="sr-only">Kata Sandi</label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                autocomplete="new-password"
                                placeholder="Password — min. 8 karakter"
                                class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-none outline-none transition-colors placeholder-gray-400 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                        </div>

                        <div>
                            <label for="password_confirmation" class="sr-only">Konfirmasi Password</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Ulangi password"
                                class="block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-none outline-none transition-colors placeholder-gray-400 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
                            />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                        </div>

                        <div class="pt-1">
                            <button
                                type="submit"
                                id="register_submit_btn"
                                class="w-full justify-center rounded-full border-0 bg-gray-900 py-3 text-[13px] font-semibold tracking-wide text-white shadow-none transition-colors hover:bg-gray-800 focus:ring-2 focus:ring-gray-400 focus:ring-offset-0 disabled:opacity-60"
                            >
                                <svg id="register_spinner" class="mr-2 hidden h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg><span id="register_btn_text">Daftar Akun Baru</span>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-5 flex items-center justify-center text-[12px] text-gray-500">
                    <a href="{{ route('login') }}" class="transition hover:text-gray-700">Sudah punya akun? Masuk</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes step-in-right {
            from { opacity: 0; transform: translateX(10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes step-in-left {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .step-in-right { animation: step-in-right 0.25s ease-out; }
        .step-in-left { animation: step-in-left 0.25s ease-out; }
        @media (prefers-reduced-motion: reduce) {
            .step-in-right, .step-in-left { animation: none !important; }
        }
    </style>

    <script>
        function escapeHtml(str) {
            var div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function showStep2(animate) {
            document.getElementById('fid-group').classList.add('hidden');
            var fields = document.getElementById('register_fields');
            fields.classList.remove('hidden');
            if (animate) {
                fields.classList.remove('step-in-right');
                void fields.offsetWidth;
                fields.classList.add('step-in-right');
            }
        }

        function showStep1(animate) {
            document.getElementById('register_fields').classList.add('hidden');
            var group = document.getElementById('fid-group');
            group.classList.remove('hidden');
            if (animate) {
                group.classList.remove('step-in-left');
                void group.offsetWidth;
                group.classList.add('step-in-left');
            }
        }

        function fillCard(fid, nama, divisi) {
            document.getElementById('card_fid').textContent = '#' + fid;
            document.getElementById('card_nama').textContent = nama;
            document.getElementById('card_divisi').textContent = divisi || '—';
        }

        // Validasi gagal (redirect back dengan old data) → buka langsung langkah 2
        @if (old('fid') && old('nama'))
            fillCard('{{ old('fid') }}', '{{ old('nama') }}', '');
            showStep2(false);
        @endif

        function checkFid() {
            var fid = document.getElementById('fid_input').value.trim();
            var resultDiv = document.getElementById('fid_result');
            var checkBtn = document.getElementById('check_fid_btn');
            var spinner = document.getElementById('check_spinner');
            var btnText = document.getElementById('check_btn_text');

            if (!fid) {
                resultDiv.innerHTML = '<span class="text-red-600">Masukkan FID terlebih dahulu.</span>';
                return;
            }

            checkBtn.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = 'Memeriksa...';
            resultDiv.innerHTML = '<span class="text-gray-400">Memeriksa data...</span>';

            fetch('{{ url('register/check-karyawan') }}/' + encodeURIComponent(fid))
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success) {
                        fillCard(data.karyawan.fid, data.karyawan.nama_karyawan, data.karyawan.divisi);
                        document.getElementById('nama_input').value = data.karyawan.nama_karyawan;
                        resultDiv.innerHTML = '';
                        showStep2(true);
                    } else {
                        resultDiv.innerHTML = '<span class="text-red-600">' + escapeHtml(data.message || 'FID tidak valid.') + '</span>';
                        document.getElementById('register_fields').classList.add('hidden');
                        document.getElementById('fid-group').classList.remove('hidden');
                    }
                })
                .catch(function () {
                    resultDiv.innerHTML = '<span class="text-red-600">Terjadi kesalahan. Coba lagi.</span>';
                    document.getElementById('register_fields').classList.add('hidden');
                    document.getElementById('fid-group').classList.remove('hidden');
                })
                .finally(function () {
                    checkBtn.disabled = false;
                    spinner.classList.add('hidden');
                    btnText.textContent = 'Periksa FID Karyawan';
                });
        }

        function resetFid() {
            var input = document.getElementById('fid_input');
            input.value = '';
            document.getElementById('nama_input').value = '';
            document.getElementById('fid_result').innerHTML = '';
            showStep1(true);
            input.focus();
        }

        function handleRegisterSubmit(e) {
            var btn = document.getElementById('register_submit_btn');
            var spinner = document.getElementById('register_spinner');
            var btnText = document.getElementById('register_btn_text');
            btn.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = 'Mendaftarkan...';
            return true;
        }
    </script>
</x-split-auth-layout>
