<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <h2 class="font-bold text-xl text-slate-800 dark:text-zinc-100 leading-tight tracking-tight">
                {{ __('Pengaturan Profil') }}
            </h2>
        </div>
    </x-slot>

    
    <div class="py-6 max-w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="p-6 bg-white dark:bg-zinc-900 border border-slate-100 dark:border-zinc-800 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.02)] flex flex-col sm:flex-row items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden bg-gradient-to-br from-slate-700 to-slate-900 dark:from-zinc-700 dark:to-zinc-900 flex items-center justify-center text-white text-2xl font-bold uppercase shadow-md shadow-slate-900/10 tracking-wider shrink-0">
                    @if(Auth::user()->avatar_path)
                        <img src="{{ asset(Auth::user()->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        {{ substr(Auth::user()->nama ?? 'U', 0, 2) }}
                    @endif
                </div>
                    <div class="text-center sm:text-left">
                        <h3 class="text-xl font-bold text-slate-800 dark:text-zinc-100">{{ Auth::user()->nama }}</h3>
                        <p class="text-sm text-slate-500 dark:text-zinc-400 mt-0.5">{{ Auth::user()->email }}</p>
                        <span class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-zinc-850 text-slate-800 dark:text-zinc-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Akun Aktif
                        </span>
                    </div>
                </div>
                <div class="p-6 sm:p-8 bg-white dark:bg-zinc-900 border border-slate-100 dark:border-zinc-800 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.02)] transition-all duration-300">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                
                <div class="p-6 bg-white dark:bg-zinc-900 border border-slate-100 dark:border-zinc-800 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.02)]">
                    <div class="flex items-center gap-2 pb-4 mb-4 border-b border-slate-100 dark:border-zinc-800">
                        <svg class="w-5 h-5 text-slate-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <h4 class="font-bold text-sm text-slate-800 dark:text-zinc-200 uppercase tracking-wider">Keamanan</h4>
                    </div>
                    <div class="max-w-full">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
                <!-- <div class="p-6 bg-white border border-rose-50 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.02)]">
                    <div class="flex items-center gap-2 pb-4 mb-4 border-b border-rose-50">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <h4 class="font-bold text-sm text-rose-700 uppercase tracking-wider">Zona Bahaya</h4>
                    </div>
                    <div class="max-w-full">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div> -->
                
            </div>
        </div>
        
    </div>
</x-app-layout>