@extends('layouts.teacher')

@section('title', 'Dashboard Guru Kejuruan')

@section('content')
<div class="space-y-8">
    <!-- 1. Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-brand-secondary dark:text-brand-primary mb-1">
                <span>Portal pembimbing vokasi</span>
                <span class="text-text-muted">&bull;</span>
                <span>NIP: {{ $teacher->nip }}</span>
                <span class="text-text-muted">&bull;</span>
                <span>{{ $teacher->mata_pelajaran }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-heading font-bold text-text-primary tracking-tight">
                Selamat datang, {{ $user->name }}
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Validasi kompetensi keahlian siswa, pantau penempatan Praktik Kerja Lapangan (PKL), dan evaluasi portofolio kejuruan.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button variant="secondary" size="md" href="{{ route('teacher.mentoring.index') }}">
                <i data-lucide="users" class="w-4 h-4 mr-2"></i>
                Siswa bimbingan
            </x-ui.button>
            <x-ui.button variant="primary" size="md" href="{{ route('teacher.skills.index') }}">
                <i data-lucide="award" class="w-4 h-4 mr-2"></i>
                Validasi keahlian
            </x-ui.button>
        </div>
    </div>

    <!-- 2. Alert Banner Antrean Validasi (Jika ada keahlian pending) -->
    @if ($stats['pending_skills'] > 0)
        <div class="p-5 rounded-card bg-state-warning-bg border border-state-warning-border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-btn bg-amber-100 dark:bg-amber-900/40 text-state-warning-text flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-heading font-bold text-state-warning-text">
                        Terdapat {{ $stats['pending_skills'] }} pengajuan keahlian menunggu validasi
                    </h3>
                    <p class="text-xs text-state-warning-text/90 mt-1 leading-relaxed">
                        Siswa telah menambahkan kompetensi mandiri pada profil mereka. Validasi resmi dari guru pengampu diperlukan agar skor pencocokan lowongan industri (bobot 60%) dapat terhitung optimal.
                    </p>
                </div>
            </div>

            <x-ui.button variant="secondary" size="sm" href="{{ route('teacher.skills.index') }}?status=pending">
                Buka antrean validasi
            </x-ui.button>
        </div>
    @endif

    <!-- 3. KPI Stat Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-ui.stat-card 
            title="Siswa binaan / rombel" 
            value="{{ number_format($stats['total_students']) }}" 
            icon="users" 
            trend="Aktif di sekolah" 
            :trendUp="true"
        />

        <x-ui.stat-card 
            title="Menunggu validasi" 
            value="{{ number_format($stats['pending_skills']) }}" 
            icon="clock" 
            trend="{{ $stats['pending_skills'] > 0 ? 'Perlu ditinjau' : 'Semua bersih' }}" 
            :trendUp="$stats['pending_skills'] === 0"
        />

        <x-ui.stat-card 
            title="Keahlian tervalidasi" 
            value="{{ number_format($stats['validated_skills']) }}" 
            icon="award" 
            trend="Standar vokasi" 
            :trendUp="true"
        />

        <x-ui.stat-card 
            title="Siswa aktif PKL / seleksi" 
            value="{{ number_format($stats['active_internships']) }}" 
            icon="briefcase" 
            trend="Di mitra industri" 
            :trendUp="true"
        />
    </div>

    <!-- 4. Main 2-Column Bento Grid: Antrean Validasi & Penempatan DUDI -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Antrean Validasi Keahlian (7 Cols) -->
        <div class="lg:col-span-7 bg-surface-card border border-border-subtle rounded-card shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-border-subtle flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-heading font-bold text-text-primary">
                        Antrean validasi keahlian siswa
                    </h2>
                    <p class="text-xs text-text-secondary mt-0.5">
                        Daftar keahlian yang diajukan siswa untuk disahkan kompetensinya.
                    </p>
                </div>

                <a href="{{ route('teacher.skills.index') }}" class="text-xs font-semibold text-brand-secondary dark:text-brand-primary hover:underline transition-colors flex items-center gap-1">
                    <span>Lihat semua</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="divide-y divide-border-subtle">
                @forelse ($pendingSkillSubmissions as $submission)
                    <div class="p-4 hover:bg-surface-bg transition-colors flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <div class="w-9 h-9 rounded-btn bg-brand-primary/10 text-brand-secondary dark:text-brand-primary font-bold text-xs flex items-center justify-center flex-shrink-0">
                                {{ strtoupper(substr($submission->student->user->name ?? 'S', 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-heading font-bold text-text-primary truncate">
                                        {{ $submission->student->user->name }}
                                    </h4>
                                    <span class="text-[10px] text-text-muted font-mono">
                                        {{ $submission->student->schoolClass?->nama ?? 'PPLG' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-neutral-100 dark:bg-neutral-800 text-text-secondary">
                                        {{ $submission->skill->nama }}
                                    </span>
                                    <span class="text-[11px] text-text-muted">
                                        Klaim siswa: <strong>Level {{ $submission->tingkat }}/5</strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Validate Action -->
                        <form method="POST" action="{{ route('teacher.skills.validate', $submission) }}" class="flex-shrink-0">
                            @csrf
                            <input type="hidden" name="tingkat" value="{{ $submission->tingkat }}">
                            <input type="hidden" name="catatan" value="Diverifikasi langsung oleh guru pengampu kejuruan.">
                            <button 
                                type="submit" 
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-btn bg-brand-primary text-text-on-brand-primary text-xs font-semibold hover:brightness-105 active:scale-[0.96] transition-transform shadow-xs"
                            >
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                <span>Sahkan</span>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-10 h-10 rounded-btn bg-state-success-bg text-state-success-text flex items-center justify-center mx-auto">
                            <i data-lucide="check-check" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-heading font-bold text-text-primary mt-2.5">
                            Semua keahlian telah tervalidasi
                        </h4>
                        <p class="text-[11px] text-text-muted mt-0.5">
                            Tidak ada pengajuan keahlian baru yang tertunda saat ini.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Status Penempatan DUDI Siswa Binaan (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-surface-card border border-border-subtle rounded-card shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-border-subtle">
                    <div>
                        <h2 class="text-sm font-heading font-bold text-text-primary">
                            Progres penempatan PKL siswa
                        </h2>
                        <p class="text-xs text-text-secondary">
                            Aktivitas seleksi terbaru di industri mitra DUDI.
                        </p>
                    </div>
                    <a href="{{ route('teacher.mentoring.index') }}" class="text-xs font-semibold text-brand-secondary dark:text-brand-primary hover:underline">
                        Pantau
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse ($recentApplications as $app)
                        @php
                            $badgeClasses = [
                                'wawancara' => 'bg-state-info-bg text-state-info-text border-state-info-border',
                                'diterima' => 'bg-state-success-bg text-state-success-text border-state-success-border',
                                'ditinjau' => 'bg-state-warning-bg text-state-warning-text border-state-warning-border',
                                'tertunda' => 'bg-neutral-100 text-text-secondary dark:bg-neutral-800 border-border-subtle',
                            ];
                        @endphp
                        <div class="p-3 rounded-btn bg-surface-bg border border-border-subtle space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-heading font-bold text-xs text-text-primary truncate">
                                    {{ $app->student->user->name }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border capitalize {{ $badgeClasses[$app->status] ?? $badgeClasses['tertunda'] }}">
                                    {{ $app->status }}
                                </span>
                            </div>
                            <p class="text-[11px] text-text-muted truncate">
                                {{ $app->job->judul }} &bull; <strong class="text-text-primary">{{ $app->job->company->nama ?? 'Mitra DUDI' }}</strong>
                            </p>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-text-muted">
                            Belum ada aktivitas lamaran siswa binaan.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Kelas Binaan Info Card -->
            <div class="p-4 rounded-card bg-surface-card border border-border-subtle space-y-2 text-xs text-text-secondary">
                <div class="flex items-center gap-2 font-heading font-bold text-text-primary">
                    <i data-lucide="graduation-cap" class="w-4 h-4 text-brand-secondary dark:text-brand-primary"></i>
                    <span>Tugas pokok guru pembimbing</span>
                </div>
                <p class="text-[11px] leading-relaxed">
                    Pastikan seluruh kompetensi yang diklaim siswa telah terverifikasi melalui penilaian unjuk kerja atau ujian praktik kejuruan agar rekomendasi lowongan PKL di industri akurat.
                </p>
            </div>
        </div>
    </div>

    <!-- 5. Widget Informasi BKK & Agenda Vokasi untuk Guru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pengumuman BKK Terkini -->
        <div class="bg-surface-card border border-border-subtle rounded-card shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-border-subtle">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-btn bg-brand-primary/10 text-brand-secondary dark:text-brand-primary flex items-center justify-center">
                        <i data-lucide="megaphone" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-heading font-bold text-text-primary">
                            Pengumuman BKK sekolah
                        </h2>
                        <p class="text-[11px] text-text-secondary">
                            Pemberitahuan rekrutmen industri dan edaran sekolah.
                        </p>
                    </div>
                </div>
                <a href="{{ route('public.announcements') }}" class="text-xs font-semibold text-brand-secondary dark:text-brand-primary hover:underline">
                    Semua pengumuman
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($recentAnnouncements as $announcement)
                    <a href="{{ route('public.announcements.show', $announcement) }}" class="block p-3.5 rounded-btn bg-surface-bg border border-border-subtle hover:border-brand-primary/40 transition-colors group">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            @if ($announcement->prioritas === 'penting')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-state-danger-bg text-state-danger-text border border-state-danger-border">
                                    Penting
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-neutral-100 dark:bg-neutral-800 text-text-secondary">
                                    Informasi
                                </span>
                            @endif
                            <span class="text-[11px] text-text-muted">
                                {{ $announcement->created_at->format('d M Y') }}
                            </span>
                        </div>
                        <h4 class="text-xs font-heading font-bold text-text-primary group-hover:text-brand-secondary dark:group-hover:text-brand-primary transition-colors line-clamp-1">
                            {{ $announcement->judul }}
                        </h4>
                        <p class="text-[11px] text-text-secondary mt-1 line-clamp-2">
                            {{ Str::limit(strip_tags($announcement->isi), 100) }}
                        </p>
                    </a>
                @empty
                    <div class="py-6 text-center text-xs text-text-muted">
                        Belum ada pengumuman baru dari BKK SMKN 1 Cirebon.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Agenda Kegiatan Terdekat -->
        <div class="bg-surface-card border border-border-subtle rounded-card shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-border-subtle">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-btn bg-brand-primary/10 text-brand-secondary dark:text-brand-primary flex items-center justify-center">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-heading font-bold text-text-primary">
                            Agenda vokasi & PKL terdekat
                        </h2>
                        <p class="text-[11px] text-text-secondary">
                            Jadwal kunjungan industri, pembekalan, & seleksi.
                        </p>
                    </div>
                </div>
                <a href="{{ route('public.agendas') }}" class="text-xs font-semibold text-brand-secondary dark:text-brand-primary hover:underline">
                    Semua agenda
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($upcomingAgendas as $agenda)
                    <div class="p-3.5 rounded-btn bg-surface-bg border border-border-subtle flex items-start justify-between gap-3">
                        <div class="space-y-1 min-w-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-state-info-bg text-state-info-text border border-state-info-border">
                                {{ ucfirst($agenda->jenis) }}
                            </span>
                            <h4 class="text-xs font-heading font-bold text-text-primary line-clamp-1">
                                {{ $agenda->judul }}
                            </h4>
                            <p class="text-[11px] text-text-secondary flex items-center gap-1.5">
                                <i data-lucide="map-pin" class="w-3 h-3 text-text-muted"></i>
                                <span class="truncate">{{ $agenda->lokasi ?: 'Aula SMKN 1 Cirebon' }}</span>
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0 text-xs">
                            <span class="font-heading font-bold text-text-primary block">
                                {{ $agenda->tanggal_mulai ? $agenda->tanggal_mulai->format('d M') : '-' }}
                            </span>
                            <span class="text-[10px] text-text-muted block">
                                {{ $agenda->tanggal_mulai ? $agenda->tanggal_mulai->format('Y') : '' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-text-muted">
                        Belum ada agenda terdekat yang dijadwalkan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
