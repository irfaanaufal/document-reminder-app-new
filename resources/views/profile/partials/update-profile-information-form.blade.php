<section>
    <header>
        <h2 class="text-lg font-medium text-slate-900 dark:text-zinc-100">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600 dark:text-zinc-400">
            {{ __("Perbarui informasi profil akun, nomor telepon, dan alamat email Anda.") }}
        </p>
    </header>

    <!-- Avatar Upload Section -->
    <div class="mt-6 p-4 bg-slate-50 dark:bg-zinc-800 rounded-xl">
        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-3">Foto Profil</label>
        <div class="flex items-center gap-4">
            <div id="avatar-preview" class="w-16 h-16 rounded-2xl overflow-hidden bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center text-white text-2xl font-bold shrink-0">
                @if(Auth::user()->avatar_path)
                    <img src="{{ asset(Auth::user()->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover">
                @else
                    {{ substr(Auth::user()->nama ?? 'U', 0, 2) }}
                @endif
            </div>
            <div>
                <label for="avatar-input" class="cursor-pointer inline-flex items-center px-4 py-2 bg-slate-800 dark:bg-white dark:text-slate-900 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Ubah Foto
                </label>
                <input type="file" id="avatar-input" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif" class="hidden">
                <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">Maks 3MB (JPG, PNG, WebP, GIF)</p>
            </div>
        </div>
        <div id="avatar-status" class="mt-2 text-sm hidden"></div>
    </div>

    <script>
    document.getElementById('avatar-input').addEventListener('change', function(input) {
        var file = input.target.files[0];
        if (!file) return;

        if (file.size > 3 * 1024 * 1024) {
            alert('Ukuran file maksimal 3MB');
            input.target.value = '';
            return;
        }

        var preview = document.getElementById('avatar-preview');
        var status = document.getElementById('avatar-status');

        // Preview
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Avatar" class="w-full h-full object-cover">';
        };
        reader.readAsDataURL(file);

        // Upload
        var formData = new FormData();
        formData.append('avatar', file);

        status.textContent = 'Mengupload...';
        status.className = 'mt-2 text-sm text-slate-500';

        fetch('{{ route("profile.avatar") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                preview.innerHTML = '<img src="' + data.avatar_url + '" alt="Avatar" class="w-full h-full object-cover">';
                status.textContent = 'Berhasil diupload!';
                status.className = 'mt-2 text-sm text-emerald-600';
            } else {
                status.textContent = 'Gagal mengupload foto.';
                status.className = 'mt-2 text-sm text-red-600';
            }
        })
        .catch(function(err) {
            status.textContent = 'Gagal mengupload foto.';
            status.className = 'mt-2 text-sm text-red-600';
        });

        input.target.value = '';
    });
    </script>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="nama" :value="__('Nama')" />
            <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $user->nama)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('nama')" />
        </div>

        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div>
            <x-input-label for="no_telpon" :value="__('Nomor Telepon')" />
            <x-text-input id="no_telpon" name="no_telpon" type="tel" class="mt-1 block w-full" :value="old('no_telpon', $user->no_telpon)" required autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('no_telpon')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-slate-800 dark:text-zinc-200">
                        {{ __('Alamat email Anda belum terverifikasi.') }}

                        <button form="send-verification" class="underline text-sm text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-emerald-600">
                            {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-slate-800 hover:bg-slate-900 focus:bg-slate-900 active:bg-slate-950 focus:ring-slate-500 shadow-md">
                {{ __('Simpan') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm font-semibold text-emerald-600 animate-pulse flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('Perubahan berhasil disimpan!') }}
                </p>
            @endif
        </div>
    </form>
</section>