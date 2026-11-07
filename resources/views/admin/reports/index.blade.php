@extends('layouts.admin')

@section('title', 'Pusat Laporan & Ekspor Data BKK')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-border-subtle">
        <div>
            <h1 class="text-2xl font-bold font-sans tracking-tight text-text-primary">
                Pusat Laporan & Ekspor Data BKK
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Rekapitulasi penyerapan lulusan, kemitraan DUDI, data Tracer Study, dan dokumen resmi SMKN 1 Cirebon.
            </p>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap items-center gap-2.5">
            <x-ui.button 
                variant="primary"
                href="{{ route('admin.reports.print-summary', request()->all()) }}" 
                target="_blank"
            >
                Cetak Laporan Resmi (PDF)
            </x-ui.button>

            <div class="relative" x-data="{ open: false }">
                <x-ui.button 
                    variant="secondary"
                    type="button" 
                    @click="open = !open" 
                >
                    Ekspor CSV / Excel
                    <i data-lucide="chevron-down" class="w-4 h-4 ml-1.5 text-text-muted"></i>
                </x-ui.button>

                <div 
                    x-show="open" 
                    x-cloak 
                    @click.away="open = false" 
                    class="absolute right-0 mt-2 w-56 rounded-2xl bg-surface-card border border-border-subtle shadow-xl py-2 z-20"
                >
                    <a 
                        href="{{ route('admin.reports.export.tracer-csv', request()->all()) }}" 
                        class="flex items-center px-4 py-2.5 text-xs font-medium text-text-primary hover:bg-surface-bg transition-colors"
                    >
                        <i data-lucide="graduation-cap" class="w-4 h-4 mr-2.5 text-brand-secondary dark:text-brand-primary"></i>
                        <span>Ekspor Data Tracer Study</span>
                    </a>
                    <a 
                        href="{{ route('admin.reports.export.applications-csv', request()->all()) }}" 
                        class="flex items-center px-4 py-2.5 text-xs font-medium text-text-primary hover:bg-surface-bg transition-colors"
                    >
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2.5 text-emerald-500"></i>
                        <span>Ekspor Rekap Lamaran & PKL</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-surface-card border border-border-subtle rounded-card p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-5">
                <label for="major_id" class="block text-xs font-semibold text-text-primary mb-1.5">
                    Konsentrasi Keahlian / Jurusan
                </label>
                <select 
                    name="major_id" 
                    id="major_id"
                    class="choices-select w-full"
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
                <label for="tahun" class="block text-xs font-semibold text-text-primary mb-1.5">
                    Tahun Kelulusan / Angkatan
                </label>
                <input 
                    type="number" 
                    name="tahun" 
                    id="tahun" 
                    placeholder="Contoh: {{ date('Y') }}" 
                    value="{{ $filters['tahun'] ?? '' }}"
                    class="w-full h-10 px-4 text-xs sm:text-sm rounded-btn bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="sm:col-span-3 flex items-center gap-2">
                <x-ui.button 
                    type="submit" 
                    variant="primary"
                    class="flex-1"
                >
                    Terapkan
                </x-ui.button>

                @if (!empty($filters['major_id']) || !empty($filters['tahun']))
                    <a 
                        href="{{ route('admin.reports.index') }}" 
                        class="h-10 w-10 rounded-btn border border-border-default hover:border-text-secondary/70 hover:bg-surface-bg text-text-secondary hover:text-text-primary flex items-center justify-center transition-all shadow-xs shrink-0"
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
        <div class="bg-surface-card border border-border-subtle rounded-card p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-text-secondary">Tingkat Penyerapan Seleksi</span>
                <div class="w-8 h-8 rounded-btn bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-heading text-text-primary">
                {{ $reportData['metrics']['absorption_rate'] }}%
            </div>
            <p class="text-xs text-text-secondary">
                {{ $reportData['metrics']['accepted_applications'] }} dari {{ $reportData['metrics']['total_applications'] }} lamaran diterima
            </p>
        </div>

        <!-- Total Mitra Terverifikasi -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-text-secondary">Mitra Industri (DUDI)</span>
                <div class="w-8 h-8 rounded-btn bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                    <i data-lucide="building-2" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-heading text-text-primary">
                {{ $reportData['metrics']['verified_companies'] }}
            </div>
            <p class="text-xs text-text-secondary">
                {{ $reportData['metrics']['total_companies'] }} total mitra terdaftar
            </p>
        </div>

        <!-- Lowongan Dibuka -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-text-secondary">Lowongan Kerja & Magang</span>
                <div class="w-8 h-8 rounded-btn bg-brand-primary/10 text-brand-secondary dark:text-brand-primary flex items-center justify-center">
                    <i data-lucide="briefcase" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-heading text-text-primary">
                {{ $reportData['metrics']['active_jobs'] }}
            </div>
            <p class="text-xs text-text-secondary">
                {{ $reportData['metrics']['internship_jobs'] }} PKL / {{ $reportData['metrics']['fulltime_jobs'] }} Penuh Waktu
            </p>
        </div>

        <!-- Rata-rata Waktu Tunggu -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-text-secondary">Masa Tunggu Kerja Lulusan</span>
                <div class="w-8 h-8 rounded-btn bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-heading text-text-primary">
                {{ $reportData['metrics']['avg_wait_time'] }} <span class="text-sm font-normal text-text-muted">Bulan</span>
            </div>
            <p class="text-xs text-text-secondary">
                Berdasarkan data kuesioner Tracer Study
            </p>
        </div>
    </div>

    <!-- 2. Dual Breakdown: BMW Stats & Keselarasan Kurikulum -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- BMW Alumni Breakdown -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-6 shadow-xs space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-text-primary">
                        Distribusi Status Lulusan (BMW)
                    </h2>
                    <p class="text-xs text-text-secondary mt-0.5">
                        Bekerja, Melanjutkan Pendidikan, Wirausaha (Total: {{ $reportData['bmw_stats']['total'] }} alumni)
                    </p>
                </div>
                <x-ui.badge variant="brand">
                    Tracer Study
                </x-ui.badge>
            </div>

            <div class="space-y-4">
                <!-- Bekerja -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-text-primary flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Bekerja di Industri / Instansi
                        </span>
                        <span class="font-bold text-text-primary">
                            {{ $reportData['bmw_stats']['bekerja']['count'] }} ({{ $reportData['bmw_stats']['bekerja']['pct'] }}%)
                        </span>
                    </div>
                    <div class="w-full bg-surface-bg border border-border-subtle rounded-full h-2.5 overflow-hidden">
                        <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-300" style="width: {{ $reportData['bmw_stats']['bekerja']['pct'] }}%"></div>
                    </div>
                </div>

                <!-- Kuliah -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-text-primary flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                            Melanjutkan Pendidikan / Kuliah
                        </span>
                        <span class="font-bold text-text-primary">
                            {{ $reportData['bmw_stats']['kuliah']['count'] }} ({{ $reportData['bmw_stats']['kuliah']['pct'] }}%)
                        </span>
                    </div>
                    <div class="w-full bg-surface-bg border border-border-subtle rounded-full h-2.5 overflow-hidden">
                        <div class="bg-sky-500 h-2.5 rounded-full transition-all duration-300" style="width: {{ $reportData['bmw_stats']['kuliah']['pct'] }}%"></div>
                    </div>
                </div>

                <!-- Wirausaha -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-text-primary flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-primary"></span>
                            Wirausaha / Usaha Mandiri
                        </span>
                        <span class="font-bold text-text-primary">
                            {{ $reportData['bmw_stats']['wirausaha']['count'] }} ({{ $reportData['bmw_stats']['wirausaha']['pct'] }}%)
                        </span>
                    </div>
                    <div class="w-full bg-surface-bg border border-border-subtle rounded-full h-2.5 overflow-hidden">
                        <div class="bg-brand-primary h-2.5 rounded-full transition-all duration-300" style="width: {{ $reportData['bmw_stats']['wirausaha']['pct'] }}%"></div>
                    </div>
                </div>

                <!-- Mencari Kerja -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-text-primary flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Dalam Masa Persiapan / Mencari Kerja
                        </span>
                        <span class="font-bold text-text-primary">
                            {{ $reportData['bmw_stats']['mencari_kerja']['count'] }} ({{ $reportData['bmw_stats']['mencari_kerja']['pct'] }}%)
                        </span>
                    </div>
                    <div class="w-full bg-surface-bg border border-border-subtle rounded-full h-2.5 overflow-hidden">
                        <div class="bg-amber-500 h-2.5 rounded-full transition-all duration-300" style="width: {{ $reportData['bmw_stats']['mencari_kerja']['pct'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keselarasan Kurikulum dengan Dunia Kerja -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-6 shadow-xs space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-text-primary">
                        Keselarasan Kurikulum Vokasi
                    </h2>
                    <p class="text-xs text-text-secondary mt-0.5">
                        Tingkat relevansi kompetensi kejuruan dengan pekerjaan alumni
                    </p>
                </div>
                <x-ui.badge variant="success">
                    Relevansi
                </x-ui.badge>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-1">
                <div class="bg-surface-bg border border-border-subtle rounded-xl p-4">
                    <span class="text-xs font-medium text-text-secondary block mb-1">Sangat Sesuai</span>
                    <span class="text-2xl font-bold font-heading text-brand-secondary dark:text-brand-primary">
                        {{ $reportData['keselarasan_stats']['sangat_sesuai'] }}
                    </span>
                    <span class="text-[11px] text-text-muted block mt-1">Kompetensi 100% terpakai</span>
                </div>

                <div class="bg-surface-bg border border-border-subtle rounded-xl p-4">
                    <span class="text-xs font-medium text-text-secondary block mb-1">Sesuai</span>
                    <span class="text-2xl font-bold font-heading text-emerald-600 dark:text-emerald-400">
                        {{ $reportData['keselarasan_stats']['sesuai'] }}
                    </span>
                    <span class="text-[11px] text-text-muted block mt-1">Sangat mendukung pekerjaan</span>
                </div>

                <div class="bg-surface-bg border border-border-subtle rounded-xl p-4">
                    <span class="text-xs font-medium text-text-secondary block mb-1">Kurang Sesuai</span>
                    <span class="text-2xl font-bold font-heading text-amber-600 dark:text-amber-400">
                        {{ $reportData['keselarasan_stats']['kurang_sesuai'] }}
                    </span>
                    <span class="text-[11px] text-text-muted block mt-1">Hanya sebagian materi</span>
                </div>

                <div class="bg-surface-bg border border-border-subtle rounded-xl p-4">
                    <span class="text-xs font-medium text-text-secondary block mb-1">Tidak Sesuai</span>
                    <span class="text-2xl font-bold font-heading text-rose-600 dark:text-rose-400">
                        {{ $reportData['keselarasan_stats']['tidak_sesuai'] }}
                    </span>
                    <span class="text-[11px] text-text-muted block mt-1">Beralih bidang karir</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Top Mitra Rekruter & Keahlian Unggulan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Mitra Industri Teraktif -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-6 shadow-xs space-y-4">
            <h2 class="text-base font-bold text-text-primary">
                Mitra Industri Teraktif dalam Penyerapan
            </h2>

            <div class="divide-y divide-border-subtle">
                @forelse ($reportData['top_companies'] as $comp)
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-surface-bg border border-border-default flex items-center justify-center font-bold text-sm text-text-primary">
                                {{ strtoupper(substr($comp->nama, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-text-primary">
                                    {{ $comp->nama }}
                                </h3>
                                <p class="text-xs text-text-secondary">
                                    {{ $comp->bidang_usaha ?? 'Industri Mitra' }} &bull; {{ $comp->lokasi ?? 'Cirebon' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right">
                            <x-ui.badge variant="success">
                                {{ $comp->accepted_count }} Siswa Diterima
                            </x-ui.badge>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-text-muted py-4 text-center">Belum ada data penyerapan kemitraan industri.</p>
                @endforelse
            </div>
        </div>

        <!-- Keahlian Paling Banyak Disahkan Guru -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-6 shadow-xs space-y-4">
            <h2 class="text-base font-bold text-text-primary">
                Kompetensi Teknis Terverifikasi Guru
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @forelse ($reportData['top_skills'] as $sk)
                    <div class="bg-surface-bg border border-border-subtle rounded-xl p-3.5 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-text-primary truncate">
                                {{ $sk->skill->nama ?? '-' }}
                            </span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-brand-primary/10 text-brand-secondary dark:text-brand-primary">
                                Skor {{ number_format($sk->avg_rating, 1) }}/5
                            </span>
                        </div>
                        <p class="text-[11px] text-text-secondary">
                            {{ $sk->total_students }} siswa tersertifikasi/divalidasi
                        </p>
                    </div>
                @empty
                    <p class="text-xs text-text-muted py-4 text-center col-span-2">Belum ada data keahlian tervalidasi.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 4. Evaluasi & Mutu Kinerja Praktik Kerja Lapangan (PKL) -->
    <div class="bg-surface-card border border-border-subtle rounded-card p-6 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-border-subtle pb-4">
            <div>
                <h2 class="text-base font-bold text-text-primary flex items-center gap-2">
                    <i data-lucide="award" class="w-5 h-5 text-brand-primary"></i>
                    <span>Evaluasi & Mutu Kinerja Praktik Kerja Lapangan (PKL)</span>
                </h2>
                <p class="text-xs text-text-secondary mt-0.5">
                    Rekapitulasi instrumen penilaian kemitraan DUDI dan transkrip kelulusan magang siswa
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <span class="text-xs text-text-secondary">Rata-rata Skor PKL</span>
                    <div class="text-2xl font-bold font-heading text-text-primary">
                        {{ $reportData['metrics']['avg_internship_score'] }}
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-xl bg-surface-bg border border-border-subtle">
                <span class="text-xs text-text-secondary">Total Evaluasi Berjalan</span>
                <div class="text-xl font-bold font-heading text-text-primary mt-1">{{ $reportData['metrics']['total_evaluations'] }}</div>
                <span class="text-[11px] text-text-muted">Siswa berstatus dinilai DUDI</span>
            </div>

            <div class="p-4 rounded-xl bg-surface-bg border border-border-subtle">
                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Sertifikat Sah Terbit</span>
                <div class="text-xl font-bold font-heading text-emerald-600 dark:text-emerald-400 mt-1">{{ $reportData['metrics']['verified_evaluations'] }}</div>
                <span class="text-[11px] text-text-muted">Tervalidasi pembimbing sekolah</span>
            </div>

            <div class="p-4 rounded-xl bg-surface-bg border border-border-subtle">
                <span class="text-xs text-brand-secondary dark:text-brand-primary font-medium">Menunggu Pengesahan</span>
                <div class="text-xl font-bold font-heading text-brand-secondary dark:text-brand-primary mt-1">{{ $reportData['metrics']['submitted_evaluations'] }}</div>
                <span class="text-[11px] text-text-muted">Telah dikirimkan supervisor DUDI</span>
            </div>

            <div class="p-4 rounded-xl bg-surface-bg border border-border-subtle">
                <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">Distribusi Predikat Lulus</span>
                <div class="flex items-center gap-2 mt-1 text-xs font-bold">
                    <x-ui.badge variant="success">A: {{ $reportData['internship_grades']['A'] }}</x-ui.badge>
                    <x-ui.badge variant="info">B: {{ $reportData['internship_grades']['B'] }}</x-ui.badge>
                    <x-ui.badge variant="warning">C: {{ $reportData['internship_grades']['C'] }}</x-ui.badge>
                </div>
                <span class="text-[11px] text-text-muted mt-1 block">Skala mutu standar BKK</span>
            </div>
        </div>
    </div>
</div>
@endsection
