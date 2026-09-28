@extends('layouts.auth')

@section('title', 'Pendaftaran Akun Baru')

@section('fullscreen')
<main class="flex-1 flex items-center justify-center py-8 sm:py-12 lg:py-16">
    <div class="max-w-7xl mx-auto w-full px-6 sm:px-8 lg:px-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
            
            <!-- SISI KIRI: Pilihan Peran & Formulir Pendaftaran (Gaya Presisi Sesuai Login Page) -->
            <div class="lg:col-span-6 xl:col-span-6 space-y-6">
                
                <!-- Judul Utama dengan Wavy Underline Khas Career School -->
                <div class="space-y-3">
                    <h1 class="font-heading text-3xl sm:text-4xl lg:text-[42px] font-bold text-text-primary tracking-tight leading-[1.25]">
                        Pendaftaran <x-wavy-underline>Akun Baru</x-wavy-underline>
                    </h1>
                    <p class="text-sm sm:text-base text-text-secondary leading-relaxed">
                        Pilih jenis pendaftaran Anda untuk memulai aktivasi atau pendaftaran di portal resmi <span class="font-semibold text-text-primary">Career School SMKN 1 Cirebon</span>.
                    </p>
                </div>

                <!-- Error Alert Feedback -->
                @if ($errors->any())
                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-xs sm:text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @php
                    $activeRole = old('role_type', request('role') ?? '');
                @endphp

                <!-- TAHAP 1: KARTU PILIHAN PERAN (Bersih, Tanpa Ikon / Emote Sesuai Permintaan) -->
                <div id="role-selection-stage" class="space-y-3.5 {{ $activeRole ? 'hidden' : '' }}">
                    <p class="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                        Pilih Jenis Pendaftaran Akun:
                    </p>

                    <!-- Pilihan 1: Siswa -->
                    <button
                        type="button"
                        onclick="selectRole('siswa')"
                        class="w-full p-4 sm:p-5 rounded-2xl bg-surface-bg border border-border-default hover:border-brand-primary text-left transition-all duration-200 group shadow-xs hover:shadow-md space-y-1.5"
                    >
                        <div class="flex items-center justify-between">
                            <h3 class="font-heading text-base sm:text-lg font-bold text-text-primary group-hover:text-brand-primary transition-colors">
                                Siswa
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-brand-primary/15 text-text-primary text-[10px] sm:text-[11px] font-semibold">
                                Aktivasi Siswa (NISN)
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                            Untuk siswa aktif SMKN 1 Cirebon. Gunakan 10 digit NISN resmi untuk aktivasi portofolio kejuruan dan melamar kerja.
                        </p>
                    </button>

                    <!-- Pilihan 2: Guru Kejuruan -->
                    <button
                        type="button"
                        onclick="selectRole('guru')"
                        class="w-full p-4 sm:p-5 rounded-2xl bg-surface-bg border border-border-default hover:border-brand-primary text-left transition-all duration-200 group shadow-xs hover:shadow-md space-y-1.5"
                    >
                        <div class="flex items-center justify-between">
                            <h3 class="font-heading text-base sm:text-lg font-bold text-text-primary group-hover:text-brand-primary transition-colors">
                                Guru Kejuruan
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-brand-primary/15 text-text-primary text-[10px] sm:text-[11px] font-semibold">
                                Tenaga Pendidik
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                            Untuk guru mata pelajaran kejuruan. Validasi kompetensi siswa, bimbing portofolio keahlian, dan pantau penyaluran lulusan.
                        </p>
                    </button>

                    <!-- Pilihan 3: Mitra Perusahaan -->
                    <button
                        type="button"
                        onclick="selectRole('perusahaan')"
                        class="w-full p-4 sm:p-5 rounded-2xl bg-surface-bg border border-border-default hover:border-brand-primary text-left transition-all duration-200 group shadow-xs hover:shadow-md space-y-1.5"
                    >
                        <div class="flex items-center justify-between">
                            <h3 class="font-heading text-base sm:text-lg font-bold text-text-primary group-hover:text-brand-primary transition-colors">
                                Mitra Perusahaan
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-brand-primary/15 text-text-primary text-[10px] sm:text-[11px] font-semibold">
                                Mitra Industri
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                            Untuk instansi dan dunia usaha mitra sekolah. Publikasikan lowongan kerja/magang dan rekrut talenta vokasi siap kerja.
                        </p>
                    </button>
                </div>

                <!-- TAHAP 2: CONTAINER FORMULIR TERPISAH BERDASARKAN PERAN -->
                <div id="forms-container" class="{{ $activeRole ? '' : 'hidden' }}">
                    
                    <!-- Tombol Ganti Peran -->
                    <div class="pb-3 flex items-center justify-between border-b border-border-subtle mb-4">
                        <button
                            type="button"
                            onclick="resetRoleSelection()"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-text-secondary hover:text-text-primary transition-colors group"
                        >
                            <x-icon name="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" />
                            <span>Ganti Pilihan Peran</span>
                        </button>
                        <span id="role-indicator-badge" class="px-2.5 py-0.5 rounded-full bg-brand-primary/15 text-text-primary text-[11px] font-semibold">
                            {{ $activeRole === 'guru' ? 'Pendaftaran Guru Kejuruan' : ($activeRole === 'perusahaan' ? 'Pendaftaran Mitra Perusahaan' : 'Aktivasi Siswa (NISN)') }}
                        </span>
                    </div>

                    <!-- FORM 1: AKTIVASI SISWA (NISN) -->
                    <form
                        id="form-siswa"
                        method="POST"
                        action="{{ route('register.submit') }}"
                        class="space-y-4 {{ $activeRole === 'siswa' ? '' : ($activeRole ? 'hidden' : '') }}"
                    >
                        @csrf
                        <input type="hidden" name="role_type" value="siswa">

                        <!-- NISN -->
                        <div class="space-y-1.5">
                            <label for="nisn" class="block text-xs font-semibold text-text-primary">
                                Nomor Induk Siswa Nasional (NISN)
                            </label>
                            <input
                                type="text"
                                id="nisn"
                                name="nisn"
                                maxlength="10"
                                pattern="[0-9]{10}"
                                value="{{ old('nisn') }}"
                                required
                                placeholder="Masukkan 10 digit NISN"
                                class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                            >
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="space-y-1.5">
                            <label for="name_siswa" class="block text-xs font-semibold text-text-primary">
                                Nama Lengkap Siswa
                            </label>
                            <input
                                type="text"
                                id="name_siswa"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                placeholder="Masukkan nama lengkap siswa"
                                class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                            >
                        </div>

                        <!-- Jurusan & Kelas -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label for="major_id" class="block text-xs font-semibold text-text-primary">
                                    Jurusan
                                </label>
                                <select
                                    id="major_id"
                                    name="major_id"
                                    required
                                    class="w-full choices-select"
                                >
                                    <option value="">Pilih Jurusan</option>
                                    @foreach ($majors as $major)
                                        <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>
                                            {{ $major->kode }} — {{ $major->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label for="class_id" class="block text-xs font-semibold text-text-primary">
                                    Kelas / Rombel
                                </label>
                                <select
                                    id="class_id"
                                    name="class_id"
                                    required
                                    class="w-full choices-select"
                                >
                                    <option value="">Pilih Kelas</option>
                                    @foreach ($classes as $class)
                                        <option
                                            value="{{ $class->id }}"
                                            data-major="{{ $class->major_id }}"
                                            {{ old('class_id') == $class->id ? 'selected' : '' }}
                                        >
                                            {{ $class->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Email & Username -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label for="email_siswa" class="block text-xs font-semibold text-text-primary">
                                    Email Aktif
                                </label>
                                <input
                                    type="email"
                                    id="email_siswa"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    placeholder="Masukkan alamat email"
                                    class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                >
                            </div>
                            <div class="space-y-1.5">
                                <label for="username_siswa" class="block text-xs font-semibold text-text-primary">
                                    Username
                                </label>
                                <input
                                    type="text"
                                    id="username_siswa"
                                    name="username"
                                    value="{{ old('username') }}"
                                    required
                                    placeholder="Masukkan username"
                                    class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                >
                            </div>
                        </div>

                        <!-- Kata Sandi & Konfirmasi -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label for="password_siswa" class="block text-xs font-semibold text-text-primary">
                                    Kata Sandi
                                </label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password_siswa"
                                        name="password"
                                        required
                                        placeholder="Masukkan kata sandi"
                                        class="w-full pl-5 pr-12 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                    >
                                    <button
                                        type="button"
                                        onclick="toggleRegisterPassword('password_siswa', 'eye-siswa-open', 'eye-siswa-closed')"
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-text-secondary hover:text-text-primary transition-colors focus:outline-none"
                                        aria-label="Tampilkan atau sembunyikan kata sandi"
                                    >
                                        <span id="eye-siswa-open" class="inline-flex transition-all duration-300 ease-out transform scale-100 rotate-0 opacity-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </span>
                                        <span id="eye-siswa-closed" class="hidden transition-all duration-300 ease-out transform scale-75 -rotate-45 opacity-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label for="password_siswa_confirmation" class="block text-xs font-semibold text-text-primary">
                                    Ulangi Sandi
                                </label>
                                <input
                                    type="password"
                                    id="password_siswa_confirmation"
                                    name="password_confirmation"
                                    required
                                    placeholder="Konfirmasi kata sandi"
                                    class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                >
                            </div>
                        </div>

                        <!-- Tombol Submit Siswa -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                class="w-full py-3.5 px-6 rounded-full bg-brand-primary text-text-on-brand-primary font-semibold text-sm hover:brightness-105 transition-all duration-150 active:scale-[0.98] shadow-md shadow-brand-primary/20"
                            >
                                Aktifkan Akun Siswa Sekarang
                            </button>
                        </div>
                    </form>

                    <!-- FORM 2: PENDAFTARAN GURU KEJURUAN -->
                    <form
                        id="form-guru"
                        method="POST"
                        action="{{ route('register.submit') }}"
                        class="space-y-4 {{ $activeRole === 'guru' ? '' : 'hidden' }}"
                    >
                        @csrf
                        <input type="hidden" name="role_type" value="guru">

                        <!-- NIP -->
                        <div class="space-y-1.5">
                            <label for="nip" class="block text-xs font-semibold text-text-primary">
                                Nomor Induk Pegawai (NIP)
                            </label>
                            <input
                                type="text"
                                id="nip"
                                name="nip"
                                value="{{ old('nip') }}"
                                required
                                placeholder="Masukkan NIP"
                                class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                            >
                        </div>

                        <!-- Nama Lengkap & Gelar -->
                        <div class="space-y-1.5">
                            <label for="name_guru" class="block text-xs font-semibold text-text-primary">
                                Nama Lengkap dan Gelar
                            </label>
                            <input
                                type="text"
                                id="name_guru"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                placeholder="Masukkan nama lengkap dan gelar"
                                class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                            >
                        </div>

                        <!-- Mata Pelajaran Kejuruan -->
                        <div class="space-y-1.5">
                            <label for="mata_pelajaran" class="block text-xs font-semibold text-text-primary">
                                Mata Pelajaran / Konsentrasi Kejuruan
                            </label>
                            <input
                                type="text"
                                id="mata_pelajaran"
                                name="mata_pelajaran"
                                value="{{ old('mata_pelajaran') }}"
                                required
                                placeholder="Masukkan mata pelajaran kejuruan"
                                class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                            >
                        </div>

                        <!-- Email & Username -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label for="email_guru" class="block text-xs font-semibold text-text-primary">
                                    Email Guru
                                </label>
                                <input
                                    type="email"
                                    id="email_guru"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    placeholder="Masukkan alamat email"
                                    class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                >
                            </div>
                            <div class="space-y-1.5">
                                <label for="username_guru" class="block text-xs font-semibold text-text-primary">
                                    Username
                                </label>
                                <input
                                    type="text"
                                    id="username_guru"
                                    name="username"
                                    value="{{ old('username') }}"
                                    required
                                    placeholder="Masukkan username"
                                    class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                >
                            </div>
                        </div>

                        <!-- Kata Sandi & Konfirmasi -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label for="password_guru" class="block text-xs font-semibold text-text-primary">
                                    Kata Sandi
                                </label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password_guru"
                                        name="password"
                                        required
                                        placeholder="Masukkan kata sandi"
                                        class="w-full pl-5 pr-12 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                    >
                                    <button
                                        type="button"
                                        onclick="toggleRegisterPassword('password_guru', 'eye-guru-open', 'eye-guru-closed')"
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-text-secondary hover:text-text-primary transition-colors focus:outline-none"
                                        aria-label="Tampilkan atau sembunyikan kata sandi"
                                    >
                                        <span id="eye-guru-open" class="inline-flex transition-all duration-300 ease-out transform scale-100 rotate-0 opacity-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </span>
                                        <span id="eye-guru-closed" class="hidden transition-all duration-300 ease-out transform scale-75 -rotate-45 opacity-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label for="password_guru_confirmation" class="block text-xs font-semibold text-text-primary">
                                    Ulangi Sandi
                                </label>
                                <input
                                    type="password"
                                    id="password_guru_confirmation"
                                    name="password_confirmation"
                                    required
                                    placeholder="Konfirmasi kata sandi"
                                    class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                >
                            </div>
                        </div>

                        <!-- Tombol Submit Guru -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                class="w-full py-3.5 px-6 rounded-full bg-brand-primary text-text-on-brand-primary font-semibold text-sm hover:brightness-105 transition-all duration-150 active:scale-[0.98] shadow-md shadow-brand-primary/20"
                            >
                                Daftarkan Akun Guru Sekarang
                            </button>
                        </div>
                    </form>

                    <!-- FORM 3: PENDAFTARAN MITRA PERUSAHAAN -->
                    <form
                        id="form-perusahaan"
                        method="POST"
                        action="{{ route('register.submit') }}"
                        class="space-y-4 {{ $activeRole === 'perusahaan' ? '' : 'hidden' }}"
                    >
                        @csrf
                        <input type="hidden" name="role_type" value="perusahaan">

                        <!-- Nama Perusahaan -->
                        <div class="space-y-1.5">
                            <label for="nama_perusahaan" class="block text-xs font-semibold text-text-primary">
                                Nama Perusahaan / Instansi Industri
                            </label>
                            <input
                                type="text"
                                id="nama_perusahaan"
                                name="nama_perusahaan"
                                value="{{ old('nama_perusahaan') }}"
                                required
                                placeholder="Masukkan nama perusahaan atau instansi"
                                class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                            >
                        </div>

                        <!-- Nama PIC & Bidang Usaha -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label for="name_perusahaan" class="block text-xs font-semibold text-text-primary">
                                    Nama PIC / HRD
                                </label>
                                <input
                                    type="text"
                                    id="name_perusahaan"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    placeholder="Masukkan nama PIC atau HRD"
                                    class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                >
                            </div>
                            <div class="space-y-1.5">
                                <label for="bidang" class="block text-xs font-semibold text-text-primary">
                                    Bidang Usaha
                                </label>
                                <input
                                    type="text"
                                    id="bidang"
                                    name="bidang"
                                    value="{{ old('bidang') }}"
                                    required
                                    placeholder="Masukkan bidang usaha industri"
                                    class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                >
                            </div>
                        </div>

                        <!-- Kota & Telepon -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label for="kota" class="block text-xs font-semibold text-text-primary">
                                    Kota Domisili Kantor
                                </label>
                                <input
                                    type="text"
                                    id="kota"
                                    name="kota"
                                    value="{{ old('kota', 'Cirebon') }}"
                                    placeholder="Masukkan kota domisili kantor"
                                    class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                >
                            </div>
                            <div class="space-y-1.5">
                                <label for="telepon" class="block text-xs font-semibold text-text-primary">
                                    No. Telepon / WhatsApp
                                </label>
                                <input
                                    type="text"
                                    id="telepon"
                                    name="telepon"
                                    value="{{ old('telepon') }}"
                                    placeholder="Masukkan nomor telepon atau WhatsApp"
                                    class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                >
                            </div>
                        </div>

                        <!-- Email & Username -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label for="email_perusahaan" class="block text-xs font-semibold text-text-primary">
                                    Email Resmi
                                </label>
                                <input
                                    type="email"
                                    id="email_perusahaan"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    placeholder="Masukkan alamat email resmi"
                                    class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                >
                            </div>
                            <div class="space-y-1.5">
                                <label for="username_perusahaan" class="block text-xs font-semibold text-text-primary">
                                    Username
                                </label>
                                <input
                                    type="text"
                                    id="username_perusahaan"
                                    name="username"
                                    value="{{ old('username') }}"
                                    required
                                    placeholder="Masukkan username"
                                    class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                >
                            </div>
                        </div>

                        <!-- Kata Sandi & Konfirmasi -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label for="password_perusahaan" class="block text-xs font-semibold text-text-primary">
                                    Kata Sandi
                                </label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password_perusahaan"
                                        name="password"
                                        required
                                        placeholder="Masukkan kata sandi"
                                        class="w-full pl-5 pr-12 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                    >
                                    <button
                                        type="button"
                                        onclick="toggleRegisterPassword('password_perusahaan', 'eye-perusahaan-open', 'eye-perusahaan-closed')"
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-text-secondary hover:text-text-primary transition-colors focus:outline-none"
                                        aria-label="Tampilkan atau sembunyikan kata sandi"
                                    >
                                        <span id="eye-perusahaan-open" class="inline-flex transition-all duration-300 ease-out transform scale-100 rotate-0 opacity-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </span>
                                        <span id="eye-perusahaan-closed" class="hidden transition-all duration-300 ease-out transform scale-75 -rotate-45 opacity-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label for="password_perusahaan_confirmation" class="block text-xs font-semibold text-text-primary">
                                    Ulangi Sandi
                                </label>
                                <input
                                    type="password"
                                    id="password_perusahaan_confirmation"
                                    name="password_confirmation"
                                    required
                                    placeholder="Konfirmasi kata sandi"
                                    class="w-full px-5 py-3.5 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm font-normal transition-all outline-none shadow-xs"
                                >
                            </div>
                        </div>

                        <!-- Catatan Verifikasi -->
                        <div class="text-xs text-text-secondary leading-relaxed bg-surface-bg p-3.5 rounded-2xl border border-border-subtle flex items-start gap-2.5">
                            <x-icon name="info" class="w-4 h-4 text-brand-primary shrink-0 mt-0.5" />
                            <span>Akun mitra perusahaan akan diverifikasi oleh tim Admin BKK SMKN 1 Cirebon sebelum dapat mempublikasikan lowongan.</span>
                        </div>

                        <!-- Tombol Submit Perusahaan -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                class="w-full py-3.5 px-6 rounded-full bg-brand-primary text-text-on-brand-primary font-semibold text-sm hover:brightness-105 transition-all duration-150 active:scale-[0.98] shadow-md shadow-brand-primary/20"
                            >
                                Daftarkan Perusahaan Mitra Sekarang
                            </button>
                        </div>
                    </form>

                </div>

                <!-- Tautan Kembali ke Login (Sama seperti Login Page) -->
                <div class="text-center pt-2">
                    <p class="text-xs sm:text-sm text-text-secondary">
                        Sudah memiliki akun terdaftar?
                        <a href="{{ route('login') }}" class="font-semibold text-text-primary underline hover:text-brand-primary transition-colors ml-1">
                            Masuk ke Akun di sini
                        </a>
                    </p>
                </div>

            </div>

            <!-- SISI KANAN: Grafis Bersih, Jelas & Proporsional dari Figma Catalog (Ecommerce/Notion/Office Club) -->
            <div class="lg:col-span-6 xl:col-span-6 flex flex-col items-center justify-center text-center lg:pl-6 py-6 lg:py-0">
                
                <!-- 1. GRAFIS TAHAP AWAL (PILIH PERAN) - Dari Ecommerce Club: marketing scene -->
                <div id="graphic-default" class="w-full flex flex-col items-center justify-center transition-all duration-300 {{ $activeRole ? 'hidden' : '' }}">
                    <div class="w-full max-w-[340px] sm:max-w-[380px] lg:max-w-[420px] h-auto flex items-center justify-center text-text-primary">
                        <x-illustration name="ecommerce-marketing" class="w-full h-auto text-text-primary" />
                    </div>
                    <div class="space-y-3 pt-6 max-w-md">
                        <h2 class="font-heading text-2xl sm:text-3xl font-bold text-text-primary tracking-tight leading-snug">
                            Mulai Langkah Kariermu Bersama <x-highlight-pill>Career School</x-highlight-pill>
                        </h2>
                        <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                            Satu ekosistem terpadu untuk siswa, guru kejuruan, dan mitra industri SMK Negeri 1 Cirebon.
                        </p>
                    </div>
                </div>

                <!-- 2. GRAFIS TAHAP SISWA - Dari Notion Club: student-coding scene -->
                <div id="graphic-siswa" class="w-full flex flex-col items-center justify-center transition-all duration-300 {{ $activeRole === 'siswa' ? '' : 'hidden' }}">
                    <div class="w-full max-w-[320px] sm:max-w-[360px] lg:max-w-[390px] h-auto flex items-center justify-center text-text-primary">
                        <x-illustration name="student-coding" class="w-full h-auto text-text-primary" />
                    </div>
                    <div class="space-y-3 pt-6 max-w-md">
                        <h2 class="font-heading text-2xl sm:text-3xl font-bold text-text-primary tracking-tight leading-snug">
                            Buktikan Keahlianmu Lewat <x-highlight-pill>Portofolio Nyata</x-highlight-pill>
                        </h2>
                        <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                            Aktivasi akun dengan NISN resmi untuk memamerkan karya terbaikmu, mengasah kompetensi, dan langsung terhubung dengan industri.
                        </p>
                    </div>
                </div>

                <!-- 3. GRAFIS TAHAP GURU - Dari Notion Club: teacher-grading scene -->
                <div id="graphic-guru" class="w-full flex flex-col items-center justify-center transition-all duration-300 {{ $activeRole === 'guru' ? '' : 'hidden' }}">
                    <div class="w-full max-w-[340px] sm:max-w-[390px] lg:max-w-[420px] h-auto flex items-center justify-center text-text-primary">
                        <x-illustration name="teacher-grading" class="w-full h-auto text-text-primary" />
                    </div>
                    <div class="space-y-3 pt-6 max-w-md">
                        <h2 class="font-heading text-2xl sm:text-3xl font-bold text-text-primary tracking-tight leading-snug">
                            Bimbing & Validasi Generasi <x-highlight-pill>Siap Industri</x-highlight-pill>
                        </h2>
                        <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                            Verifikasi capaian portofolio kejuruan siswa, berikan umpan balik profesional, dan pantau keterserapan kerja lulusan.
                        </p>
                    </div>
                </div>

                <!-- 4. GRAFIS TAHAP MITRA PERUSAHAAN - Dari Notion Club: recruit-directly tight scene -->
                <div id="graphic-perusahaan" class="w-full flex flex-col items-center justify-center transition-all duration-300 {{ $activeRole === 'perusahaan' ? '' : 'hidden' }}">
                    <div class="w-full max-w-[280px] sm:max-w-[320px] lg:max-w-[350px] h-auto flex items-center justify-center text-text-primary">
                        <x-illustration name="recruit-directly" class="w-full h-auto text-text-primary" />
                    </div>
                    <div class="space-y-3 pt-6 max-w-md">
                        <h2 class="font-heading text-2xl sm:text-3xl font-bold text-text-primary tracking-tight leading-snug">
                            Temukan Talenta Vokasi <x-highlight-pill>Siap Kerja</x-highlight-pill>
                        </h2>
                        <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                            Jalin kemitraan langsung dengan SMKN 1 Cirebon untuk rekrutmen cepat, program magang, dan penyaluran lulusan kompeten.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</main>

<!-- Interactive Script for Step Switching & Password Animation -->
<script>
    function selectRole(role) {
        document.getElementById('role-selection-stage').classList.add('hidden');
        document.getElementById('forms-container').classList.remove('hidden');

        document.getElementById('form-siswa').classList.add('hidden');
        document.getElementById('form-guru').classList.add('hidden');
        document.getElementById('form-perusahaan').classList.add('hidden');

        // Sembunyikan semua grafis samping kanan
        document.getElementById('graphic-default').classList.add('hidden');
        document.getElementById('graphic-siswa').classList.add('hidden');
        document.getElementById('graphic-guru').classList.add('hidden');
        document.getElementById('graphic-perusahaan').classList.add('hidden');

        const badge = document.getElementById('role-indicator-badge');

        if (role === 'siswa') {
            document.getElementById('form-siswa').classList.remove('hidden');
            document.getElementById('graphic-siswa').classList.remove('hidden');
            if (badge) badge.textContent = 'Aktivasi Siswa (NISN)';
            setTimeout(initRegisterChoices, 20);
        } else if (role === 'guru') {
            document.getElementById('form-guru').classList.remove('hidden');
            document.getElementById('graphic-guru').classList.remove('hidden');
            if (badge) badge.textContent = 'Pendaftaran Guru Kejuruan';
        } else if (role === 'perusahaan') {
            document.getElementById('form-perusahaan').classList.remove('hidden');
            document.getElementById('graphic-perusahaan').classList.remove('hidden');
            if (badge) badge.textContent = 'Pendaftaran Mitra Perusahaan';
        }

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }

    function resetRoleSelection() {
        document.getElementById('forms-container').classList.add('hidden');
        document.getElementById('role-selection-stage').classList.remove('hidden');

        // Kembalikan grafis samping kanan ke grafis awal (ecommerce-notification)
        document.getElementById('graphic-siswa').classList.add('hidden');
        document.getElementById('graphic-guru').classList.add('hidden');
        document.getElementById('graphic-perusahaan').classList.add('hidden');
        document.getElementById('graphic-default').classList.remove('hidden');
    }

    function toggleRegisterPassword(inputId, openIconId, closedIconId) {
        const input = document.getElementById(inputId);
        const openIcon = document.getElementById(openIconId);
        const closedIcon = document.getElementById(closedIconId);

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

    let majorChoices = null;
    let classChoices = null;
    let rawClassOptions = [];

    function initRegisterChoices() {
        const majorEl = document.getElementById('major_id');
        const classEl = document.getElementById('class_id');

        if (!window.Choices || !majorEl || !classEl) return;
        if (majorEl._choicesInstance && classEl._choicesInstance) return;

        if (rawClassOptions.length === 0) {
            rawClassOptions = Array.from(classEl.options).map(opt => ({
                value: opt.value,
                label: opt.text.trim(),
                majorId: opt.getAttribute('data-major') || '',
                selected: opt.selected
            }));
        }

        const commonOptions = {
            searchEnabled: false,
            itemSelectText: '',
            shouldSort: false,
            placeholder: true,
            allowHTML: false,
            position: 'bottom',
        };

        if (!classEl._choicesInstance) {
            classChoices = new Choices(classEl, {
                ...commonOptions,
                placeholderValue: 'Pilih Kelas'
            });
            classEl._choicesInstance = classChoices;
        }

        if (!majorEl._choicesInstance) {
            majorChoices = new Choices(majorEl, {
                ...commonOptions,
                placeholderValue: 'Pilih Jurusan'
            });
            majorEl._choicesInstance = majorChoices;

            majorEl.addEventListener('change', function () {
                syncClassDropdown(this.value);
            });
        }

        // If old input or initial value exists, sync class options
        if (majorEl.value) {
            const initialClassId = '{{ old("class_id", "") }}';
            syncClassDropdown(majorEl.value, initialClassId);
        }
    }

    function syncClassDropdown(selectedMajorId, preselectedValue = '') {
        if (!classChoices) return;

        const filtered = rawClassOptions.filter(item => {
            if (!item.value) return false;
            if (!selectedMajorId) return true;
            return String(item.majorId) === String(selectedMajorId);
        });

        const choices = [
            { value: '', label: 'Pilih Kelas', selected: !preselectedValue, disabled: false }
        ].concat(filtered.map(item => ({
            value: item.value,
            label: item.label,
            selected: preselectedValue ? (String(item.value) === String(preselectedValue)) : false
        })));

        classChoices.clearStore();
        classChoices.setChoices(choices, 'value', 'label', true);
    }

    document.addEventListener('DOMContentLoaded', () => {
        initRegisterChoices();
    });
</script>
@endsection
