<x-guest-layout>
    <div class="w-full max-w-md bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm p-8 sm:p-10 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/20 dark:border-zinc-800 transition-all duration-300">
        
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 mb-4 ring-8 ring-indigo-50/50 dark:ring-indigo-950/20">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.249-8.25-3.286Zm0 0v0Z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-zinc-100 mb-2">
                {{ __('Verifikasi OTP') }}
            </h2>
            <p class="text-sm leading-relaxed text-gray-500 dark:text-zinc-400 px-2">
                {{ __('Masukkan 6 digit kode keamanan yang Anda dapatkan dari Super Admin.') }}
            </p>
        </div>

        <div class="mb-6 flex items-center justify-between p-3.5 bg-zinc-50/80 dark:bg-zinc-800/50 rounded-xl border border-gray-100 dark:border-zinc-700/50 text-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-zinc-500">{{ __('Email Tujuan:') }}</span>
            <span class="text-gray-900 dark:text-zinc-100 font-semibold truncate max-w-[200px]">{{ $email }}</span>
        </div>

        <form method="POST" action="{{ route('password.otp.verify') }}" class="space-y-6"
              x-data="{ 
                otpValues: ['', '', '', '', '', ''],
                handleInput(e, index) {
                    let val = e.target.value.replace(/[^0-9]/g, ''); // Hanya izinkan angka
                    this.otpValues[index] = val.substring(val.length - 1);
                    
                    // Auto focus ke box selanjutnya
                    if (this.otpValues[index] !== '' && index < 5) {
                        this.$refs['input' + (index + 1)].focus();
                    }
                    this.syncHiddenInput();
                },
                handleKeyDown(e, index) {
                    // Backspace untuk kembali ke box sebelumnya
                    if (e.key === 'Backspace' && this.otpValues[index] === '' && index > 0) {
                        this.$refs['input' + (index - 1)].focus();
                    }
                },
                handlePaste(e) {
                    e.preventDefault();
                    let pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').substring(0, 6);
                    for (let i = 0; i < pasteData.length; i++) {
                        this.otpValues[i] = pasteData[i];
                    }
                    this.syncHiddenInput();
                    // Focus ke box terakhir yang terisi
                    let nextFocus = Math.min(pasteData.length, 5);
                    this.$refs['input' + nextFocus].focus();
                },
                syncHiddenInput() {
                    this.$refs.hiddenOtp.value = this.otpValues.join('');
                }
              }">
            
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            
            <input type="hidden" id="otp" name="otp" x-ref="hiddenOtp" required>

            <div class="space-y-2">
                <label class="text-xs font-semibold uppercase tracking-wider block text-center text-gray-500 dark:text-zinc-400 mb-3">
                    {{ __('Kode Akses') }}
                </label>
                
                <div class="flex justify-between gap-2 sm:gap-3" @paste="handlePaste($event)">
                    <template x-for="(value, index) in otpValues" :key="index">
                        <input 
                            type="text" 
                            maxlength="1"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            :x-ref="'input' + index"
                            :value="value"
                            @input="handleInput($event, index)"
                            @keydown="handleKeyDown($event, index)"
                            class="w-12 h-14 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-bold bg-gray-50/50 dark:bg-zinc-800/50 border border-gray-200 dark:border-zinc-700/80 rounded-xl focus:bg-white dark:focus:bg-zinc-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-150 shadow-inner"
                            required
                        />
                    </template>
                </div>
                <x-input-error :messages="$errors->get('otp')" class="mt-3 text-xs font-medium text-center text-red-500" />
            </div>

            <div class="space-y-4 pt-2">
                <button type="submit" class="w-full inline-flex justify-center bg-slate-800 text-white text-xs font-bold tracking-wider uppercase px-12 py-3.5 rounded-xl shadow-lg shadow-slate-900/20 hover:bg-slate-900 active:scale-95 transition-all duration-150">
                    {{ __('Verifikasi & Lanjutkan') }}
                </button>
                
                <div class="text-center">
                    <a href="{{ route('password.request') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300 transition-colors group">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 transform group-hover:-translate-x-0.5 transition-transform">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7M3 12h18" />
                        </svg>
                        {{ __('Kembali') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>