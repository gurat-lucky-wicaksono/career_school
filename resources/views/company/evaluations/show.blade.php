@extends('layouts.company')

@section('title', 'Detail Evaluasi Kinerja PKL')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with status and actions -->
    <div class="bg-surface-card border border-border-subtle rounded-btn p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold font-heading text-text-primary">Lembar Evaluasi Praktik Kerja Lapangan</h2>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $evaluation->status_badge_class }}">
                        {{ $evaluation->status_label }}
                    </span>
                </div>
                <p class="text-xs text-text-secondary mt-1">
                    Nomor Transkrip / Sertifikat: <span class="font-sans font-bold text-text-primary">{{ $evaluation->nomor_sertifikat ?? '-' }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto">
                @if($evaluation->status !== 'verified')
                    <x-ui.button variant="primary" size="sm" href="{{ route('company.evaluations.edit', $evaluation) }}">
                        Edit Penilaian
                    </x-ui.button>
                @endif
                <x-ui.button variant="secondary" size="sm" href="{{ route('company.evaluations.index') }}">
                    Daftar Evaluasi
                </x-ui.button>
            </div>
        </div>
    </div>

    <!-- Student & Partner Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-surface-card border border-border-subtle rounded-btn p-5 shadow-xs space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-text-muted flex items-center gap-1.5">
                <i data-lucide="user" class="w-4 h-4 text-brand-primary"></i>
                Identitas Siswa Praktikan
            </h3>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between py-1 border-b border-border-subtle/50">
                    <span class="text-text-secondary">Nama Lengkap</span>
                    <span class="font-bold text-text-primary">{{ $evaluation->student->user->name ?? 'Siswa' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-border-subtle/50">
                    <span class="text-text-secondary">NISN</span>
                    <span class="font-sans text-text-primary">{{ $evaluation->student->nisn }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-border-subtle/50">
                    <span class="text-text-secondary">Kelas / Angkatan</span>
                    <span class="text-text-primary">{{ $evaluation->student->schoolClass->nama ?? '-' }} ({{ $evaluation->student->angkatan }})</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-text-secondary">Kompetensi Keahlian</span>
                    <span class="font-medium text-text-primary">{{ $evaluation->student->major->nama ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-surface-card border border-border-subtle rounded-btn p-5 shadow-xs space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-text-muted flex items-center gap-1.5">
                <i data-lucide="building-2" class="w-4 h-4 text-brand-primary"></i>
                Mitra Industri & Posisi
            </h3>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between py-1 border-b border-border-subtle/50">
                    <span class="text-text-secondary">Perusahaan (DUDI)</span>
                    <span class="font-bold text-text-primary">{{ $evaluation->company->nama ?? 'Mitra Industri' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-border-subtle/50">
                    <span class="text-text-secondary">Posisi Magang</span>
                    <span class="font-medium text-text-primary">{{ $evaluation->application->job->judul ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-border-subtle/50">
                    <span class="text-text-secondary">Tanggal Pengiriman</span>
                    <span class="text-text-primary">{{ $evaluation->submitted_at ? $evaluation->submitted_at->translatedFormat('d F Y, H:i') : 'Belum Dikirim' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-text-secondary">Status Verifikasi Guru</span>
                    <span class="font-medium {{ $evaluation->status === 'verified' ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $evaluation->status === 'verified' ? 'Disahkan (' . ($evaluation->verified_at?->translatedFormat('d M Y') ?? '-') . ')' : 'Menunggu' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Scorecard & Rubric Breakdown -->
    <div class="bg-surface-card border border-border-subtle rounded-btn p-6 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-border-subtle gap-4">
            <div>
                <h3 class="text-base font-bold font-heading text-text-primary">Rincian Capaian Kinerja Magang</h3>
                <p class="text-xs text-text-secondary mt-0.5">Akumulasi penilaian berdasarkan 3 pilar kompetensi vokasi</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <span class="text-xs text-text-muted">Skor Komposit</span>
                    <div class="text-3xl font-extrabold font-sans text-text-primary">{{ number_format($evaluation->nilai_akhir, 1) }}</div>
                </div>
                <div class="px-3 py-1.5 rounded-btn border {{ $evaluation->predicate_badge_class }} text-center">
                    <div class="text-sm font-bold">{{ $evaluation->predikat }}</div>
                    <div class="text-[10px] uppercase font-semibold">{{ $evaluation->predicate_label }}</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-btn border border-border-subtle bg-surface-bg/50">
                <div class="text-[11px] font-semibold text-text-muted uppercase">Kompetensi Teknis (40%)</div>
                <div class="text-2xl font-bold font-sans text-text-primary mt-1">{{ number_format($evaluation->nilai_teknis, 1) }}</div>
                <div class="text-[11px] text-text-secondary mt-1">Kontribusi skor: {{ number_format($evaluation->nilai_teknis * 0.4, 1) }} poin</div>
            </div>

            <div class="p-4 rounded-btn border border-border-subtle bg-surface-bg/50">
                <div class="text-[11px] font-semibold text-text-muted uppercase">Disiplin & Etika (30%)</div>
                <div class="text-2xl font-bold font-sans text-text-primary mt-1">{{ number_format($evaluation->nilai_disiplin, 1) }}</div>
                <div class="text-[11px] text-text-secondary mt-1">Kontribusi skor: {{ number_format($evaluation->nilai_disiplin * 0.3, 1) }} poin</div>
            </div>

            <div class="p-4 rounded-btn border border-border-subtle bg-surface-bg/50">
                <div class="text-[11px] font-semibold text-text-muted uppercase">Kerjasama & Komunikasi (30%)</div>
                <div class="text-2xl font-bold font-sans text-text-primary mt-1">{{ number_format($evaluation->nilai_kerjasama, 1) }}</div>
                <div class="text-[11px] text-text-secondary mt-1">Kontribusi skor: {{ number_format($evaluation->nilai_kerjasama * 0.3, 1) }} poin</div>
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
                    {{ $evaluation->catatan_industri ?: 'Tidak ada catatan tertulis dari pembimbing industri.' }}
                </p>
            </div>

            @if($evaluation->catatan_guru)
                <div class="p-4 rounded-btn border border-emerald-200/60 bg-emerald-50/30 dark:bg-emerald-950/20 dark:border-emerald-800/60 space-y-2">
                    <div class="flex items-center gap-2 text-xs font-bold text-emerald-800 dark:text-emerald-300">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                        <span>Catatan Validasi Guru Pembimbing:</span>
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
