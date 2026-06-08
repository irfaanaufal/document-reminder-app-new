<x-guest-layout>
    <div class="w-full max-w-md bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm p-8 sm:p-10 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/20 dark:border-zinc-800 transition-all duration-300">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 mb-4 ring-8 ring-indigo-50/50 dark:ring-indigo-950/20">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-zinc-100 mb-2">
                {{ __('Pemulihan Akun') }}
            </h2>
            <p class="text-sm leading-relaxed text-gray-500 dark:text-zinc-400 px-2">
                {{ __('Masukkan email Anda untuk mengajukan reset password. Kode verifikasi OTP wajib diminta langsung melalui Super Admin setelah pengajuan.') }}
            </p>
        </div>

        @if (session('status'))
            <x-auth-session-status class="mb-6 p-4 bg-emerald-50/80 dark:bg-emerald-950/30 rounded-xl border border-emerald-100/50 dark:border-emerald-900/30 text-sm text-emerald-700 dark:text-emerald-400" :status="session('status')" />
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <div class="space-y-1.5">
                <x-input-label for="email" :value="__('Masukan Email Terdaftar')" class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-zinc-400" />
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                    </div>
                    
                    <x-text-input 
                        id="email" 
                        class="block w-full pl-11 pr-4 py-3 bg-gray-50/50 dark:bg-zinc-800/50 border border-gray-200 dark:border-zinc-700/80 rounded-xl shadow-inner focus:bg-white dark:focus:bg-zinc-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 placeholder-gray-400" 
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        required 
                        autofocus 
                        placeholder="example@gmail.com"
                    />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-medium text-red-500" />
            </div>

            <div class="space-y-4 pt-2">
                <x-primary-button class="w-full justify-center bg-slate-800 text-white text-xs font-bold tracking-wider uppercase px-12 py-3.5 rounded-xl shadow-lg shadow-slate-900/20 hover:bg-slate-900 active:scale-95 transition-all duration-150">
                    {{ __('Kirim Permohonan') }}
                </x-primary-button>
                
                <div class="text-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300 transition-colors group">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 transform group-hover:-translate-x-0.5 transition-transform">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7M3 12h18" />
                        </svg>
                        {{ __('Kembali ke Login') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>