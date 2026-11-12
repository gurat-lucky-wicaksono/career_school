@extends('layouts.company')

@section('title', 'Dashboard Mitra Industri')

@section('content')
<div class="space-y-8">
    <!-- 1. Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-heading font-bold text-text-primary tracking-tight">
                Dashboard Mitra Industri
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Selamat datang, <span class="font-semibold text-text-primary">{{ $company->nama }}</span>. Pantau rekrutmen talenta vokasi dan kelola lowongan kerja sekolah.
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if ($company->isVerified())
                <x-ui.button variant="primary" href="{{ route('company.jobs.index') }}?create=1">
                    Pasang lowongan
                </x-ui.button>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-state-warning-border bg-state-warning-bg text-state-warning-text text-xs font-medium">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                    <span>Menunggu verifikasi BKK</span>
                </span>
            @endif
        </div>
    </div>

    <!-- 2. Account Status Alert Banner (if pending) -->
    @if (!$company->isVerified())
        <div class="p-5 rounded-card bg-state-warning-bg border border-state-warning-border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-btn bg-amber-100 dark:bg-amber-900/40 text-state-warning-text flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i data-lucide="shield-alert" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-heading font-bold text-state-warning-text">
                        Akun Mitra Sedang Dalam Proses Verifikasi
                    </h3>
                    <p class="text-xs text-state-warning-text/90 mt-1 leading-relaxed">
                        Pengajuan kemitraan perusahaan Anda sedang ditinjau oleh tim BKK (Bursa Kerja Khusus) SMKN 1 Cirebon. Anda tetap dapat melengkapi profil perusahaan, namun penerbitan lowongan baru akan aktif setelah verifikasi disetujui.
                    </p>
                </div>
            </div>

            <x-ui.button variant="secondary" size="sm" href="{{ route('company.profile') }}">
                Lengkapi profil perusahaan
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
            title="Lowongan aktif" 
            value="{{ number_format($stats['active_jobs']) }}" 
            icon="check-circle" 
            trend="Menerima lamaran" 
            :trendUp="true"
        />

        <x-ui.stat-card 
            title="Total pelamar masuk" 
            value="{{ number_format($stats['total_applications']) }}" 
            icon="users" 
            trend="{{ $stats['pending_applications'] }} Perlu ditinjau" 
            :trendUp="$stats['pending_applications'] > 0"
        />

        <x-ui.stat-card 
            title="Pelamar diterima" 
            value="{{ number_format($stats['accepted_applications']) }}" 
            icon="user-check" 
            trend="Talenta lolos" 
            :trendUp="true"
        />
    </div>

    <!-- 4. Main 2-Column Bento Grid: Lowongan Terbaru & Pelamar Masuk -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Lowongan Kerja Perusahaan (7 Cols) -->
        <div class="lg:col-span-7 bg-surface-card border border-border-subtle rounded-card shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-border-subtle flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-heading font-bold text-text-primary">
                        Lowongan kerja terbaru
                    </h2>
                    <p class="text-xs text-text-secondary mt-0.5">
                        Daftar posisi pekerjaan dan kuota yang Anda publikasikan.
                    </p>
                </div>

                <a 
                    href="{{ route('company.jobs.index') }}" 
                    class="text-xs font-semibold text-brand-secondary dark:text-brand-primary hover:underline"
                >
                    Lihat semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-border-subtle">
                @forelse ($recentJobs as $job)
                    <div class="p-4 sm:p-5 hover:bg-surface-bg transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1 min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-heading font-bold text-text-primary truncate">
                                    {{ $job->judul }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $job->status === 'dibuka' ? 'bg-state-success-bg text-state-success-text border border-state-success-border' : 'bg-neutral-100 text-text-secondary dark:bg-neutral-800' }}">
                                    {{ ucfirst($job->status) }}
                                </span>
                            </div>

                            <p class="text-xs text-text-secondary truncate">
                                {{ ucfirst(str_replace('_', ' ', $job->jenis)) }} &bull; Kuota: {{ $job->kuota }} orang &bull; {{ $job->lokasi ?: ($company->kota ?: 'Cirebon') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3 sm:self-center flex-shrink-0">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-neutral-100 dark:bg-neutral-800 text-xs font-medium text-text-secondary border border-border-subtle">
                                <i data-lucide="users" class="w-3.5 h-3.5 text-text-muted"></i>
                                <span>{{ $job->applications_count }} Pelamar</span>
                            </span>

                            <a 
                                href="{{ route('company.jobs.edit', $job) }}" 
                                class="p-2 rounded-btn text-text-muted hover:text-text-primary hover:bg-surface-bg active:scale-[0.96] transition-transform"
                                title="Edit lowongan"
                            >
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 rounded-card bg-surface-bg border border-border-subtle text-text-muted flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="briefcase" class="w-6 h-6"></i>
                        </div>
                        <p class="text-sm font-heading font-semibold text-text-primary">Belum ada lowongan kerja</p>
                        <p class="text-xs text-text-secondary mt-1 max-w-sm mx-auto">
                            Mulai publikasikan posisi lowongan atau magang untuk merekrut siswa terbaik SMK Negeri 1 Cirebon.
                        </p>
                        @if ($company->isVerified())
                            <x-ui.button variant="primary" size="sm" href="{{ route('company.jobs.create') }}" class="mt-4">
                                Buat lowongan pertama
                            </x-ui.button>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Pelamar Terbaru Masuk (5 Cols) -->
        <div class="lg:col-span-5 bg-surface-card border border-border-subtle rounded-card shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-border-subtle flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-heading font-bold text-text-primary">
                        Pelamar masuk terbaru
                    </h2>
                    <p class="text-xs text-text-secondary mt-0.5">
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

            <div class="divide-y divide-border-subtle">
                @forelse ($recentApplications as $app)
                    <a 
                        href="{{ route('company.applications.show', $app) }}"
                        class="p-4 hover:bg-surface-bg transition-colors flex items-center justify-between gap-3 group"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Avatar Initial -->
                            <div class="w-9 h-9 rounded-full bg-neutral-100 dark:bg-neutral-800 border border-border-subtle text-text-primary font-bold text-xs flex items-center justify-center flex-shrink-0 group-hover:border-brand-primary/50 transition-colors">
                                {{ strtoupper(substr($app->student->user->name ?? 'S', 0, 2)) }}
                            </div>

                            <div class="min-w-0">
                                <h4 class="text-xs font-heading font-bold text-text-primary truncate group-hover:text-brand-secondary dark:group-hover:text-brand-primary transition-colors">
                                    {{ $app->student->user->name ?? 'Siswa' }}
                                </h4>
                                <p class="text-[11px] text-text-muted truncate">
                                    {{ $app->job->judul ?? 'Lowongan' }} &bull; {{ $app->student->major->kode ?? 'Vokasi' }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right flex-shrink-0">
                            <!-- Match Score Pill Badge -->
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold {{ $app->skor_kecocokan >= 75 ? 'bg-state-success-bg text-state-success-text border border-state-success-border' : 'bg-neutral-100 text-text-secondary dark:bg-neutral-800' }}">
                                <i data-lucide="sparkles" class="w-3 h-3 text-brand-secondary dark:text-brand-primary"></i>
                                <span>{{ number_format($app->skor_kecocokan, 0) }}% Cocok</span>
                            </span>
                            <span class="block text-[10px] text-text-muted mt-1 capitalize">
                                {{ $app->status }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-10 h-10 rounded-card bg-surface-bg border border-border-subtle text-text-muted flex items-center justify-center mx-auto mb-2">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <p class="text-xs font-heading font-semibold text-text-primary">Belum ada pelamar</p>
                        <p class="text-[11px] text-text-secondary mt-0.5">
                            Pelamar yang mendaftar ke lowongan akan langsung tampil di sini.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
