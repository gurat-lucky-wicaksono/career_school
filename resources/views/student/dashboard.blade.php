@extends('layouts.student')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="space-y-8">
    <!-- 1. Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-brand-primary mb-1">
                <span>Portal Siswa Vokasi</span>
                <span class="text-slate-300 dark:text-neutral-600">&bull;</span>
                <span>{{ $student->schoolClass?->nama ?? 'PPLG' }}</span>
                <span class="text-slate-300 dark:text-neutral-600">&bull;</span>
                <span>NISN: {{ $student->nisn }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Halo, {{ $user->name }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Pantau progres lamaran PKL/kerja, tingkatkan keahlian kejuruan, dan pamerkan portofolio terbaikmu.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button variant="secondary" size="md" href="{{ route('student.portfolios.index') }}">
                <i data-lucide="folder-kanban" class="w-4 h-4 mr-2"></i>
                Kelola Portofolio
            </x-ui.button>
            <x-ui.button variant="primary" size="md" href="{{ route('student.jobs.index') }}">
                <i data-lucide="compass" class="w-4 h-4 mr-2"></i>
                Jelajahi Lowongan
            </x-ui.button>
        </div>
    </div>

    <!-- 2. Active Application Alert Banner (If Any) -->
    @if ($activeApplication)
        @php
            $isInterview = $activeApplication->status === 'wawancara';
            $isReview = $activeApplication->status === 'ditinjau';
        @endphp
        <div class="p-5 rounded-2xl border transition-all {{ $isInterview ? 'bg-indigo-50/90 dark:bg-indigo-950/30 border-indigo-200 dark:border-indigo-800/50' : 'bg-sky-50/90 dark:bg-sky-950/30 border-sky-200 dark:border-sky-800/50' }} flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 {{ $isInterview ? 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300' : 'bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-300' }}">
                    <i data-lucide="{{ $isInterview ? 'calendar-check-2' : 'clock' }}" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold uppercase tracking-wider {{ $isInterview ? 'bg-indigo-600 text-white' : 'bg-sky-600 text-white' }}">
                            {{ $isInterview ? 'Panggilan Wawancara' : 'Sedang Ditinjau' }}
                        </span>
                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                            {{ $activeApplication->job->company->nama ?? 'Mitra Industri' }}
                        </span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mt-1">
                        {{ $activeApplication->job->judul }}
                    </h3>
                    @if ($activeApplication->catatan_hrd)
                        <div class="mt-1.5 p-2.5 rounded-lg bg-white/70 dark:bg-neutral-900/60 border border-slate-200/50 dark:border-white/5 text-xs text-slate-700 dark:text-slate-300">
                            <span class="font-semibold text-slate-900 dark:text-white">Catatan HRD:</span> {{ $activeApplication->catatan_hrd }}
                        </div>
                    @else
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                            Lamaran Anda telah diterima oleh HRD dan sedang dievaluasi bersama portofolio kejuruan Anda.
                        </p>
                    @endif
                </div>
            </div>

            <x-ui.button variant="secondary" size="sm" href="{{ route('student.applications.index') }}">
                Detail Lamaran
            </x-ui.button>
        </div>
    @endif

    <!-- 3. KPI Stat Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-ui.stat-card 
            title="Lamaran Terkirim" 
            value="{{ number_format($stats['total_applications']) }}" 
            icon="send" 
            trend="{{ $stats['accepted_applications'] }} Diterima" 
            :trendUp="$stats['accepted_applications'] > 0"
        />

        <x-ui.stat-card 
            title="Panggilan Wawancara" 
            value="{{ number_format($stats['interview_applications']) }}" 
            icon="calendar-check" 
            trend="Tahap Lanjut" 
            :trendUp="true"
        />

        <x-ui.stat-card 
            title="Karya Portofolio" 
            value="{{ number_format($stats['portfolios_count']) }}" 
            icon="folder-git-2" 
            trend="Proyek Terdaftar" 
            :trendUp="true"
        />

        <x-ui.stat-card 
            title="Keahlian Tervalidasi" 
            value="{{ number_format($stats['validated_skills_count']) }}" 
            icon="award" 
            trend="Oleh Guru Pengampu" 
            :trendUp="true"
        />
    </div>

    <!-- 4. Main Section: Rekomendasi Lowongan & Profil Kompetensi -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Smart Job Recommendations (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="p-5 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                                Rekomendasi Lowongan Industri
                            </h2>
                            <span class="px-2 py-0.5 rounded-full bg-brand-primary/10 dark:bg-brand-primary/20 text-brand-primary text-[10px] font-bold">
                                Smart Matching KAK 15
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Disesuaikan dengan keahlian, jurusan, dan portofolio kejuruan Anda.
                        </p>
                    </div>

                    <a href="{{ route('student.jobs.index') }}" class="text-xs font-semibold text-brand-primary hover:text-brand-primary/80 transition-colors flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse ($recommendedJobs as $job)
                        <div class="p-5 hover:bg-slate-50/60 dark:hover:bg-neutral-800/40 transition-colors flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-neutral-800 border border-slate-200/60 dark:border-white/10 flex items-center justify-center flex-shrink-0 text-slate-700 dark:text-slate-200 font-bold text-sm shadow-sm">
                                    {{ strtoupper(substr($job->company->nama ?? 'P', 0, 2)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm font-bold text-slate-900 dark:text-white hover:text-brand-primary transition-colors">
                                            <a href="{{ route('student.jobs.show', $job) }}">
                                                {{ $job->judul }}
                                            </a>
                                        </h3>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $job->jenis === 'magang' ? 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800/40' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40' }}">
                                            {{ $job->jenis === 'magang' ? 'Magang / PKL' : 'Penuh Waktu' }}
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        <span class="flex items-center gap-1 font-medium text-slate-700 dark:text-slate-300">
                                            <i data-lucide="building-2" class="w-3.5 h-3.5 text-slate-400"></i>
                                            {{ $job->company->nama ?? 'Mitra DUDI' }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                            {{ $job->lokasi }}
                                        </span>
                                        @if ($job->gaji_min)
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="wallet" class="w-3.5 h-3.5 text-slate-400"></i>
                                                Rp {{ number_format($job->gaji_min, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Matched Skills Pills -->
                                    <div class="flex flex-wrap items-center gap-1.5 mt-2.5">
                                        @foreach ($job->skills->take(3) as $skill)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300">
                                                {{ $skill->nama }}
                                            </span>
                                        @endforeach
                                        @if ($job->skills->count() > 3)
                                            <span class="text-[10px] text-slate-400">
                                                +{{ $job->skills->count() - 3 }} lainnya
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Match Score & Action -->
                            <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-2 flex-shrink-0 pt-2 sm:pt-0 border-t sm:border-0 border-slate-100 dark:border-white/5">
                                <div class="text-right">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ $job->match_score >= 80 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60' : ($job->match_score >= 60 ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200/80 dark:border-blue-800/60' : 'bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-slate-300') }}">
                                        <i data-lucide="sparkles" class="w-3 h-3"></i>
                                        <span>{{ $job->match_percentage }} Cocok</span>
                                    </span>
                                </div>

                                <div class="mt-1">
                                    @if ($job->is_applied)
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-medium bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-white/10">
                                            <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>
                                            Sudah Dilamar
                                        </span>
                                    @else
                                        <x-ui.button variant="primary" size="sm" href="{{ route('student.jobs.show', $job) }}">
                                            Lihat Lowongan
                                        </x-ui.button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-neutral-800 flex items-center justify-center mx-auto text-slate-400">
                                <i data-lucide="briefcase" class="w-6 h-6"></i>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white mt-3">
                                Belum ada lowongan dibuka
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                                Mitra industri belum membuka lowongan baru. Silakan periksa kembali dalam beberapa waktu.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Kompetensi & Portofolio Siswa (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Kompetensi Siswa Card -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/5">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            Keahlian Terdaftar
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Validasi kompetensi oleh guru kejuruan.
                        </p>
                    </div>
                    <a href="{{ route('student.profile') }}" class="text-xs font-semibold text-brand-primary hover:underline">
                        Kelola
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse ($studentSkills as $skill)
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-medium text-slate-800 dark:text-slate-200 truncate">
                                        {{ $skill->nama }}
                                    </span>
                                    @if ($skill->pivot->tervalidasi)
                                        <span title="Divalidasi oleh Guru" class="text-emerald-600 dark:text-emerald-400 flex-shrink-0">
                                            <i data-lucide="badge-check" class="w-3.5 h-3.5"></i>
                                        </span>
                                    @endif
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-neutral-800 rounded-full h-1.5 mt-1.5 overflow-hidden">
                                    <div class="bg-brand-primary h-1.5 rounded-full" style="width: {{ (($skill->pivot->tingkat ?? 3) / 5) * 100 }}%"></div>
                                </div>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 flex-shrink-0">
                                Lv. {{ $skill->pivot->tingkat ?? 3 }}/5
                            </span>
                        </div>
                    @empty
                        <div class="py-4 text-center text-xs text-slate-500 dark:text-slate-400">
                            Belum ada keahlian terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Portofolio Terbaru Card -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/5">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            Portofolio Karya
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Bukti karya dan proyek mandiri.
                        </p>
                    </div>
                    <a href="{{ route('student.portfolios.index') }}" class="text-xs font-semibold text-brand-primary hover:underline">
                        Lihat Semua
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse ($recentPortfolios as $portfolio)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                    {{ $portfolio->judul }}
                                </h4>
                                @if ($portfolio->tautan)
                                    <a href="{{ $portfolio->tautan }}" target="_blank" rel="noopener noreferrer" class="text-brand-primary hover:text-brand-primary/80 flex-shrink-0" title="Kunjungi Tautan">
                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    </a>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2">
                                {{ $portfolio->deskripsi }}
                            </p>
                        </div>
                    @empty
                        <div class="py-4 text-center text-xs text-slate-500 dark:text-slate-400">
                            Belum ada portofolio yang diunggah.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Widget Pusat Informasi & Agenda BKK SMKN 1 Cirebon -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pengumuman BKK Terkini -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-brand-primary/10 text-brand-secondary dark:text-brand-primary flex items-center justify-center">
                        <i data-lucide="megaphone" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            Pengumuman BKK Terkini
                        </h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Pemberitahuan rekrutmen dan panduan PKL sekolah.
                        </p>
                    </div>
                </div>
                <a href="{{ route('public.announcements') }}" class="text-xs font-semibold text-brand-secondary dark:text-brand-primary hover:underline">
                    Semua Pengumuman
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($recentAnnouncements as $announcement)
                    <a href="{{ route('public.announcements.show', $announcement) }}" class="block p-3.5 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5 hover:border-brand-primary/40 transition-all group">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            @if ($announcement->prioritas === 'penting')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                    Penting
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-200/70 text-slate-700 dark:bg-neutral-700 dark:text-slate-300">
                                    Informasi
                                </span>
                            @endif
                            <span class="text-[11px] text-slate-400">
                                {{ $announcement->created_at->format('d M Y') }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-brand-secondary dark:group-hover:text-brand-primary transition-colors line-clamp-1">
                            {{ $announcement->judul }}
                        </h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                            {{ Str::limit(strip_tags($announcement->isi), 100) }}
                        </p>
                    </a>
                @empty
                    <div class="py-6 text-center text-xs text-slate-500 dark:text-slate-400">
                        Belum ada pengumuman baru dari BKK SMKN 1 Cirebon.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Agenda Kegiatan Terdekat -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            Agenda Kegiatan Vokasi
                        </h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Jadwal kunjungan industri & pembekalan PKL.
                        </p>
                    </div>
                </div>
                <a href="{{ route('public.agendas') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                    Semua Agenda
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($upcomingAgendas as $agenda)
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5 flex items-start justify-between gap-3">
                        <div class="space-y-1 min-w-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300">
                                {{ ucfirst($agenda->jenis) }}
                            </span>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white line-clamp-1">
                                {{ $agenda->judul }}
                            </h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                <i data-lucide="map-pin" class="w-3 h-3 text-slate-400"></i>
                                <span class="truncate">{{ $agenda->lokasi ?: 'Aula SMKN 1 Cirebon' }}</span>
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0 text-xs">
                            <span class="font-bold text-slate-900 dark:text-white block">
                                {{ $agenda->tanggal_mulai ? $agenda->tanggal_mulai->format('d M') : '-' }}
                            </span>
                            <span class="text-[10px] text-slate-400 block">
                                {{ $agenda->tanggal_mulai ? $agenda->tanggal_mulai->format('Y') : '' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-500 dark:text-slate-400">
                        Belum ada agenda terdekat yang dijadwalkan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
