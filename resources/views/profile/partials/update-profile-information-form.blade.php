<form method="post" action="{{ route('profile.update') }}" class="flex flex-1 flex-col gap-4">
    @csrf
    @method('patch')

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="nama" class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-neutral-400 dark:text-neutral-400">{{ __('Nama Lengkap') }}</label>
            <input id="nama" name="nama" type="text" required autocomplete="name"
                value="{{ old('nama', $user->nama) }}"
                class="w-full rounded-md border border-neutral-200 bg-neutral-50 px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-300 outline-none transition-all focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-[#2d2d2d] dark:text-neutral-50 dark:placeholder-neutral-500 dark:focus:border-white dark:focus:ring-white">
            @error('nama')
                <p class="mt-1.5 text-[10px] font-bold text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-neutral-400 dark:text-neutral-400">{{ __('Alamat Email') }}</label>
            <input id="email" name="email" type="email" required autocomplete="username"
                value="{{ old('email', $user->email) }}"
                class="w-full rounded-md border border-neutral-200 bg-neutral-50 px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-300 outline-none transition-all focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-[#2d2d2d] dark:text-neutral-50 dark:placeholder-neutral-500 dark:focus:border-white dark:focus:ring-white">
            @error('email')
                <p class="mt-1.5 text-[10px] font-bold text-red-500">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-[11px] leading-relaxed text-neutral-500 dark:text-neutral-400">
                        {{ __('Alamat email Anda belum terverifikasi.') }}

                        <button form="send-verification" class="font-semibold text-neutral-700 underline rounded-md hover:text-neutral-900 focus:outline-none focus:ring-2 focus:ring-neutral-900 dark:text-neutral-300 dark:hover:text-white">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                            {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="mt-auto flex items-center justify-end gap-4 pt-1">
        <button type="submit" class="rounded-lg bg-neutral-900 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:bg-neutral-800 active:scale-[0.98] disabled:opacity-50 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100">
            {{ __('Simpan') }}
        </button>

        @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition.opacity
               x-init="setTimeout(() => show = false, 3000)"
               class="text-xs font-semibold text-neutral-400 dark:text-neutral-400">Tersimpan.</p>
        @endif
    </div>
</form>
