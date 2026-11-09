@extends('layouts.student')

@section('title', 'Sertifikat & Nilai PKL Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-heading font-bold text-text-primary tracking-tight">
                Sertifikat & Transkrip PKL
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Pantau capaian evaluasi kinerja magang dari mitra industri dan unduh sertifikat resmi selesai Praktik Kerja Lapangan.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <x-ui.button variant="secondary" size="md" href="{{ route('student.applications.index') }}">
                Riwayat Lamaran
            </x-ui.button>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-surface-card border border-border-subtle p-5 rounded-btn shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-text-muted uppercase tracking-wider">Penempatan PKL Diterima</p>
                    <h3 class="text-2xl font-bold font-heading text-text-primary mt-1">{{ $stats['total_accepted'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center">
                    <i data-lucide="briefcase" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-text-secondary mt-2">Program magang yang telah disetujui</p>
        </div>

        <div class="bg-surface-card border border-border-subtle p-5 rounded-btn shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-text-muted uppercase tracking-wider">Sertifikat Resmi Terbit</p>
                    <h3 class="text-2xl font-bold font-heading text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['total_verified'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="award" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-text-secondary mt-2">Divalidasi oleh pembimbing sekolah</p>
        </div>

        <div class="bg-surface-card border border-border-subtle p-5 rounded-btn shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-text-muted uppercase tracking-wider">Skor Tertinggi Capaian</p>
                    <h3 class="text-2xl font-bold font-heading text-text-primary mt-1 font-sans">
                        {{ $stats['highest_score'] > 0 ? number_format($stats['highest_score'], 1) : '-' }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-text-secondary mt-2">Skor kumulatif kompetensi kejuruan</p>
        </div>
    </div>

    <!-- Internship Placements and Transcripts List -->
    <div class="space-y-4">
        <h2 class="text-base font-bold font-heading text-text-primary">Daftar Tempat PKL & Transkrip Kinerja</h2>

        @forelse($acceptedApplications as $app)
            @php
                $eval = $app->evaluation;
                $isVerified = $eval && $eval->status === 'verified';
                $isSubmitted = $eval && $eval->status === 'submitted';
            @endphp
            <div class="bg-surface-card border border-border-subtle rounded-btn p-5 shadow-xs transition-all hover:border-border-strong space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-border-subtle">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-btn bg-surface-bg border border-border-subtle flex items-center justify-center flex-shrink-0 text-text-secondary">
                            <i data-lucide="building-2" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold font-heading text-text-primary">{{ $app->job->judul ?? 'Magang Kejuruan' }}</h3>
                                @if($isVerified)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300">
                                        <i data-lucide="check-circle" class="w-3 h-3"></i>
                                        Sertifikat Sah
                                    </span>
                                @elseif($isSubmitted)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full border bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        Menunggu Validasi Sekolah
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300">
                                        <i data-lucide="hourglass" class="w-3 h-3"></i>
                                        Proses Evaluasi Mitra
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-text-secondary mt-0.5">
                                {{ $app->job->company->nama ?? 'Mitra Industri' }} &bull; {{ $app->job->lokasi ?? 'Kantor Perusahaan' }}
                            </p>
                        </div>
                    </div>

                    @if($isVerified)
                        <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                            <x-ui.button 
                                variant="secondary" 
                                size="sm" 
                                href="{{ route('student.evaluations.show', $eval) }}"
                            >
                                Lihat Transkrip
                            </x-ui.button>
                            <x-ui.button 
                                variant="primary" 
                                size="sm" 
                                href="{{ route('student.evaluations.print', $eval) }}" 
                                target="_blank"
                            >
                                Cetak Sertifikat PKL
                            </x-ui.button>
                        </div>
                    @elseif($isSubmitted)
                        <div class="self-start sm:self-auto shrink-0">
                            <x-ui.button 
                                variant="primary" 
                                size="sm" 
                                href="{{ route('student.evaluations.show', $eval) }}"
                            >
                                Lihat Rincian Nilai
                            </x-ui.button>
                        </div>
                    @endif
                </div>

                <!-- Score breakdown if evaluated -->
                @if($eval && ($isSubmitted || $isVerified))
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-surface-bg/50 p-3.5 rounded-btn border border-border-subtle/80">
                        <div>
                            <span class="text-[10px] text-text-muted uppercase font-semibold">1. Teknis (40%)</span>
                            <div class="text-base font-bold font-sans text-text-primary mt-0.5">{{ number_format($eval->nilai_teknis, 1) }}</div>
                        </div>
                        <div>
                            <span class="text-[10px] text-text-muted uppercase font-semibold">2. Disiplin (30%)</span>
                            <div class="text-base font-bold font-sans text-text-primary mt-0.5">{{ number_format($eval->nilai_disiplin, 1) }}</div>
                        </div>
                        <div>
                            <span class="text-[10px] text-text-muted uppercase font-semibold">3. Kerjasama (30%)</span>
                            <div class="text-base font-bold font-sans text-text-primary mt-0.5">{{ number_format($eval->nilai_kerjasama, 1) }}</div>
                        </div>
                        <div class="border-l border-border-subtle pl-3">
                            <span class="text-[10px] text-text-muted uppercase font-semibold">Skor Akhir & Predikat</span>
                            <div class="text-base font-bold font-sans text-text-primary mt-0.5 flex items-center gap-1.5">
                                <span>{{ number_format($eval->nilai_akhir, 1) }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-bold border {{ $eval->predicate_badge_class }}">
                                    {{ $eval->predikat }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($eval->catatan_industri)
                        <div class="text-xs text-text-secondary bg-surface-bg/30 p-3 rounded-btn border border-border-subtle/60">
                            <span class="font-semibold text-text-primary">Catatan Supervisor:</span>
                            <p class="mt-0.5 italic">"{{ $eval->catatan_industri }}"</p>
                        </div>
                    @endif
                @else
                    <div class="p-3.5 rounded-btn bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/50 text-xs text-amber-800 dark:text-amber-300 flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0"></i>
                        <span>Supervisor mitra industri sedang melakukan evaluasi berkala. Nilai dan sertifikat akan tersedia setelah dikirim dan diverifikasi guru pembimbing.</span>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-surface-card border border-border-subtle rounded-btn p-10 text-center text-text-muted">
                <i data-lucide="award" class="w-12 h-12 mx-auto mb-3 text-text-muted/40"></i>
                <h3 class="text-base font-bold font-heading text-text-primary">Belum Ada Riwayat PKL Diterima</h3>
                <p class="text-xs text-text-secondary mt-1 max-w-sm mx-auto">
                    Lamar formasi magang industri melalui menu Eksplorasi Lowongan. Setelah diterima dan menyelesaikan PKL, sertifikat akan tampil di sini.
                </p>
                <div class="mt-4">
                    <x-ui.button variant="primary" size="sm" href="{{ route('student.jobs.index') }}">
                        Eksplorasi Lowongan PKL
                    </x-ui.button>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
