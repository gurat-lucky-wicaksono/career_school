@extends('layouts.teacher')

@section('title', 'Dashboard Guru Kejuruan')

@section('content')
<div class="space-y-8">
    <!-- 1. Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-1">
                <span>Portal Pembimbing Vokasi</span>
                <span class="text-slate-300 dark:text-neutral-600">&bull;</span>
                <span>NIP: {{ $teacher->nip }}</span>
                <span class="text-slate-300 dark:text-neutral-600">&bull;</span>
                <span>{{ $teacher->mata_pelajaran }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Selamat Datang, {{ $user->name }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Validasi kompetensi keahlian siswa, pantau penempatan Praktik Kerja Lapangan (PKL), dan evaluasi portofolio kejuruan.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button variant="secondary" size="md" href="{{ route('teacher.mentoring.index') }}">
                <i data-lucide="users" class="w-4 h-4 mr-2"></i>
                Siswa Bimbingan
            </x-ui.button>
            <x-ui.button variant="primary" size="md" href="{{ route('teacher.skills.index') }}">
                <i data-lucide="award" class="w-4 h-4 mr-2"></i>
                Validasi Keahlian
            </x-ui.button>
        </div>
    </div>

    <!-- 2. Alert Banner Antrean Validasi (Jika ada keahlian pending) -->
    @if ($stats['pending_skills'] > 0)
        <div class="p-5 rounded-2xl bg-amber-50/90 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-amber-900 dark:text-amber-200">
                        Terdapat {{ $stats['pending_skills'] }} Pengajuan Keahlian Menunggu Validasi
                    </h3>
                    <p class="text-xs text-amber-800/90 dark:text-amber-300/80 mt-1 leading-relaxed">
                        Siswa telah menambahkan kompetensi mandiri pada profil mereka. Validasi resmi dari guru pengampu diperlukan agar skor pencocokan lowongan industri (bobot 60%) dapat terhitung optimal.
                    </p>
                </div>
            </div>

            <x-ui.button variant="secondary" size="sm" href="{{ route('teacher.skills.index') }}?status=pending">
                Buka Antrean Validasi
            </x-ui.button>
        </div>
    @endif

    <!-- 3. KPI Stat Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-ui.stat-card 
            title="Siswa Binaan / Rombel" 
            value="{{ number_format($stats['total_students']) }}" 
            icon="users" 
            trend="Aktif di Sekolah" 
            :trendUp="true"
        />

        <x-ui.stat-card 
            title="Menunggu Validasi" 
            value="{{ number_format($stats['pending_skills']) }}" 
            icon="clock" 
            trend="{{ $stats['pending_skills'] > 0 ? 'Perlu Ditinjau' : 'Semua Bersih' }}" 
            :trendUp="$stats['pending_skills'] === 0"
        />

        <x-ui.stat-card 
            title="Keahlian Tervalidasi" 
            value="{{ number_format($stats['validated_skills']) }}" 
            icon="award" 
            trend="Standar Vokasi" 
            :trendUp="true"
        />

        <x-ui.stat-card 
            title="Siswa Aktif PKL / Wawancara" 
            value="{{ number_format($stats['active_internships']) }}" 
            icon="briefcase" 
            trend="Di Mitra Industri" 
            :trendUp="true"
        />
    </div>

    <!-- 4. Main 2-Column Bento Grid: Antrean Validasi & Penempatan DUDI -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Antrean Validasi Keahlian (7 Cols) -->
        <div class="lg:col-span-7 bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                        Antrean Validasi Keahlian Siswa
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Daftar keahlian yang diajukan siswa untuk disahkan kompetensinya.
                    </p>
                </div>

                <a href="{{ route('teacher.skills.index') }}" class="text-xs font-semibold text-brand-primary hover:text-brand-primary/80 transition-colors flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-white/5">
                @forelse ($pendingSkillSubmissions as $submission)
                    <div class="p-4 hover:bg-slate-50/60 dark:hover:bg-neutral-800/40 transition-colors flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <div class="w-9 h-9 rounded-xl bg-brand-primary/10 text-brand-primary font-bold text-xs flex items-center justify-center flex-shrink-0">
                                {{ strtoupper(substr($submission->student->user->name ?? 'S', 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                        {{ $submission->student->user->name }}
                                    </h4>
                                    <span class="text-[10px] text-slate-400 font-mono">
                                        {{ $submission->student->schoolClass?->nama ?? 'PPLG' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-200">
                                        {{ $submission->skill->nama }}
                                    </span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                        Klaim Siswa: <strong>Level {{ $submission->tingkat }}/5</strong>
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
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition-colors"
                            >
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                <span>Sahkan</span>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto">
                            <i data-lucide="check-check" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white mt-2.5">
                            Semua Keahlian Telah Tervalidasi
                        </h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Tidak ada pengajuan keahlian baru yang tertunda saat ini.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Status Penempatan DUDI Siswa Binaan (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/5">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            Progres Penempatan PKL Siswa
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Aktivitas seleksi terbaru di industri mitra DUDI.
                        </p>
                    </div>
                    <a href="{{ route('teacher.mentoring.index') }}" class="text-xs font-semibold text-brand-primary hover:underline">
                        Pantau
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse ($recentApplications as $app)
                        @php
                            $badgeClasses = [
                                'wawancara' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border-indigo-200/60',
                                'diterima' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200/60',
                                'ditinjau' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border-sky-200/60',
                                'tertunda' => 'bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-slate-300 border-transparent',
                            ];
                        @endphp
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-xs text-slate-900 dark:text-white truncate">
                                    {{ $app->student->user->name }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border capitalize {{ $badgeClasses[$app->status] ?? $badgeClasses['tertunda'] }}">
                                    {{ $app->status }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                {{ $app->job->judul }} &bull; <strong class="text-slate-700 dark:text-slate-300">{{ $app->job->company->nama ?? 'Mitra DUDI' }}</strong>
                            </p>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            Belum ada aktivitas lamaran siswa binaan.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Kelas Binaan Info Card -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-neutral-800/50 border border-slate-200/60 dark:border-white/5 space-y-2 text-xs text-slate-600 dark:text-slate-400">
                <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white">
                    <i data-lucide="graduation-cap" class="w-4 h-4 text-emerald-600"></i>
                    <span>Tugas Pokok Guru Pembimbing</span>
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
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <i data-lucide="megaphone" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            Pengumuman BKK Sekolah
                        </h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Pemberitahuan rekrutmen industri dan edaran sekolah.
                        </p>
                    </div>
                </div>
                <a href="{{ route('public.announcements') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                    Semua Pengumuman
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($recentAnnouncements as $announcement)
                    <a href="{{ route('public.announcements.show', $announcement) }}" class="block p-3.5 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5 hover:border-emerald-500/40 transition-all group">
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
                        <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors line-clamp-1">
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
                            Agenda Vokasi & PKL Terdekat
                        </h2>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Jadwal kunjungan industri, pembekalan, & seleksi.
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
