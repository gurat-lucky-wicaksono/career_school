@extends('layouts.teacher')

@section('title', 'Tinjau & Validasi Evaluasi PKL')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with status and back button -->
    <div class="bg-surface-card border border-border-subtle rounded-btn p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold font-heading text-text-primary">Validasi Nilai Praktik Kerja Lapangan</h2>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border {{ $evaluation->status_badge_class }}">
                        {{ $evaluation->status_label }}
                    </span>
                </div>
                <p class="text-xs text-text-secondary mt-1">
                    Nomor Sertifikat: <span class="font-sans font-bold text-text-primary">{{ $evaluation->nomor_sertifikat ?? 'Akan diterbitkan saat disahkan' }}</span>
                </p>
            </div>

            <x-ui.button 
                variant="secondary" 
                size="md" 
                href="{{ route('teacher.evaluations.index') }}"
                class="self-start sm:self-auto"
            >
                Kembali ke Daftar
            </x-ui.button>
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
                    <span class="text-text-secondary">Kelas & Angkatan</span>
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
                Mitra Industri & Supervisor
            </h3>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between py-1 border-b border-border-subtle/50">
                    <span class="text-text-secondary">Nama Perusahaan</span>
                    <span class="font-bold text-text-primary">{{ $evaluation->company->nama ?? 'Mitra Industri' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-border-subtle/50">
                    <span class="text-text-secondary">Posisi PKL</span>
                    <span class="font-medium text-text-primary">{{ $evaluation->application->job->judul ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-border-subtle/50">
                    <span class="text-text-secondary">Waktu Kirim Nilai</span>
                    <span class="text-text-primary">{{ $evaluation->submitted_at ? $evaluation->submitted_at->translatedFormat('d F Y, H:i') : '-' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-text-secondary">Bidang Industri</span>
                    <span class="text-text-primary">{{ $evaluation->company->bidang ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Scorecard & Rubric Breakdown -->
    <div class="bg-surface-card border border-border-subtle rounded-btn p-6 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-border-subtle gap-4">
            <div>
                <h3 class="text-base font-bold font-heading text-text-primary">Rubrik & Nilai Evaluasi Kinerja</h3>
                <p class="text-xs text-text-secondary mt-0.5">Penilaian dari pembimbing lapangan perusahaan mitra</p>
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

        <!-- Remarks Section from Industry -->
        <div class="p-4 rounded-btn border border-border-subtle bg-surface-bg/30 space-y-2">
            <div class="flex items-center gap-2 text-xs font-bold text-text-primary">
                <i data-lucide="message-square" class="w-4 h-4 text-brand-primary"></i>
                <span>Catatan & Rekomendasi Supervisor Industri:</span>
            </div>
            <p class="text-xs sm:text-sm text-text-secondary whitespace-pre-line leading-relaxed">
                {{ $evaluation->catatan_industri ?: 'Tidak ada catatan tertulis dari pembimbing industri.' }}
            </p>
        </div>
    </div>

    <!-- Verification / Endorsement Box -->
    @if($evaluation->status === 'verified')
        <div class="bg-surface-card border border-emerald-200/60 dark:border-emerald-800/60 rounded-btn p-6 shadow-xs space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="check-check" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold font-heading text-emerald-800 dark:text-emerald-300">Nilai PKL Telah Diverifikasi & Disahkan</h3>
                    <p class="text-xs text-text-secondary">
                        Disahkan pada {{ $evaluation->verified_at ? $evaluation->verified_at->translatedFormat('d F Y, H:i') : '-' }} oleh {{ $evaluation->teacher->user->name ?? 'Guru Pembimbing' }} (NIP: {{ $evaluation->teacher->nip ?? '-' }})
                    </p>
                </div>
            </div>

            @if($evaluation->catatan_guru)
                <div class="p-4 rounded-btn bg-emerald-50/40 dark:bg-emerald-950/20 border border-emerald-200/40 space-y-1">
                    <div class="text-xs font-semibold text-emerald-800 dark:text-emerald-300">Catatan Bimbingan Sekolah:</div>
                    <p class="text-xs sm:text-sm text-text-secondary dark:text-emerald-200/90 whitespace-pre-line">{{ $evaluation->catatan_guru }}</p>
                </div>
            @endif
        </div>
    @else
        <!-- Verification Form for Teacher -->
        <form action="{{ route('teacher.evaluations.verify', $evaluation) }}" method="POST" class="bg-surface-card border border-border-subtle rounded-btn p-6 shadow-xs space-y-4">
            @csrf
            <div>
                <h3 class="text-base font-bold font-heading text-text-primary">Validasi & Pengesahan Sekolah</h3>
                <p class="text-xs text-text-secondary mt-1">Dengan mengesahkan evaluasi ini, nilai akhir akan dikunci dan transkrip resmi sertifikat PKL dapat diunduh oleh siswa.</p>
            </div>

            <div>
                <label for="catatan_guru" class="block text-xs font-semibold text-text-primary mb-1.5">
                    Catatan Guru Pembimbing Kejuruan (Opsional)
                </label>
                <textarea 
                    id="catatan_guru" 
                    name="catatan_guru" 
                    rows="3" 
                    placeholder="Berikan catatan apresiasi atau evaluasi pembinaan untuk siswa..."
                    class="w-full p-3 text-xs sm:text-sm bg-surface-bg border border-border-subtle rounded-btn focus:outline-hidden focus:border-brand-primary text-text-primary"
                >{{ old('catatan_guru') }}</textarea>
                @error('catatan_guru')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2 flex items-center justify-end">
                <x-ui.button 
                    type="submit" 
                    variant="primary"
                >
                    Sahkan & Kunci Nilai PKL
                </x-ui.button>
            </div>
        </form>
    @endif
</div>
@endsection
