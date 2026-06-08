<section>
    <header>
        <h2 class="text-lg font-medium text-slate-900 dark:text-zinc-100">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600 dark:text-zinc-400">
            {{ __("Perbarui informasi profil akun, nomor telepon, dan alamat email Anda.") }}
        </p>
    </header>

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