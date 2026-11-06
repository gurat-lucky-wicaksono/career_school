@extends('layouts.student')

@section('title', 'Transkrip Nilai PKL Siswa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with status and actions -->
    <div class="bg-surface-card border border-border-subtle rounded-btn p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold font-heading text-text-primary">Transkrip Hasil Evaluasi PKL</h2>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $evaluation->status_badge_class }}">
                        {{ $evaluation->status_label }}
                    </span>
                </div>
                <p class="text-xs text-text-secondary mt-1">
                    Nomor Sertifikat: <span class="font-mono font-bold text-text-primary">{{ $evaluation->nomor_sertifikat ?? '-' }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto">
                @if($evaluation->status === 'verified')
                    <a 
                        href="{{ route('student.evaluations.print', $evaluation) }}" 
                        target="_blank"
                        class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-btn bg-emerald-600 text-white hover:bg-emerald-700 transition-all shadow-xs flex items-center gap-2"
                    >
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>Cetak Sertifikat Resmi</span>
                    </a>
                @endif
                <a 
                    href="{{ route('student.evaluations.index') }}" 
                    class="px-3.5 py-2 text-xs font-semibold rounded-btn border border-border-subtle text-text-secondary hover:text-text-primary hover:bg-surface-bg transition-colors flex items-center gap-1.5"
                >
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Partner & Placement Details -->
    <div class="bg-surface-card border border-border-subtle rounded-btn p-5 shadow-xs">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="space-y-2">
                <div class="flex justify-between py-1 border-b border-border-subtle/50">
                    <span class="text-text-secondary">Mitra Industri (DUDI)</span>
                    <span class="font-bold text-text-primary">{{ $evaluation->company->nama ?? 'Mitra Industri' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-border-subtle/50">
                    <span class="text-text-secondary">Bidang Industri</span>
                    <span class="text-text-primary">{{ $evaluation->company->bidang ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-text-secondary">Lokasi Magang</span>
                    <span class="text-text-primary">{{ $evaluation->application->job->lokasi ?? '-' }}</span>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex justify-between py-1 border-b border-border-subtle/50">
                    <span class="text-text-secondary">Posisi / Formasi PKL</span>
                    <span class="font-bold text-text-primary">{{ $evaluation->application->job->judul ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-border-subtle/50">
                    <span class="text-text-secondary">Tanggal Pengiriman Nilai</span>
                    <span class="text-text-primary">{{ $evaluation->submitted_at ? $evaluation->submitted_at->translatedFormat('d F Y, H:i') : '-' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-text-secondary">Pengesahan Sekolah</span>
                    <span class="font-medium {{ $evaluation->status === 'verified' ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $evaluation->status === 'verified' ? 'Disahkan (' . ($evaluation->verified_at?->translatedFormat('d F Y') ?? '-') . ')' : 'Menunggu Validasi' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Scorecard & Rubric Breakdown -->
    <div class="bg-surface-card border border-border-subtle rounded-btn p-6 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-border-subtle gap-4">
            <div>
                <h3 class="text-base font-bold font-heading text-text-primary">Lembar Nilai Capaian Kompetensi</h3>
                <p class="text-xs text-text-secondary mt-0.5">Penilaian resmi kinerja magang dari pembimbing industri</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <span class="text-xs text-text-muted">Skor Komposit</span>
                    <div class="text-3xl font-extrabold font-mono text-text-primary">{{ number_format($evaluation->nilai_akhir, 1) }}</div>
                </div>
                <div class="px-3 py-1.5 rounded-btn border {{ $evaluation->predicate_badge_class }} text-center">
                    <div class="text-sm font-bold">{{ $evaluation->predikat }}</div>
                    <div class="text-[10px] uppercase font-semibold">{{ $evaluation->predicate_label }}</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-btn border border-border-subtle bg-surface-bg/50">
                <div class="text-[11px] font-semibold text-text-muted uppercase">1. Teknis Kejuruan (40%)</div>
                <div class="text-2xl font-bold font-mono text-text-primary mt-1">{{ number_format($evaluation->nilai_teknis, 1) }}</div>
                <div class="text-[11px] text-text-secondary mt-1">Bobot: {{ number_format($evaluation->nilai_teknis * 0.4, 1) }} poin</div>
            </div>

            <div class="p-4 rounded-btn border border-border-subtle bg-surface-bg/50">
                <div class="text-[11px] font-semibold text-text-muted uppercase">2. Disiplin & Etika (30%)</div>
                <div class="text-2xl font-bold font-mono text-text-primary mt-1">{{ number_format($evaluation->nilai_disiplin, 1) }}</div>
                <div class="text-[11px] text-text-secondary mt-1">Bobot: {{ number_format($evaluation->nilai_disiplin * 0.3, 1) }} poin</div>
            </div>

            <div class="p-4 rounded-btn border border-border-subtle bg-surface-bg/50">
                <div class="text-[11px] font-semibold text-text-muted uppercase">3. Kerjasama & Komunikasi (30%)</div>
                <div class="text-2xl font-bold font-mono text-text-primary mt-1">{{ number_format($evaluation->nilai_kerjasama, 1) }}</div>
                <div class="text-[11px] text-text-secondary mt-1">Bobot: {{ number_format($evaluation->nilai_kerjasama * 0.3, 1) }} poin</div>
            </div>
        </div>

        <!-- Remarks Section -->
        <div class="space-y-4 pt-2">
            <div class="p-4 rounded-btn border border-border-subtle bg-surface-bg/30 space-y-2">
                <div class="flex items-center gap-2 text-xs font-bold text-text-primary">
                    <i data-lucide="message-square" class="w-4 h-4 text-brand-primary"></i>
                    <span>Catatan & Ulasan Pembimbing Industri:</span>
                </div>
                <p class="text-xs sm:text-sm text-text-secondary whitespace-pre-line leading-relaxed">
                    {{ $evaluation->catatan_industri ?: 'Tidak ada catatan tertulis.' }}
                </p>
            </div>

            @if($evaluation->catatan_guru)
                <div class="p-4 rounded-btn border border-emerald-200/60 bg-emerald-50/30 dark:bg-emerald-950/20 dark:border-emerald-800/60 space-y-2">
                    <div class="flex items-center gap-2 text-xs font-bold text-emerald-800 dark:text-emerald-300">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                        <span>Catatan Guru Pembimbing Sekolah:</span>
                    </div>
                    <p class="text-xs sm:text-sm text-text-secondary dark:text-emerald-200/90 whitespace-pre-line leading-relaxed">
                        {{ $evaluation->catatan_guru }}
                    </p>
                    @if($evaluation->teacher)
                        <div class="text-[11px] text-text-muted mt-2 pt-2 border-t border-emerald-200/40">
                            Disahkan oleh: <span class="font-semibold text-text-primary">{{ $evaluation->teacher->user->name ?? 'Guru Pembimbing' }}</span> (NIP: {{ $evaluation->teacher->nip }})
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
