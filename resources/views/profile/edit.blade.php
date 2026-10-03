<x-app-layout>
    <x-slot name="header">
        <div class="min-w-0">
            <h2 class="font-bold text-xl text-slate-800 dark:text-zinc-100 leading-tight tracking-tight">
                {{ __('Pengaturan Profil') }}
            </h2>
            <p class="text-[10px] font-semibold tracking-wide text-gray-400 dark:text-zinc-500">
                {{ __('Perbarui informasi akun dan keamanan') }}
            </p>
        </div>
    </x-slot>

    @php
        $profileInputClass = 'w-full rounded-md border border-neutral-200 bg-neutral-50 px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-300 outline-none transition-all focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-[#2d2d2d] dark:text-neutral-50 dark:placeholder-neutral-500 dark:focus:border-white dark:focus:ring-white';
        $profileReadonlyClass = 'w-full rounded-md border border-neutral-200 bg-white px-3.5 py-2.5 text-sm font-semibold capitalize text-neutral-800 outline-none dark:border-neutral-700 dark:bg-[#2d2d2d] dark:text-neutral-200';
        $profileCardClass = 'flex flex-col rounded-lg border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-[#1e1e1e]';
        $profileCardTitleClass = 'text-base font-semibold tracking-tight text-gray-900 dark:text-white';
        $profileLabelClass = 'mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-neutral-400 dark:text-neutral-400';
        $profileErrorClass = 'mt-1.5 text-[10px] font-bold text-red-500';
        $profileBtnDark = 'rounded-lg bg-neutral-900 text-xs font-bold text-white shadow-sm transition-all hover:bg-neutral-800 active:scale-[0.98] disabled:opacity-50 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100';
    @endphp

    <div class="w-full max-w-full">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">

            {{-- 1 · Foto Profil --}}
            <div class="flex flex-col overflow-hidden rounded-lg border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-[#1e1e1e] lg:col-start-1 lg:row-start-1">
                <div class="h-14 border-b border-neutral-200 dark:border-neutral-800"></div>

                <div class="flex flex-1 flex-col px-5 pb-5">
                    <input type="file" id="avatar-input" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif" class="hidden">

                    <div class="-mt-10 flex flex-1 items-stretch gap-5">
                        <div class="group relative aspect-square h-24 shrink-0 lg:h-auto">
                            <div id="avatar-preview" class="relative h-full w-full overflow-hidden rounded-lg border border-neutral-300 bg-white shadow-sm dark:border-neutral-600 dark:bg-neutral-800">
                                @if ($user->avatar_path)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar_path) }}" alt="Avatar" class="h-full w-full object-cover">
                                @else
                                    <span class="flex h-full w-full items-center justify-center text-2xl font-black text-neutral-700 dark:text-neutral-200 lg:text-5xl">{{ strtoupper(substr($user->nama ?? 'U', 0, 2)) }}</span>
                                @endif
                            </div>

                            <div id="avatar-processing" class="absolute inset-0 hidden items-center justify-center rounded-lg bg-black/50">
                                <svg class="h-6 w-6 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                            </div>

                            <button type="button" id="avatar-pick" title="Ganti Foto"
                                class="absolute inset-0 flex cursor-pointer items-center justify-center rounded-lg bg-black/0 opacity-0 transition hover:bg-black/40 hover:opacity-100 group-focus-within:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex min-w-0 flex-1 flex-col pt-1">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="truncate text-lg font-bold tracking-tight text-gray-900 dark:text-white md:text-xl">{{ $user->nama }}</h2>

                                @if ($user?->role_id)
                                    <span class="inline-block shrink-0 rounded-md bg-rose-400 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-white">{{ $user->roleLabel() }}</span>
                                @endif
                            </div>

                            <p class="mt-4 truncate text-sm text-gray-500 dark:text-neutral-400">{{ $user->email }}</p>

                            <p class="mt-2 text-[10px] leading-relaxed text-neutral-400 dark:text-neutral-500">Format JPEG, PNG, JPG, WEBP, atau GIF (Maks. 3MB).</p>
                        </div>
                    </div>

                    <div id="avatar-progress" class="mt-3 hidden h-1 w-full overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800">
                        <div id="avatar-progress-fill" class="h-full rounded-full bg-neutral-900 transition-[width] duration-200 dark:bg-white" style="width: 0%"></div>
                    </div>

                    <p id="avatar-success" class="mt-3 hidden text-xs font-semibold text-emerald-600 dark:text-emerald-400">Foto profil berhasil diperbarui.</p>
                    <p id="avatar-error" class="mt-2 hidden text-[10px] font-bold text-red-500"></p>
                </div>
            </div>

            {{-- 2 · Informasi Akun --}}
            <div class="{{ $profileCardClass }} lg:col-start-2 lg:row-start-1">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h3 class="{{ $profileCardTitleClass }}">{{ __('Informasi Akun') }}</h3>
                </div>

                <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                    @csrf
                </form>

                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- 3 · Data Pengguna --}}
            <div class="{{ $profileCardClass }} lg:col-start-1 lg:row-start-2">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h3 class="{{ $profileCardTitleClass }}">{{ __('Data Pengguna') }}</h3>
                    <span class="rounded-md px-2 py-0.5 text-[10px] font-black uppercase tracking-wider {{ ($isAppActive ?? false) ? 'bg-emerald-500 text-white' : 'bg-neutral-200 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300' }}">
                        {{ ($isAppActive ?? false) ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="emp-username" class="{{ $profileLabelClass }}">{{ __('Username') }}</label>
                        <input id="emp-username" readonly tabindex="-1" value="{{ $user->username }}" class="{{ $profileReadonlyClass }}">
                    </div>
                    <div>
                        <label for="emp-phone" class="{{ $profileLabelClass }}">{{ __('No. Telepon') }}</label>
                        <input id="emp-phone" readonly tabindex="-1" value="{{ $user->no_telpon }}" class="{{ $profileReadonlyClass }}">
                    </div>
                    <div>
                        <label for="emp-role" class="{{ $profileLabelClass }}">{{ __('Role Akses') }}</label>
                        <input id="emp-role" readonly tabindex="-1" value="{{ $user->role_id ? $user->roleLabel() : '-' }}" class="{{ $profileReadonlyClass }}">
                    </div>
                    <div>
                        <label for="emp-joined" class="{{ $profileLabelClass }}">{{ __('Terdaftar') }}</label>
                        <input id="emp-joined" readonly tabindex="-1" value="{{ $user->created_at?->format('d M Y') }}" class="{{ $profileReadonlyClass }}">
                    </div>
                </div>

                <div class="mt-4 space-y-3 border-t border-neutral-100 pt-4 dark:border-neutral-800">
                    @if ($isAppActive ?? false)
                        <p class="text-[11px] leading-relaxed text-neutral-400 dark:text-neutral-500">
                            Nonaktifkan akses aplikasi ini dari akun Anda. Untuk mengaktifkan kembali, hubungi tim IT.
                        </p>
                        <form method="POST" action="{{ route('profile.access') }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition-all hover:bg-rose-700 active:scale-[0.98]">
                                {{ __('Nonaktifkan Akses') }}
                            </button>
                        </form>
                    @else
                        <p class="text-[11px] leading-relaxed text-neutral-400 dark:text-neutral-500">
                            Akses aplikasi tidak aktif. Aktivasi hanya dapat dilakukan oleh tim IT melalui persetujuan akses.
                        </p>
                    @endif
                    @error('access')
                        <p class="{{ $profileErrorClass }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- 4 · Ubah Password --}}
            <div class="{{ $profileCardClass }} lg:col-start-1 lg:row-start-3">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h3 class="{{ $profileCardTitleClass }}">{{ __('Ubah Password') }}</h3>
                </div>

                @include('profile.partials.update-password-form')
            </div>

            {{-- 5 · Statistik --}}
            <div class="{{ $profileCardClass }} lg:col-start-2 lg:row-span-2 lg:row-start-2">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h3 class="{{ $profileCardTitleClass }}">{{ __('Statistik') }}</h3>
                </div>

                <div class="flex flex-1 flex-col gap-3">
                    @php
                        $statItems = [
                            ['label' => 'Total Dokumen', 'value' => $stats['total'] ?? 0],
                            ['label' => 'Segera Habis (30 Hari)', 'value' => $stats['segera_habis'] ?? 0],
                            ['label' => 'Kadaluarsa', 'value' => $stats['kadaluarsa'] ?? 0],
                            ['label' => 'Tanpa Expiry', 'value' => $stats['tanpa_expiry'] ?? 0],
                        ];
                    @endphp
                    @foreach ($statItems as $item)
                        <div class="flex flex-1 items-center justify-between rounded-lg border border-neutral-100 bg-neutral-50 px-4 py-3.5 dark:border-neutral-700 dark:bg-[#2a2a2a]">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 dark:text-neutral-400">{{ $item['label'] }}</span>
                            <span class="text-xl font-black tracking-tight text-gray-900 dark:text-white">{{ $item['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <script>
    (function () {
        var input = document.getElementById('avatar-input');
        var pick = document.getElementById('avatar-pick');
        var preview = document.getElementById('avatar-preview');
        var processing = document.getElementById('avatar-processing');
        var progress = document.getElementById('avatar-progress');
        var fill = document.getElementById('avatar-progress-fill');
        var success = document.getElementById('avatar-success');
        var errorEl = document.getElementById('avatar-error');
        var allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/gif'];

        if (!input || !pick) return;

        pick.addEventListener('click', function () { input.click(); });

        input.addEventListener('change', function (e) {
            var file = e.target.files[0];
            e.target.value = '';
            if (!file) return;

            var originalPreview = preview.innerHTML;
            errorEl.classList.add('hidden');
            success.classList.add('hidden');

            if (file.size > 3 * 1024 * 1024) {
                errorEl.textContent = 'Ukuran file maksimal 3MB.';
                errorEl.classList.remove('hidden');
                return;
            }

            if (allowed.indexOf(file.type) === -1) {
                errorEl.textContent = 'Format file harus JPEG, PNG, JPG, WEBP, atau GIF.';
                errorEl.classList.remove('hidden');
                return;
            }

            var reader = new FileReader();
            reader.onload = function (ev) {
                preview.innerHTML = '<img src="' + ev.target.result + '" alt="Avatar" class="h-full w-full object-cover">';
            };
            reader.readAsDataURL(file);

            processing.classList.remove('hidden');
            processing.classList.add('flex');
            progress.classList.remove('hidden');
            fill.style.width = '0%';

            var formData = new FormData();
            formData.append('avatar', file);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('profile.avatar') }}');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            xhr.setRequestHeader('Accept', 'application/json');

            xhr.upload.addEventListener('progress', function (ev) {
                if (ev.lengthComputable) {
                    fill.style.width = Math.round((ev.loaded / ev.total) * 100) + '%';
                }
            });

            function stopProcessing() {
                processing.classList.add('hidden');
                processing.classList.remove('flex');
            }

            xhr.addEventListener('load', function () {
                stopProcessing();
                var data = null;
                try { data = JSON.parse(xhr.responseText); } catch (err) {}

                if (xhr.status >= 200 && xhr.status < 300 && data && data.success) {
                    preview.innerHTML = '<img src="' + data.avatar_url + '" alt="Avatar" class="h-full w-full object-cover">';
                    success.classList.remove('hidden');
                    setTimeout(function () {
                        success.classList.add('hidden');
                        progress.classList.add('hidden');
                        fill.style.width = '0%';
                    }, 3000);
                } else {
                    var msg = 'Gagal mengupload foto.';
                    if (data && data.errors && data.errors.avatar) {
                        msg = data.errors.avatar[0];
                    }
                    preview.innerHTML = originalPreview;
                    progress.classList.add('hidden');
                    fill.style.width = '0%';
                    errorEl.textContent = msg;
                    errorEl.classList.remove('hidden');
                }
            });

            xhr.addEventListener('error', function () {
                stopProcessing();
                preview.innerHTML = originalPreview;
                progress.classList.add('hidden');
                fill.style.width = '0%';
                errorEl.textContent = 'Gagal mengupload foto.';
                errorEl.classList.remove('hidden');
            });

            xhr.send(formData);
        });
    })();
    </script>
</x-app-layout>
