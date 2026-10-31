@extends('layouts.admin')

@section('title', 'Pusat Laporan & Ekspor Data BKK')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-white/10">
        <div>
            <h1 class="text-2xl font-bold font-sans tracking-tight text-slate-900 dark:text-white">
                Pusat Laporan & Ekspor Data BKK
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Rekapitulasi penyerapan lulusan, kemitraan DUDI, data Tracer Study, dan dokumen resmi SMKN 1 Cirebon.
            </p>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap items-center gap-2.5">
            <a 
                href="{{ route('admin.reports.print-summary', request()->all()) }}" 
                target="_blank"
                class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-brand-primary text-neutral-900 hover:bg-brand-primary/90 shadow-sm transition-all duration-150"
            >
                <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                <span>Cetak Laporan Resmi (PDF)</span>
            </a>

            <div class="relative" x-data="{ open: false }">
                <button 
                    type="button" 
                    @click="open = !open" 
                    class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-white dark:bg-neutral-800 text-slate-700 dark:text-slate-200 border border-slate-200/80 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-neutral-700 transition-colors shadow-sm"
                >
                    <i data-lucide="download" class="w-4 h-4 mr-2 text-slate-400"></i>
                    <span>Ekspor CSV / Excel</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 ml-1.5 text-slate-400"></i>
                </button>

                <div 
                    x-show="open" 
                    x-cloak 
                    @click.away="open = false" 
                    class="absolute right-0 mt-2 w-56 rounded-2xl bg-white dark:bg-neutral-900 border border-slate-200/80 dark:border-white/10 shadow-lg py-2 z-20"
                >
                    <a 
                        href="{{ route('admin.reports.export.tracer-csv', request()->all()) }}" 
                        class="flex items-center px-4 py-2.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-neutral-800 transition-colors"
                    >
                        <i data-lucide="graduation-cap" class="w-4 h-4 mr-2.5 text-brand-secondary dark:text-brand-primary"></i>
                        <span>Ekspor Data Tracer Study</span>
                    </a>
                    <a 
                        href="{{ route('admin.reports.export.applications-csv', request()->all()) }}" 
                        class="flex items-center px-4 py-2.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-neutral-800 transition-colors"
                    >
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2.5 text-emerald-500"></i>
                        <span>Ekspor Rekap Lamaran & PKL</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-5">
                <label for="major_id" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                    Konsentrasi Keahlian / Jurusan
                </label>
                <select 
                    name="major_id" 
                    id="major_id"
                    class="w-full text-sm rounded-xl border-slate-200 dark:border-white/10 dark:bg-neutral-800 dark:text-slate-200 focus:border-brand-primary focus:ring-brand-primary/20"
                >
                    <option value="">Semua Konsentrasi Keahlian</option>
                    @foreach ($majors as $m)
                        <option value="{{ $m->id }}" {{ ($filters['major_id'] ?? '') == $m->id ? 'selected' : '' }}>
                            {{ $m->nama }} ({{ $m->kode }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-4">
                <label for="tahun" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                    Tahun Kelulusan / Angkatan
                </label>
                <input 
                    type="number" 
                    name="tahun" 
                    id="tahun" 
                    placeholder="Contoh: {{ date('Y') }}" 
                    value="{{ $filters['tahun'] ?? '' }}"
                    class="w-full text-sm rounded-xl border-slate-200 dark:border-white/10 dark:bg-neutral-800 dark:text-slate-200 focus:border-brand-primary focus:ring-brand-primary/20"
                >
            </div>

            <div class="sm:col-span-3 flex items-center gap-2">
                <button 
                    type="submit" 
                    class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-neutral-900 text-white dark:bg-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-slate-100 transition-colors"
                >
                    <i data-lucide="filter" class="w-4 h-4 mr-1.5"></i>
                    <span>Terapkan</span>
                </button>

                @if (!empty($filters['major_id']) || !empty($filters['tahun']))
                    <a 
                        href="{{ route('admin.reports.index') }}" 
                        class="p-2.5 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white border border-slate-200/80 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-neutral-800 transition-colors"
                        title="Reset Filter"
                    >
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 1. Executive Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Tingkat Penyerapan -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Tingkat Penyerapan Seleksi</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-sans text-slate-900 dark:text-white">
                {{ $reportData['metrics']['absorption_rate'] }}%
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $reportData['metrics']['accepted_applications'] }} dari {{ $reportData['metrics']['total_applications'] }} lamaran diterima
            </p>
        </div>

        <!-- Total Mitra Terverifikasi -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Mitra Industri (DUDI)</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                    <i data-lucide="building-2" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-sans text-slate-900 dark:text-white">
                {{ $reportData['metrics']['verified_companies'] }}
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $reportData['metrics']['total_companies'] }} total mitra terdaftar
            </p>
        </div>

        <!-- Lowongan Dibuka -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Lowongan Kerja & Magang</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <i data-lucide="briefcase" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-sans text-slate-900 dark:text-white">
                {{ $reportData['metrics']['active_jobs'] }}
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                {{ $reportData['metrics']['internship_jobs'] }} PKL / {{ $reportData['metrics']['fulltime_jobs'] }} Penuh Waktu
            </p>
        </div>

        <!-- Rata-rata Waktu Tunggu -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Masa Tunggu Kerja Lulusan</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-sans text-slate-900 dark:text-white">
                {{ $reportData['metrics']['avg_wait_time'] }} <span class="text-sm font-normal text-slate-400">Bulan</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Berdasarkan data kuesioner Tracer Study
            </p>
        </div>
    </div>

    <!-- 2. Dual Breakdown: BMW Stats & Keselarasan Kurikulum -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- BMW Alumni Breakdown -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">
                        Distribusi Status Lulusan (BMW)
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Bekerja, Melanjutkan Pendidikan, Wirausaha (Total: {{ $reportData['bmw_stats']['total'] }} alumni)
                    </p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-brand-primary/10 text-neutral-900 dark:text-brand-primary">
                    Tracer Study
                </span>
            </div>

            <div class="space-y-4">
                <!-- Bekerja -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Bekerja di Industri / Instansi
                        </span>
                        <span class="font-bold text-slate-900 dark:text-white">
                            {{ $reportData['bmw_stats']['bekerja']['count'] }} ({{ $reportData['bmw_stats']['bekerja']['pct'] }}%)
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-neutral-800 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-300" style="width: {{ $reportData['bmw_stats']['bekerja']['pct'] }}%"></div>
                    </div>
                </div>

                <!-- Kuliah -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                            Melanjutkan Pendidikan / Kuliah
                        </span>
                        <span class="font-bold text-slate-900 dark:text-white">
                            {{ $reportData['bmw_stats']['kuliah']['count'] }} ({{ $reportData['bmw_stats']['kuliah']['pct'] }}%)
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-neutral-800 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-sky-500 h-2.5 rounded-full transition-all duration-300" style="width: {{ $reportData['bmw_stats']['kuliah']['pct'] }}%"></div>
                    </div>
                </div>

                <!-- Wirausaha -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                            Wirausaha / Usaha Mandiri
                        </span>
                        <span class="font-bold text-slate-900 dark:text-white">
                            {{ $reportData['bmw_stats']['wirausaha']['count'] }} ({{ $reportData['bmw_stats']['wirausaha']['pct'] }}%)
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-neutral-800 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-purple-500 h-2.5 rounded-full transition-all duration-300" style="width: {{ $reportData['bmw_stats']['wirausaha']['pct'] }}%"></div>
                    </div>
                </div>

                <!-- Mencari Kerja -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Dalam Masa Persiapan / Mencari Kerja
                        </span>
                        <span class="font-bold text-slate-900 dark:text-white">
                            {{ $reportData['bmw_stats']['mencari_kerja']['count'] }} ({{ $reportData['bmw_stats']['mencari_kerja']['pct'] }}%)
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-neutral-800 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-amber-500 h-2.5 rounded-full transition-all duration-300" style="width: {{ $reportData['bmw_stats']['mencari_kerja']['pct'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keselarasan Kurikulum dengan Dunia Kerja -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">
                        Keselarasan Kurikulum Vokasi
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Tingkat relevansi kompetensi kejuruan dengan pekerjaan alumni
                    </p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60">
                    Relevansi
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-1">
                <div class="bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/60 dark:border-white/5 rounded-xl p-4">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 block mb-1">Sangat Sesuai</span>
                    <span class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                        {{ $reportData['keselarasan_stats']['sangat_sesuai'] }}
                    </span>
                    <span class="text-[11px] text-slate-400 block mt-1">Kompetensi 100% terpakai</span>
                </div>

                <div class="bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/60 dark:border-white/5 rounded-xl p-4">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 block mb-1">Sesuai</span>
                    <span class="text-2xl font-bold text-sky-600 dark:text-sky-400">
                        {{ $reportData['keselarasan_stats']['sesuai'] }}
                    </span>
                    <span class="text-[11px] text-slate-400 block mt-1">Sangat mendukung pekerjaan</span>
                </div>

                <div class="bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/60 dark:border-white/5 rounded-xl p-4">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 block mb-1">Kurang Sesuai</span>
                    <span class="text-2xl font-bold text-amber-600 dark:text-amber-400">
                        {{ $reportData['keselarasan_stats']['kurang_sesuai'] }}
                    </span>
                    <span class="text-[11px] text-slate-400 block mt-1">Hanya sebagian materi</span>
                </div>

                <div class="bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/60 dark:border-white/5 rounded-xl p-4">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 block mb-1">Tidak Sesuai</span>
                    <span class="text-2xl font-bold text-rose-600 dark:text-rose-400">
                        {{ $reportData['keselarasan_stats']['tidak_sesuai'] }}
                    </span>
                    <span class="text-[11px] text-slate-400 block mt-1">Beralih bidang karir</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Top Mitra Rekruter & Keahlian Unggulan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Mitra Industri Teraktif -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                Mitra Industri Teraktif dalam Penyerapan
            </h2>

            <div class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($reportData['top_companies'] as $comp)
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-neutral-800 flex items-center justify-center font-bold text-sm text-slate-700 dark:text-slate-200">
                                {{ strtoupper(substr($comp->nama, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                                    {{ $comp->nama }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $comp->bidang_usaha ?? 'Industri Mitra' }} &bull; {{ $comp->lokasi ?? 'Cirebon' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300">
                                {{ $comp->accepted_count }} Siswa Diterima
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada data penyerapan kemitraan industri.</p>
                @endforelse
            </div>
        </div>

        <!-- Keahlian Paling Banyak Disahkan Guru -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                Kompetensi Teknis Terverifikasi Guru
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @forelse ($reportData['top_skills'] as $sk)
                    <div class="bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/60 dark:border-white/5 rounded-xl p-3.5 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                {{ $sk->skill->nama ?? '-' }}
                            </span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-brand-primary/10 text-neutral-900 dark:text-brand-primary">
                                Skor {{ number_format($sk->avg_rating, 1) }}/5
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            {{ $sk->total_students }} siswa tersertifikasi/divalidasi
                        </p>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center col-span-2">Belum ada data keahlian tervalidasi.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
