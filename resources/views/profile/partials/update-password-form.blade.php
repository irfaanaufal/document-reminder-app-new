<form method="post" action="{{ route('password.update') }}" class="flex flex-1 flex-col gap-4">
    @csrf
    @method('put')

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="update_password_current_password" class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-neutral-400 dark:text-neutral-400">{{ __('Password Sekarang') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                class="w-full rounded-md border border-neutral-200 bg-neutral-50 px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-300 outline-none transition-all focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-[#2d2d2d] dark:text-neutral-50 dark:placeholder-neutral-500 dark:focus:border-white dark:focus:ring-white">
            @foreach ($errors->updatePassword->get('current_password') ?? [] as $message)
                <p class="mt-1.5 text-[10px] font-bold text-red-500">{{ $message }}</p>
            @endforeach
        </div>

        <div>
            <label for="update_password_password" class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-neutral-400 dark:text-neutral-400">{{ __('Password Baru') }}</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                class="w-full rounded-md border border-neutral-200 bg-neutral-50 px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-300 outline-none transition-all focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-[#2d2d2d] dark:text-neutral-50 dark:placeholder-neutral-500 dark:focus:border-white dark:focus:ring-white">
            @foreach ($errors->updatePassword->get('password') ?? [] as $message)
                <p class="mt-1.5 text-[10px] font-bold text-red-500">{{ $message }}</p>
            @endforeach
        </div>

        <div class="md:col-span-2">
            <label for="update_password_password_confirmation" class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-neutral-400 dark:text-neutral-400">{{ __('Konfirmasi Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                class="w-full rounded-md border border-neutral-200 bg-neutral-50 px-3.5 py-2.5 text-sm text-neutral-900 placeholder-neutral-300 outline-none transition-all focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 dark:border-neutral-700 dark:bg-[#2d2d2d] dark:text-neutral-50 dark:placeholder-neutral-500 dark:focus:border-white dark:focus:ring-white">
            @foreach ($errors->updatePassword->get('password_confirmation') ?? [] as $message)
                <p class="mt-1.5 text-[10px] font-bold text-red-500">{{ $message }}</p>
            @endforeach
        </div>
    </div>

    <div class="mt-auto flex items-center gap-4 pt-1">
        <button type="submit" class="rounded-lg bg-neutral-900 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:bg-neutral-800 active:scale-[0.98] disabled:opacity-50 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100">
            {{ __('Perbarui Password') }}
        </button>

        @if (session('status') === 'password-updated')
            <p x-data="{ show: true }" x-show="show" x-transition.opacity
               x-init="setTimeout(() => show = false, 3000)"
               class="text-xs font-semibold text-neutral-400 dark:text-neutral-400">Tersimpan.</p>
        @endif
    </div>
</form>
