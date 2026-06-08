<x-guest-layout>
    <div class="w-full max-w-md bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm p-8 sm:p-10 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/20 dark:border-zinc-800 transition-all duration-300">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 mb-4 ring-8 ring-indigo-50/50 dark:ring-indigo-950/20">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-zinc-100 mb-2">
                {{ __('Kata Sandi Baru') }}
            </h2>
            <p class="text-sm leading-relaxed text-gray-500 dark:text-zinc-400 px-2">
                {{ __('Tahap terakhir verifikasi berhasil. Silakan buat kata sandi baru yang kuat untuk mengamankan kembali akun Anda.') }}
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="space-y-1.5">
                <x-input-label for="email" :value="__('Email Terverifikasi')" class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-zinc-400" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    <x-text-input 
                        id="email" 
                        class="block w-full pl-11 pr-4 py-3 bg-gray-100/70 text-gray-500 border border-gray-200 dark:bg-zinc-800/40 dark:text-zinc-400 dark:border-zinc-800 rounded-xl pointer-events-none shadow-inner" 
                        type="email" 
                        name="email" 
                        :value="old('email', $request->email)" 
                        required 
                        autocomplete="username" 
                    />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
            </div>

            <div class="space-y-1.5">
                <x-input-label for="password" :value="__('Kata Sandi Baru')" class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-zinc-400" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                    <x-text-input 
                        id="password" 
                        class="block w-full pl-11 pr-4 py-3 bg-gray-50/50 dark:bg-zinc-800/50 border border-gray-200 dark:border-zinc-700/80 rounded-xl shadow-inner focus:bg-white dark:focus:bg-zinc-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200" 
                        type="password" 
                        name="password" 
                        required 
                        autofocus
                        autocomplete="new-password" 
                        placeholder="••••••••"
                    />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
            </div>

            <div class="space-y-1.5">
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-zinc-400" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                        </svg>
                    </div>
                    <x-text-input 
                        id="password_confirmation" 
                        class="block w-full pl-11 pr-4 py-3 bg-gray-50/50 dark:bg-zinc-800/50 border border-gray-200 dark:border-zinc-700/80 rounded-xl shadow-inner focus:bg-white dark:focus:bg-zinc-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200" 
                        type="password" 
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password" 
                        placeholder="••••••••"
                    />
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
            </div>

            <div class="pt-3">
                <x-primary-button class="w-full justify-center bg-slate-800 text-white text-xs font-bold tracking-wider uppercase px-12 py-3.5 rounded-xl shadow-lg shadow-slate-900/20 hover:bg-slate-900 active:scale-95 transition-all duration-150">
                    {{ __('Perbarui Kata Sandi') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>