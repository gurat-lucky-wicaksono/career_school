@extends('layouts.auth')

@section('title', 'Masuk ke Akun')

@section('fullscreen')
<main class="flex-1 flex items-center justify-center py-8 sm:py-12 lg:py-16">
    <div class="max-w-7xl mx-auto w-full px-6 sm:px-8 lg:px-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
            
            <!-- SISI KIRI: Formulir Masuk (Tanpa Kotak Card Besar, Full Natural Surface) -->
            <div class="lg:col-span-6 xl:col-span-5 space-y-6">
                
                <!-- Judul dengan Wavy Underline Khas Career School -->
                <div class="space-y-3">
                    <h1 class="font-heading text-3xl sm:text-4xl lg:text-[42px] font-bold text-text-primary tracking-tight leading-[1.25]">
                        Selamat Datang <x-wavy-underline>Kembali!</x-wavy-underline>
                    </h1>
                    <p class="text-sm sm:text-base text-text-secondary leading-relaxed">
                        Masuk ke akun <span class="font-semibold text-text-primary">Career School SMKN 1 Cirebon</span> untuk mengelola portofolio kejuruan, validasi guru, dan peluang kerja industri.
                    </p>
                </div>

                <!-- Notifikasi & Alert Validasi -->
                @if (session('success'))
                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs sm:text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="p-4 rounded-xl bg-brand-primary/10 border border-brand-primary/20 text-text-primary text-xs sm:text-sm">
                        {{ session('info') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-xs sm:text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Formulir Masuk -->
                <form method="POST" action="{{ route('login.submit') }}" class="space-y-4 pt-1">
                    @csrf

                    <!-- Input Email atau Username -->
                    <div class="space-y-1.5">
                        <label for="login" class="block text-xs font-semibold text-text-primary">
                            Email atau Username
                        </label>
                        <input
                            type="text"
                            id="login"
                            name="login"
                            value="{{ old('login') }}"
                            required
                            autofocus
                            placeholder="Masukkan email atau username"
                            class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
                        >
                    </div>

                    <!-- Input Kata Sandi dengan Animasi Ikon Mata -->
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-semibold text-text-primary">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                placeholder="Masukkan kata sandi"
                                class="w-full pl-5 pr-12 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
                            >
                            <!-- Tombol Show/Hide Mata dengan Animasi Rotasi & Transisi -->
                            <button
                                type="button"
                                id="toggle-password-btn"
                                onclick="togglePasswordVisibility('password', this)"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-text-secondary hover:text-text-primary transition-colors focus:outline-none"
                                aria-label="Tampilkan atau sembunyikan kata sandi"
                            >
                                <span id="eye-icon-open" class="inline-flex transition-all duration-300 ease-out transform scale-100 rotate-0 opacity-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </span>
                                <span id="eye-icon-closed" class="hidden transition-all duration-300 ease-out transform scale-75 -rotate-45 opacity-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Baris Ingat Saya & Lupa Kata Sandi -->
                    <div class="flex items-center justify-between px-1 text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                name="remember"
                                id="remember"
                                {{ old('remember') ? 'checked' : '' }}
                                class="rounded border-border-default text-brand-primary focus:ring-brand-primary w-4 h-4 bg-surface-bg"
                            >
                            <span class="text-text-secondary">
                                Ingat saya
                            </span>
                        </label>
                        <a
                            href="javascript:void(0)"
                            onclick="alert('Untuk bantuan atur ulang kata sandi akun sekolah atau mitra, silakan menghubungi tim Admin BKK SMK Negeri 1 Cirebon.')"
                            class="text-text-secondary hover:text-text-primary transition-colors font-medium"
                        >
                            Lupa Kata Sandi?
                        </a>
                    </div>

                    <!-- Tombol Masuk Utama (Warna Brand System) -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            class="w-full py-3.5 px-6 rounded-full bg-brand-primary text-text-on-brand-primary font-semibold text-sm hover:brightness-105 transition-all duration-150 active:scale-[0.98] shadow-md shadow-brand-primary/20"
                        >
                            Masuk Sekarang
                        </button>
                    </div>

                    <!-- Langsung Teks Daftar Akun (Tanpa Ikon Google/Apple/SSO) -->
                    <div class="text-center pt-3">
                        <p class="text-xs sm:text-sm text-text-secondary">
                            Belum memiliki akun terdaftar?
                            <a href="{{ route('register') }}" class="font-semibold text-text-primary underline hover:text-brand-primary transition-colors ml-1">
                                Aktivasi NISN atau Daftar sekarang
                            </a>
                        </p>
                    </div>

                </form>

            </div>

            <!-- SISI KANAN: Grafis Bersih (Hanya Asset & Teks Bahasa Indonesia, Tanpa Background Card / Popup) -->
            <div class="lg:col-span-6 xl:col-span-7 flex flex-col items-center justify-center text-center lg:pl-6 py-6 lg:py-0">
                
                <!-- Aset Ilustrasi Vektor Figma Reaktif -->
                <div class="w-full max-w-[380px] sm:max-w-[440px] lg:max-w-[480px] h-auto flex items-center justify-center text-text-primary">
                    <x-illustration name="work-balance" class="w-full h-auto text-text-primary" />
                </div>

                <!-- Teks Inspiratif Bahasa Indonesia Sesuai Warna Sistem -->
                <div class="space-y-3 pt-6 max-w-md">
                    <h2 class="font-heading text-2xl sm:text-3xl font-bold text-text-primary tracking-tight leading-snug">
                        Wujudkan Karier Impian dari <x-highlight-pill>Karya Nyata</x-highlight-pill>
                    </h2>
                    <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                        Portofolio digital resmi SMK Negeri 1 Cirebon dengan validasi guru kejuruan dan pencocokan cerdas mitra industri DUDI.
                    </p>
                </div>

            </div>

        </div>
    </div>
</main>

<!-- Interactive Script for Password Toggle Animation -->
<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const openIcon = document.getElementById('eye-icon-open');
        const closedIcon = document.getElementById('eye-icon-closed');

        if (!input || !openIcon || !closedIcon) return;

        if (input.type === 'password') {
            input.type = 'text';

            // Animasi transisi: Open icon mengecil dan berputar keluar
            openIcon.classList.remove('scale-100', 'rotate-0', 'opacity-100');
            openIcon.classList.add('scale-75', 'rotate-45', 'opacity-0');

            setTimeout(() => {
                openIcon.classList.add('hidden');
                closedIcon.classList.remove('hidden');

                // Closed icon berputar masuk dan membesar halus
                setTimeout(() => {
                    closedIcon.classList.remove('scale-75', '-rotate-45', 'opacity-0');
                    closedIcon.classList.add('scale-100', 'rotate-0', 'opacity-100');
                }, 10);
            }, 120);

        } else {
            input.type = 'password';

            // Animasi transisi: Closed icon mengecil dan berputar keluar
            closedIcon.classList.remove('scale-100', 'rotate-0', 'opacity-100');
            closedIcon.classList.add('scale-75', '-rotate-45', 'opacity-0');

            setTimeout(() => {
                closedIcon.classList.add('hidden');
                openIcon.classList.remove('hidden');

                // Open icon berputar masuk dan membesar halus
                setTimeout(() => {
                    openIcon.classList.remove('scale-75', 'rotate-45', 'opacity-0');
                    openIcon.classList.add('scale-100', 'rotate-0', 'opacity-100');
                }, 10);
            }, 120);
        }
    }
</script>
@endsection
