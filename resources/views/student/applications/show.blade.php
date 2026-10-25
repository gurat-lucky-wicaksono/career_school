@extends('layouts.student')

@section('title', 'Pelacakan Lamaran - ' . $application->job->judul)

@section('content')
<div class="space-y-6">
    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <x-ui.button 
            variant="secondary" 
            size="sm" 
            href="{{ route('student.applications.index') }}"
        >
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i>
            Kembali ke Daftar Lamaran
        </x-ui.button>

        <x-ui.button 
            variant="ghost" 
            size="sm" 
            href="{{ route('student.jobs.show', $application->job) }}"
        >
            <span>Buka Detail Lowongan</span>
            <i data-lucide="external-link" class="w-3.5 h-3.5 ml-1.5"></i>
        </x-ui.button>
    </div>

    <!-- 1. Hero Card Lamaran -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4 min-w-0">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-neutral-800 border border-slate-200/60 dark:border-white/10 text-slate-800 dark:text-slate-100 font-bold text-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                    {{ strtoupper(substr($application->job->company->nama ?? 'P', 0, 2)) }}
                </div>

                <div class="space-y-1.5 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold text-brand-primary">
                            {{ $application->job->company->nama ?? 'Mitra Industri' }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $application->job->jenis === 'magang' ? 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200/60' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60' }}">
                            {{ $application->job->jenis === 'magang' ? 'Magang / PKL' : 'Penuh Waktu' }}
                        </span>
                        <span class="text-xs text-slate-400 font-mono">
                            #APP-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                        {{ $application->job->judul }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400 pt-1">
                        <span class="flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                            {{ $application->job->lokasi }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                            Dikirim: {{ $application->created_at->translatedFormat('d F Y, H:i') }} WIB
                        </span>
                        @if ($application->skor_kecocokan)
                            <span class="flex items-center gap-1 font-semibold text-slate-700 dark:text-slate-200">
                                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-brand-primary"></i>
                                Skor Kecocokan: {{ round($application->skor_kecocokan) }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Current Status Badge & Action -->
            <div class="flex flex-col sm:items-end gap-3 flex-shrink-0">
                @php
                    $statusStyles = [
                        'tertunda' => ['label' => 'Menunggu Review', 'badge' => 'bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-slate-300 border-slate-200'],
                        'ditinjau' => ['label' => 'Sedang Ditinjau', 'badge' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border-sky-200'],
                        'wawancara' => ['label' => 'Panggilan Wawancara', 'badge' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border-indigo-200'],
                        'diterima' => ['label' => 'Diterima di Perusahaan', 'badge' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200'],
                        'ditolak' => ['label' => 'Belum Lolos', 'badge' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border-rose-200'],
                    ];
                    $currentBadge = $statusStyles[$application->status] ?? $statusStyles['tertunda'];
                @endphp

                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold border {{ $currentBadge['badge'] }}">
                    <span class="w-2 h-2 rounded-full {{ $application->status === 'wawancara' ? 'bg-indigo-600 animate-pulse' : ($application->status === 'diterima' ? 'bg-emerald-600' : ($application->status === 'ditolak' ? 'bg-rose-600' : 'bg-slate-400')) }}"></span>
                    <span>{{ $currentBadge['label'] }}</span>
                </span>

                @if ($application->status === 'tertunda')
                    <form method="POST" action="{{ route('student.applications.destroy', $application) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan lamaran ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-rose-600 hover:text-rose-700 font-semibold hover:underline">
                            Batalkan Pengajuan Lamaran
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. Visual Recruitment Timeline Stepper -->
    @php
        $statusLevels = [
            'tertunda' => 1,
            'ditinjau' => 2,
            'wawancara' => 3,
            'diterima' => 4,
            'ditolak' => 4,
        ];
        $currentLevel = $statusLevels[$application->status] ?? 1;
    @endphp
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-6">
        <div class="pb-3 border-b border-slate-100 dark:border-white/5">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                Alur Tahapan Rekrutmen & Seleksi
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Pemantauan status berkas secara transparan dari pihak sekolah dan mitra industri.
            </p>
        </div>

        <!-- 4-Step Linear Progress Bar -->
        <div class="relative">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
                <!-- Step 1: Lamaran Terkirim -->
                <div class="p-4 rounded-xl border {{ $currentLevel >= 1 ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : 'bg-slate-50 dark:bg-neutral-800/40 border-slate-200/60 dark:border-white/5' }} space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="w-7 h-7 rounded-lg {{ $currentLevel >= 1 ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-neutral-700 text-slate-500' }} text-xs font-bold flex items-center justify-center">
                            1
                        </span>
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 dark:text-white">Lamaran Terkirim</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $application->created_at->translatedFormat('d M Y, H:i') }}
                        </p>
                    </div>
                </div>

                <!-- Step 2: Peninjauan Berkas -->
                <div class="p-4 rounded-xl border {{ $currentLevel >= 2 ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : ($currentLevel === 1 ? 'bg-sky-50/50 dark:bg-sky-950/20 border-sky-200 dark:border-sky-800/40' : 'bg-slate-50 dark:bg-neutral-800/40 border-slate-200/60 dark:border-white/5') }} space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="w-7 h-7 rounded-lg {{ $currentLevel >= 2 ? 'bg-emerald-600 text-white' : ($currentLevel === 1 ? 'bg-sky-600 text-white animate-pulse' : 'bg-slate-200 dark:bg-neutral-700 text-slate-500') }} text-xs font-bold flex items-center justify-center">
                            2
                        </span>
                        @if ($currentLevel >= 2)
                            <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        @else
                            <i data-lucide="clock" class="w-4 h-4 text-sky-600"></i>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 dark:text-white">Peninjauan HRD</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            @if ($application->reviewed_at)
                                Selesai ditinjau {{ $application->reviewed_at->translatedFormat('d M Y') }}
                            @else
                                {{ $currentLevel === 1 ? 'Dalam antrean seleksi berkas' : 'Berkas portofolio dievaluasi' }}
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Step 3: Wawancara Teknis -->
                <div class="p-4 rounded-xl border {{ $currentLevel === 3 ? 'bg-indigo-50/80 dark:bg-indigo-950/30 border-indigo-300 dark:border-indigo-800/60 shadow-sm' : ($currentLevel > 3 ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : 'bg-slate-50 dark:bg-neutral-800/40 border-slate-200/60 dark:border-white/5') }} space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="w-7 h-7 rounded-lg {{ $currentLevel === 3 ? 'bg-indigo-600 text-white animate-bounce' : ($currentLevel > 3 ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-neutral-700 text-slate-500') }} text-xs font-bold flex items-center justify-center">
                            3
                        </span>
                        @if ($currentLevel > 3)
                            <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        @elseif ($currentLevel === 3)
                            <i data-lucide="calendar-check-2" class="w-4 h-4 text-indigo-600"></i>
                        @else
                            <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 dark:text-white">Wawancara / Uji Teknis</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $currentLevel === 3 ? 'Panggilan wawancara aktif' : ($currentLevel > 3 ? 'Tahap wawancara terlaksana' : 'Menunggu tahap berkas selesai') }}
                        </p>
                    </div>
                </div>

                <!-- Step 4: Keputusan Akhir -->
                <div class="p-4 rounded-xl border {{ $application->status === 'diterima' ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-300 dark:border-emerald-800/60 shadow-sm' : ($application->status === 'ditolak' ? 'bg-rose-50/80 dark:bg-rose-950/30 border-rose-300 dark:border-rose-800/60' : 'bg-slate-50 dark:bg-neutral-800/40 border-slate-200/60 dark:border-white/5') }} space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="w-7 h-7 rounded-lg {{ $application->status === 'diterima' ? 'bg-emerald-600 text-white' : ($application->status === 'ditolak' ? 'bg-rose-600 text-white' : 'bg-slate-200 dark:bg-neutral-700 text-slate-500') }} text-xs font-bold flex items-center justify-center">
                            4
                        </span>
                        @if ($application->status === 'diterima')
                            <i data-lucide="award" class="w-4 h-4 text-emerald-600"></i>
                        @elseif ($application->status === 'ditolak')
                            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                        @else
                            <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 dark:text-white">Keputusan Akhir</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            @if ($application->status === 'diterima')
                                Lolos seleksi penerimaan
                            @elseif ($application->status === 'ditolak')
                                Belum memenuhi kriteria
                            @else
                                Pengumuman kelulusan
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Details Grid: HRD Notes & Submitted Documents -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: HRD Feedback & Cover Note (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Catatan HRD Card -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/5">
                    <div class="flex items-center gap-2">
                        <i data-lucide="message-square" class="w-4 h-4 text-brand-primary"></i>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                            Instruksi & Catatan HRD Perusahaan
                        </h3>
                    </div>
                    @if ($application->reviewer)
                        <span class="text-[11px] text-slate-400">
                            Peninjau: {{ $application->reviewer->name }}
                        </span>
                    @endif
                </div>

                @if ($application->catatan_hrd)
                    <div class="p-4 rounded-xl {{ $application->status === 'wawancara' ? 'bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200/80 text-indigo-950 dark:text-indigo-200' : 'bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/60 dark:border-white/5 text-slate-700 dark:text-slate-300' }} text-xs leading-relaxed whitespace-pre-line font-sans">
                        {{ $application->catatan_hrd }}
                    </div>
                @else
                    <div class="py-6 text-center text-xs text-slate-400">
                        Belum ada catatan khusus dari HRD mitra industri. Pantau secara berkala saat status berlanjut.
                    </div>
                @endif
            </div>

            <!-- Surat Pengantar / Catatan Siswa Card -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="pb-3 border-b border-slate-100 dark:border-white/5">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        Surat Pengantar yang Dikirimkan
                    </h3>
                </div>

                @if ($application->pesan)
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/60 dark:border-white/5 text-xs text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                        {{ $application->pesan }}
                    </div>
                @else
                    <div class="py-4 text-center text-xs text-slate-400">
                        Tidak ada catatan pengantar khusus yang disertakan pada saat mengajukan lamaran.
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Profile Snapshot & Job Info (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Posisi & Kebutuhan Keahlian Card -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="pb-3 border-b border-slate-100 dark:border-white/5">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        Kebutuhan Keahlian Lowongan
                    </h3>
                </div>

                <div class="space-y-2">
                    @foreach ($application->job->skills as $skill)
                        <div class="flex items-center justify-between text-xs p-2 rounded-lg bg-slate-50 dark:bg-neutral-800/40">
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $skill->nama }}</span>
                            @if ($skill->pivot->wajib)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300">
                                    Wajib
                                </span>
                            @else
                                <span class="text-slate-400 text-[10px]">Nilai Tambah</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- BKK Support Info -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-neutral-800/50 border border-slate-200/60 dark:border-white/5 space-y-2 text-xs text-slate-600 dark:text-slate-400">
                <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white">
                    <i data-lucide="help-circle" class="w-4 h-4 text-brand-primary"></i>
                    <span>Bantuan Layanan BKK</span>
                </div>
                <p class="text-[11px] leading-relaxed">
                    Jika terdapat kendala atau pertanyaan terkait jadwal wawancara dengan mitra industri, silakan berkonsultasi langsung dengan Tim BKK SMKN 1 Cirebon atau guru pembimbing kejuruan Anda.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
