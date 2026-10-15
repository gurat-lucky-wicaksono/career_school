@extends('layouts.company')

@section('title', 'Dashboard Mitra Industri')

@section('content')
<div class="space-y-8">
    <!-- 1. Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Dashboard Mitra Industri
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Selamat datang, <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $company->nama }}</span>. Pantau rekrutmen talenta vokasi dan kelola lowongan kerja sekolah.
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if ($company->isVerified())
                <x-ui.button variant="primary" href="{{ route('company.jobs.index') }}?create=1">
                    Pasang Lowongan
                </x-ui.button>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-amber-300 dark:border-amber-700/60 bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 text-xs font-medium">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                    <span>Menunggu Verifikasi BKK</span>
                </span>
            @endif
        </div>
    </div>

    <!-- 2. Account Status Alert Banner (if pending) -->
    @if (!$company->isVerified())
        <div class="p-5 rounded-2xl bg-amber-50/90 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i data-lucide="shield-alert" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-amber-900 dark:text-amber-200">
                        Akun Mitra Sedang Dalam Proses Verifikasi
                    </h3>
                    <p class="text-xs text-amber-800/90 dark:text-amber-300/80 mt-1 leading-relaxed">
                        Pengajuan kemitraan perusahaan Anda sedang ditinjau oleh tim BKK (Bursa Kerja Khusus) SMKN 1 Cirebon. Anda tetap dapat melengkapi profil perusahaan, namun penerbitan lowongan baru akan aktif setelah verifikasi disetujui.
                    </p>
                </div>
            </div>

            <x-ui.button variant="secondary" size="sm" href="{{ route('company.profile') }}">
                Lengkapi Profil Perusahaan
            </x-ui.button>
        </div>
    @endif

    <!-- 3. KPI Stat Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-ui.stat-card 
            title="Total Lowongan Kerja" 
            value="{{ number_format($stats['total_jobs']) }}" 
            icon="briefcase" 
            trend="{{ $stats['active_jobs'] }} Aktif" 
            :trendUp="true"
        />

        <x-ui.stat-card 
            title="Lowongan Aktif" 
            value="{{ number_format($stats['active_jobs']) }}" 
            icon="check-circle" 
            trend="Menerima Lamaran" 
            :trendUp="true"
        />

        <x-ui.stat-card 
            title="Total Pelamar Masuk" 
            value="{{ number_format($stats['total_applications']) }}" 
            icon="users" 
            trend="{{ $stats['pending_applications'] }} Menunggu Review" 
            :trendUp="$stats['pending_applications'] > 0"
        />

        <x-ui.stat-card 
            title="Pelamar Diterima" 
            value="{{ number_format($stats['accepted_applications']) }}" 
            icon="user-check" 
            trend="Talenta Lolos" 
            :trendUp="true"
        />
    </div>

    <!-- 4. Main 2-Column Bento Grid: Lowongan Terbaru & Pelamar Masuk -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Lowongan Kerja Perusahaan (7 Cols) -->
        <div class="lg:col-span-7 bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                        Lowongan Kerja Terbaru
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Daftar posisi pekerjaan dan kuota yang Anda publikasikan.
                    </p>
                </div>

                <a 
                    href="{{ route('company.jobs.index') }}" 
                    class="text-xs font-semibold text-brand-secondary dark:text-brand-primary hover:underline"
                >
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($recentJobs as $job)
                    <div class="p-4 sm:p-5 hover:bg-slate-50/60 dark:hover:bg-neutral-800/40 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1 min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                    {{ $job->judul }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $job->status === 'dibuka' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40' : 'bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-slate-400' }}">
                                    {{ ucfirst($job->status) }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                {{ ucfirst(str_replace('_', ' ', $job->jenis)) }} &bull; Kuota: {{ $job->kuota }} orang &bull; {{ $job->lokasi ?: ($company->kota ?: 'Cirebon') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3 sm:self-center flex-shrink-0">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-neutral-800 text-xs font-medium text-slate-700 dark:text-slate-300">
                                <i data-lucide="users" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>{{ $job->applications_count }} Pelamar</span>
                            </span>

                            <a 
                                href="{{ route('company.jobs.edit', $job) }}" 
                                class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors"
                                title="Edit Lowongan"
                            >
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-neutral-800 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="briefcase" class="w-6 h-6"></i>
                        </div>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum Ada Lowongan Kerja</p>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Mulai publikasikan posisi lowongan atau magang untuk merekrut siswa terbaik SMK Negeri 1 Cirebon.
                        </p>
                        @if ($company->isVerified())
                            <a 
                                href="{{ route('company.jobs.create') }}" 
                                class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-brand-primary text-neutral-900 text-xs font-semibold hover:brightness-105 transition-all"
                            >
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Buat Lowongan Pertama</span>
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Pelamar Terbaru Masuk (5 Cols) -->
        <div class="lg:col-span-5 bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                        Pelamar Masuk Terbaru
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Kandidat siswa yang baru melamar ke lowongan Anda.
                    </p>
                </div>

                <a 
                    href="{{ route('company.applications.index') }}" 
                    class="text-xs font-semibold text-brand-secondary dark:text-brand-primary hover:underline"
                >
                    Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($recentApplications as $app)
                    <a 
                        href="{{ route('company.applications.show', $app) }}"
                        class="p-4 hover:bg-slate-50/60 dark:hover:bg-neutral-800/40 transition-colors flex items-center justify-between gap-3 group"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Avatar Initial -->
                            <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-neutral-800 border border-slate-200/60 dark:border-white/10 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center justify-center flex-shrink-0 group-hover:border-brand-primary/50 transition-colors">
                                {{ strtoupper(substr($app->student->user->name ?? 'S', 0, 2)) }}
                            </div>

                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-brand-secondary dark:group-hover:text-brand-primary transition-colors">
                                    {{ $app->student->user->name ?? 'Siswa' }}
                                </h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                    {{ $app->job->judul ?? 'Lowongan' }} &bull; {{ $app->student->major->kode ?? 'Vokasi' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right flex-shrink-0">
                            <!-- Match Score Pill Badge -->
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold {{ $app->skor_kecocokan >= 75 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60' : 'bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-slate-300' }}">
                                <i data-lucide="sparkles" class="w-3 h-3 text-brand-secondary dark:text-brand-primary"></i>
                                <span>{{ number_format($app->skor_kecocokan, 0) }}% Cocok</span>
                            </span>
                            <span class="block text-[10px] text-slate-400 mt-1 capitalize">
                                {{ $app->status }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-neutral-800 text-slate-400 flex items-center justify-center mx-auto mb-2">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Belum Ada Pelamar</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Pelamar yang mendaftar ke lowongan akan langsung tampil di sini.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
