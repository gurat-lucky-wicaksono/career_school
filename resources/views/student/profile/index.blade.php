@extends('layouts.student')

@section('title', 'Keahlian & Profil Siswa')

@section('content')
<div class="space-y-6">
    <!-- 1. Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Keahlian & Profil Siswa
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola data diri, kompetensi kejuruan mandiri, dan pantau status validasi resmi oleh guru pengampu.
            </p>
        </div>
    </div>

    <!-- 2. Main 2-Column Bento Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Biodata Siswa & Akademik (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Informasi Akademik Terverifikasi (Read-only) -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="pb-3 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            Data Akademik SMKN 1 Cirebon
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Data sinkronisasi resmi dari Bagian Kurikulum & Kesiswaan.
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60">
                        <i data-lucide="shield-check" class="w-3 h-3"></i>
                        <span>Siswa Aktif</span>
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">NISN</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white text-xs mt-0.5 block">
                            {{ $student->nisn }}
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Rombel / Kelas</span>
                        <span class="font-bold text-slate-900 dark:text-white text-xs mt-0.5 block">
                            {{ $student->schoolClass?->nama ?? 'PPLG' }}
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Tahun Angkatan</span>
                        <span class="font-bold text-slate-900 dark:text-white text-xs mt-0.5 block">
                            {{ $student->angkatan ?? '2024' }}
                        </span>
                    </div>

                    <div class="col-span-2 sm:col-span-3 p-3 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Program Keahlian</span>
                        <span class="font-semibold text-slate-900 dark:text-white text-xs mt-0.5 block">
                            {{ $student->major?->nama ?? 'Pengembangan Perangkat Lunak & Gim' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Form Edit Biodata Pribadi -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-5">
                <div class="pb-3 border-b border-slate-100 dark:border-white/5">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                        Biodata Pribadi Siswa
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Informasi kontak ini ditampilkan kepada tim HRD saat Anda mengajukan lamaran.
                    </p>
                </div>

                <form method="POST" action="{{ route('student.profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Nama Lengkap -->
                    <div class="space-y-1">
                        <label for="name" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $user->name) }}" 
                            required 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                        >
                    </div>

                    <!-- Bio / Deskripsi Singkat -->
                    <div class="space-y-1">
                        <label for="bio" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                            Bio Ringkas Profil
                        </label>
                        <textarea 
                            name="bio" 
                            id="bio" 
                            rows="3" 
                            maxlength="1000"
                            placeholder="Tuliskan minat kejuruan, fokus spesialisasi, atau aspirasi karier vokasi Anda..." 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all resize-none"
                        >{{ old('bio', $user->profile?->bio) }}</textarea>
                    </div>

                    <!-- Telepon & Kota Domisili -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="telepon" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                                Nomor Telepon / WhatsApp
                            </label>
                            <input 
                                type="text" 
                                name="telepon" 
                                id="telepon" 
                                value="{{ old('telepon', $user->profile?->telepon) }}" 
                                placeholder="08xxxxxxxxxx" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                            >
                        </div>

                        <div class="space-y-1">
                            <label for="kota" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                                Kota Domisili
                            </label>
                            <input 
                                type="text" 
                                name="kota" 
                                id="kota" 
                                value="{{ old('kota', $user->profile?->kota ?? 'Cirebon') }}" 
                                placeholder="Kota Cirebon" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                            >
                        </div>
                    </div>

                    <!-- Jenis Kelamin & Tanggal Lahir -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="jenis_kelamin" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                                Jenis Kelamin
                            </label>
                            <select 
                                name="jenis_kelamin" 
                                id="jenis_kelamin" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                            >
                                <option value="">Pilih Jenis Kelamin...</option>
                                <option value="L" {{ old('jenis_kelamin', $user->profile?->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin', $user->profile?->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label for="tanggal_lahir" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                                Tanggal Lahir
                            </label>
                            <input 
                                type="date" 
                                name="tanggal_lahir" 
                                id="tanggal_lahir" 
                                value="{{ old('tanggal_lahir', $user->profile?->tanggal_lahir ? $user->profile->tanggal_lahir->format('Y-m-d') : '') }}" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                            >
                        </div>
                    </div>

                    <!-- Form Submit -->
                    <div class="pt-3 border-t border-slate-100 dark:border-white/5 flex justify-end">
                        <x-ui.button variant="primary" size="md" type="submit">
                            <i data-lucide="check" class="w-4 h-4 mr-1.5"></i>
                            Simpan Perubahan Biodata
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Kelola Keahlian Vokasi (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Tambah Keahlian Baru Card -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="pb-3 border-b border-slate-100 dark:border-white/5">
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            Tambah Keahlian Vokasi
                        </h2>
                        <span class="px-2 py-0.5 rounded-full bg-brand-primary/10 text-brand-primary text-[10px] font-bold">
                            Bobot 60%
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Pilih keahlian kompetensi yang telah Anda pelajari di sekolah.
                    </p>
                </div>

                <form method="POST" action="{{ route('student.profile.skills.add') }}" class="space-y-3">
                    @csrf

                    <div class="space-y-1">
                        <label for="skill_id" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                            Pilih Keahlian
                        </label>
                        <select 
                            name="skill_id" 
                            id="skill_id" 
                            required 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                        >
                            <option value="">Pilih kompetensi keahlian...</option>
                            @foreach ($availableSkills as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->kategori }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label for="tingkat" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                            Tingkat Kemahiran (1-5)
                        </label>
                        <select 
                            name="tingkat" 
                            id="tingkat" 
                            required 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                        >
                            <option value="1">Level 1 - Pemula (Dasar Konsep)</option>
                            <option value="2">Level 2 - Menengah Awal (Mampu Menjalankan Tugas)</option>
                            <option value="3" selected>Level 3 - Terampil (Mandiri Membangun Fitur)</option>
                            <option value="4">Level 4 - Mahir (Mendalam & Optimasi)</option>
                            <option value="5">Level 5 - Sangat Mahir (Standar Industri)</option>
                        </select>
                    </div>

                    <div class="pt-2">
                        <x-ui.button variant="primary" size="sm" type="submit" class="w-full justify-center">
                            <i data-lucide="plus" class="w-3.5 h-3.5 mr-1.5"></i>
                            Tambahkan ke Profil
                        </x-ui.button>
                    </div>
                </form>
            </div>

            <!-- Daftar Keahlian Terdaftar Card -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/5">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            Keahlian Terdaftar ({{ $student->skills->count() }})
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Daftar kompetensi yang siap ditinjau guru kejuruan.
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse ($student->skills as $skill)
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="font-bold text-xs text-slate-900 dark:text-white truncate">
                                        {{ $skill->nama }}
                                    </span>
                                    @if ($skill->pivot->tervalidasi)
                                        <span title="Divalidasi Guru Pengampu" class="text-emerald-600 dark:text-emerald-400 flex-shrink-0">
                                            <i data-lucide="badge-check" class="w-3.5 h-3.5"></i>
                                        </span>
                                    @endif
                                </div>

                                <form method="POST" action="{{ route('student.profile.skills.remove', $skill) }}" onsubmit="return confirm('Hapus keahlian {{ $skill->nama }} dari profil Anda?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors p-1" title="Hapus Keahlian">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                                <span>Kemahiran: <strong>Level {{ $skill->pivot->tingkat ?? 3 }}/5</strong></span>
                                @if ($skill->pivot->tervalidasi)
                                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                        Tervalidasi
                                    </span>
                                @else
                                    <span class="text-amber-600 dark:text-amber-400 font-medium flex items-center gap-1">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        Menunggu Validasi Guru
                                    </span>
                                @endif
                            </div>

                            <div class="w-full bg-slate-200 dark:bg-neutral-700 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-brand-primary h-1.5 rounded-full" style="width: {{ (($skill->pivot->tingkat ?? 3) / 5) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            Belum ada keahlian yang ditambahkan ke profil Anda.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
