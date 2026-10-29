@extends('layouts.landing')

@section('title', 'Jejak Alumni: ' . ($alumni->user->name ?? 'SMKN 1 Cirebon'))

@section('content')
<div class="py-12 sm:py-16 bg-surface-bg min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Back Link -->
        <div>
            <a
                href="{{ route('alumni.index') }}"
                class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors"
            >
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Direktori Alumni</span>
            </a>
        </div>

        <!-- Main Profile Card -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-3xl p-6 sm:p-8 shadow-sm space-y-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 pb-6 border-b border-slate-100 dark:border-white/5">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-brand-primary/10 text-brand-secondary dark:text-brand-primary border border-brand-primary/20 flex items-center justify-center font-bold text-2xl shrink-0">
                        {{ strtoupper(substr($alumni->user->name ?? 'A', 0, 2)) }}
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                                {{ $alumni->user->name ?? 'Alumni SMKN 1 Cirebon' }}
                            </h1>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                            Angkatan Lulus {{ $alumni->tahun_lulus }} &bull; Konsentrasi Keahlian {{ $alumni->major->nama ?? 'PPLG' }}
                        </p>
                    </div>
                </div>

                <div class="shrink-0">
                    @if($alumni->status_bmw === 'bekerja')
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <i data-lucide="briefcase" class="w-3.5 h-3.5"></i>
                            <span>Bekerja di Industri</span>
                        </span>
                    @elseif($alumni->status_bmw === 'kuliah')
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                            <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i>
                            <span>Melanjutkan Kuliah</span>
                        </span>
                    @elseif($alumni->status_bmw === 'wirausaha')
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
                            <i data-lucide="rocket" class="w-3.5 h-3.5"></i>
                            <span>Wirausaha / Freelance</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                            <i data-lucide="search" class="w-3.5 h-3.5"></i>
                            <span>Mencari Kerja</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-slate-50 dark:bg-white/[0.02] border border-slate-100 dark:border-white/5 rounded-2xl p-4 space-y-1">
                    <span class="text-xs text-slate-400 block">Posisi / Jabatan / Prodi</span>
                    <span class="text-sm sm:text-base font-bold text-slate-900 dark:text-white block">
                        {{ $alumni->posisi_studi ?: ($alumni->pekerjaan ?: '-') }}
                    </span>
                </div>

                <div class="bg-slate-50 dark:bg-white/[0.02] border border-slate-100 dark:border-white/5 rounded-2xl p-4 space-y-1">
                    <span class="text-xs text-slate-400 block">Nama Instansi / Perusahaan / Kampus</span>
                    <span class="text-sm sm:text-base font-bold text-slate-900 dark:text-white block">
                        {{ $alumni->nama_instansi ?: ($alumni->perusahaan ?: '-') }}
                    </span>
                </div>

                <div class="bg-slate-50 dark:bg-white/[0.02] border border-slate-100 dark:border-white/5 rounded-2xl p-4 space-y-1">
                    <span class="text-xs text-slate-400 block">Kesesuaian dengan Bidang Kejuruan</span>
                    <span class="text-sm sm:text-base font-bold text-slate-900 dark:text-white block">
                        {{ $alumni->keselarasan_label }}
                    </span>
                </div>

                <div class="bg-slate-50 dark:bg-white/[0.02] border border-slate-100 dark:border-white/5 rounded-2xl p-4 space-y-1">
                    <span class="text-xs text-slate-400 block">Masa Tunggu Mendapat Peluang</span>
                    <span class="text-sm sm:text-base font-bold text-slate-900 dark:text-white block">
                        {{ $alumni->waktu_tunggu_bulan !== null ? $alumni->waktu_tunggu_bulan . ' Bulan' : 'Langsung Terserap' }}
                    </span>
                </div>
            </div>

            <!-- Testimonial Section -->
            @if($alumni->testimoni)
                <div class="space-y-3 pt-4 border-t border-slate-100 dark:border-white/5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Pesan & Testimoni Alumni
                    </h3>
                    <div class="bg-brand-primary/5 border border-brand-primary/20 rounded-2xl p-5 text-slate-700 dark:text-slate-300 text-sm italic leading-relaxed">
                        &ldquo;{{ $alumni->testimoni }}&rdquo;
                    </div>
                </div>
            @endif

            <!-- Footer Action Links -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-white/5">
                <a
                    href="{{ route('alumni.index') }}"
                    class="px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors"
                >
                    Kembali
                </a>

                @if($alumni->user && $alumni->user->username)
                    <a
                        href="{{ route('community.profile', $alumni->user->username) }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-primary text-slate-950 font-bold text-xs sm:text-sm shadow hover:brightness-105 transition-all"
                    >
                        <i data-lucide="user" class="w-4 h-4"></i>
                        <span>Lihat Profil di Komunitas</span>
                    </a>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
