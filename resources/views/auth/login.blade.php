<x-guest-layout>
    <!-- Kontainer Utama -->
    <div id="auth-container" class="relative w-full max-w-4xl bg-white rounded-[32px] shadow-[0_30px_60px_rgba(0,0,0,0.15)] overflow-hidden flex min-h-[640px]">
        
        <!-- ==========================================
             1. AREA FORM INPUT (SISI KIRI / KANAN)
             ========================================== -->
        <div id="form-side" class="w-full md:w-1/2 h-full absolute top-0 left-0 transition-all duration-700 ease-in-out z-10">
            
            <!-- FORM MASUK (SIGN IN) -->
            <div id="signin-section" class="w-full h-full min-h-[640px] flex flex-col justify-center items-center px-8 py-10 sm:px-16 bg-white transition-all duration-500">
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Log In</h2>

                <span class="text-xs text-slate-400 mt-4 tracking-wide">Gunakan username dan kata sandi Anda</span>
                <x-auth-session-status class="mt-4 text-xs" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="w-full mt-4 space-y-4">
                    @csrf
                    <div>
                        <input type="text" name="username" :value="old('username')" required autofocus placeholder="Username" class="w-full px-4 py-3 bg-slate-100 border-none rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-600/20 transition-all text-slate-800" />
                        <x-input-error :messages="$errors->get('username')" class="mt-1 text-xs" />
                    </div>
                    <div>
                        <input type="password" name="password" required placeholder="Kata Sandi" class="w-full px-4 py-3 bg-slate-100 border-none rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-600/20 transition-all text-slate-800" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                    </div>
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-slate-800 focus:ring-slate-700 w-3.5 h-3.5">
                            <span class="ml-1.5 text-slate-500 font-medium">Ingat saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-slate-500 hover:text-slate-800 font-medium transition hover:underline" href="{{ route('password.request') }}">Lupa Kata Sandi?</a>
                        @endif
                    </div>
                    <div class="flex justify-center pt-2">
                        <button type="submit" class="bg-slate-800 text-white text-xs font-bold tracking-wider uppercase px-12 py-3.5 rounded-xl shadow-lg shadow-slate-900/20 hover:bg-slate-900 active:scale-95 transition-all duration-150">Masuk</button>
                    </div>
                </form>
                <!-- Navigasi khusus Mobile -->
                <p class="mt-8 text-xs text-slate-500 md:hidden">Belum punya akun? <button type="button" onclick="toggleAuthMode(true)" class="text-slate-800 font-bold ml-1 hover:underline">Daftar Sekarang</button></p>
            </div>

            <!-- FORM DAFTAR (SIGN UP) -->
            <div id="signup-section" class="w-full h-full min-h-[640px] hidden flex-col justify-center items-center px-8 py-10 sm:px-16 bg-white transition-all duration-500">
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Buat Akun Baru</h2>

                <span class="text-xs text-slate-400 mt-4 tracking-wide">atau gunakan email untuk registrasi</span>

                <form method="POST" action="{{ route('register') }}" class="w-full mt-4 space-y-3.5">
                    @csrf
                    <div>
                        <input type="text" name="nama" :value="old('nama')" required placeholder="Nama Lengkap" class="w-full px-4 py-3 bg-slate-100 border-none rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-600/20 transition-all text-slate-800" />
                        <x-input-error :messages="$errors->get('nama')" class="mt-1 text-xs" />
                    </div>
                    <div>
                        <input type="text" name="username" :value="old('username')" required placeholder="Username" class="w-full px-4 py-3 bg-slate-100 border-none rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-600/20 transition-all text-slate-800" />
                        <x-input-error :messages="$errors->get('username')" class="mt-1 text-xs" />
                    </div>
                    <div>
                        <input type="email" name="email" :value="old('email')" required placeholder="Alamat Email" class="w-full px-4 py-3 bg-slate-100 border-none rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-600/20 transition-all text-slate-800" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                    </div>
                    <div>
                        <input type="tel" name="no_telpon" :value="old('no_telpon')" required placeholder="Nomor Telepon" class="w-full px-4 py-3 bg-slate-100 border-none rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-600/20 transition-all text-slate-800" />
                        <x-input-error :messages="$errors->get('no_telpon')" class="mt-1 text-xs" />
                    </div>
                    <div>
                        <input type="password" name="password" required placeholder="Kata Sandi" class="w-full px-4 py-3 bg-slate-100 border-none rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-600/20 transition-all text-slate-800" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                    </div>
                    <div>
                        <input type="password" name="password_confirmation" required placeholder="Konfirmasi Kata Sandi" class="w-full px-4 py-3 bg-slate-100 border-none rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-slate-600/20 transition-all text-slate-800" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
                    </div>
                    <div class="flex justify-center pt-2">
                        <button type="submit" class="bg-slate-800 text-white text-xs font-bold tracking-wider uppercase px-12 py-3.5 rounded-xl shadow-lg shadow-slate-900/20 hover:bg-slate-900 active:scale-95 transition-all duration-150">Daftar</button>
                    </div>
                </form>
                <!-- Navigasi khusus Mobile -->
                <p class="mt-8 text-xs text-slate-500 md:hidden">Sudah punya akun? <button type="button" onclick="toggleAuthMode(false)" class="text-slate-800 font-bold ml-1 hover:underline">Masuk Di Sini</button></p>
            </div>
        </div>

        <!-- ==========================================
             2. PANEL VISUAL ABU GELAP (DESKTOP ONLY)
             ========================================== -->
        <div id="overlay-container" class="hidden md:block absolute top-0 left-1/2 w-1/2 h-full overflow-hidden transition-all duration-700 ease-in-out z-30 rounded-l-[150px]">
            <div id="overlay-content" class="absolute top-0 left-0 w-[200%] h-full bg-gradient-to-br from-slate-800 to-slate-900 text-white transition-all duration-700 ease-in-out flex">
                
                <!-- OVERLAY KIRI (Dipicu saat form Masuk aktif -> Menawarkan tombol DAFTAR) -->
                <div class="w-1/2 h-full flex flex-col justify-center items-center text-center p-12">
                    <h2 class="text-3xl font-bold tracking-tight">Selamat Datang Kembali!</h2>
                    <p class="text-xs text-slate-300/90 max-w-[240px] mt-4 leading-relaxed">Silakan masuk kembali menggunakan akun Anda untuk tetap terhubung bersama kami</p>
                    <div class="pt-6">
                        <button type="button" onclick="toggleAuthMode(true)" class="border border-white text-white text-xs font-bold tracking-wider uppercase px-12 py-3 rounded-xl hover:bg-white hover:text-slate-900 active:scale-95 transition shadow-md">
                            Daftar
                        </button>
                    </div>
                </div>

                <!-- OVERLAY KANAN (Dipicu saat form Daftar aktif -> Menawarkan tombol MASUK) -->
                <div class="w-1/2 h-full flex flex-col justify-center items-center text-center p-12">
                    <h2 class="text-3xl font-bold tracking-tight">Halo, Teman!</h2>
                    <p class="text-xs text-slate-300/90 max-w-[240px] mt-4 leading-relaxed">Daftarkan data diri Anda untuk menikmati seluruh fitur layanan yang tersedia</p>
                    <div class="pt-6">
                        <button type="button" onclick="toggleAuthMode(false)" class="border border-white text-white text-xs font-bold tracking-wider uppercase px-12 py-3 rounded-xl hover:bg-white hover:text-slate-900 active:scale-95 transition shadow-md">
                            Masuk
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- ==========================================
         JAVASCRIPT STATE CONTROLLER
         ========================================== -->
    <script>
        function toggleAuthMode(isSignUp) {
            const formSide = document.getElementById('form-side');
            const overlayContainer = document.getElementById('overlay-container');
            const overlayContent = document.getElementById('overlay-content');
            
            const signinSection = document.getElementById('signin-section');
            const signupSection = document.getElementById('signup-section');

            const isMobile = window.innerWidth < 768;

            if (isSignUp) {
                if (!isMobile) {
                    formSide.style.transform = 'translateX(100%)';
                    overlayContainer.style.transform = 'translateX(-100%)';
                    overlayContainer.classList.remove('rounded-l-[150px]');
                    overlayContainer.classList.add('rounded-r-[150px]');
                    overlayContent.style.transform = 'translateX(-50%)';
                }
                signinSection.classList.add('hidden');
                signinSection.classList.remove('flex');
                
                signupSection.classList.add('flex');
                signupSection.classList.remove('hidden');
            } else {
                if (!isMobile) {
                    formSide.style.transform = 'translateX(0)';
                    overlayContainer.style.transform = 'translateX(0)';
                    overlayContainer.classList.remove('rounded-r-[150px]');
                    overlayContainer.classList.add('rounded-l-[150px]');
                    overlayContent.style.transform = 'translateX(0)';
                }
                signinSection.classList.add('flex');
                signinSection.classList.remove('hidden');
                
                signupSection.classList.add('hidden');
                signupSection.classList.remove('flex');
            }
        }

        @if($errors->has('nama') || $errors->has('username') || $errors->has('no_telpon') || (old('nama') && !$errors->has('email')))
            window.addEventListener('DOMContentLoaded', () => toggleAuthMode(true));
        @endif
    </script>
</x-guest-layout>