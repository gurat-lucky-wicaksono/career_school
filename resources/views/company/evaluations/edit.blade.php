@extends('layouts.company')

@section('title', 'Edit Evaluasi Kinerja PKL')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Student & Internship Information Card -->
    <div class="bg-surface-card border border-border-subtle rounded-btn p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-brand-primary/10 text-brand-primary font-bold text-base flex items-center justify-center flex-shrink-0">
                    {{ strtoupper(substr($evaluation->student->user->name ?? 'S', 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-bold font-heading text-text-primary">{{ $evaluation->student->user->name ?? 'Siswa Praktikan' }}</h2>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full border {{ $evaluation->status_badge_class }}">
                            {{ $evaluation->status_label }}
                        </span>
                    </div>
                    <p class="text-xs text-text-muted mt-0.5 font-mono">
                        NISN: {{ $evaluation->student->nisn }} &bull; {{ $evaluation->student->schoolClass->nama ?? 'Kelas' }} &bull; {{ $evaluation->student->major->nama ?? 'Jurusan' }}
                    </p>
                    <p class="text-xs text-text-secondary mt-1">
                        Posisi PKL: <span class="font-medium text-text-primary">{{ $evaluation->application->job->judul ?? 'Magang' }}</span>
                    </p>
                </div>
            </div>

            <a href="{{ route('company.evaluations.index') }}" class="px-3 py-1.5 text-xs font-semibold rounded-btn border border-border-subtle text-text-secondary hover:text-text-primary hover:bg-surface-bg transition-colors self-start sm:self-auto flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Evaluation Form -->
    <form action="{{ route('company.evaluations.update', $evaluation) }}" method="POST" class="space-y-6" id="evalForm">
        @csrf
        @method('PUT')

        <div class="bg-surface-card border border-border-subtle rounded-btn p-6 shadow-xs space-y-6">
            <div>
                <h3 class="text-base font-bold font-heading text-text-primary">Perbarui Rubrik Penilaian Kinerja Magang</h3>
                <p class="text-xs text-text-secondary mt-1">Perubahan nilai akan disinkronkan ke portal pembimbing sekolah sebelum status diverifikasi.</p>
            </div>

            <!-- Rubric Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- 1. Kompetensi Teknis (40%) -->
                <div class="p-4 rounded-btn border border-border-subtle bg-surface-bg/40 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-text-primary flex items-center gap-1.5">
                            <i data-lucide="cpu" class="w-4 h-4 text-brand-primary"></i>
                            Kompetensi Teknis
                        </span>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-brand-primary/10 text-brand-primary">Bobot 40%</span>
                    </div>
                    <p class="text-[11px] text-text-secondary leading-relaxed">
                        Penguasaan alat kerja, pemecahan masalah teknis kejuruan, dan kerapian output pekerjaan.
                    </p>
                    <div>
                        <label for="nilai_teknis" class="block text-xs font-semibold text-text-primary mb-1">Nilai (0 - 100)</label>
                        <input 
                            type="number" 
                            step="0.1" 
                            min="0" 
                            max="100" 
                            id="nilai_teknis" 
                            name="nilai_teknis" 
                            value="{{ old('nilai_teknis', $evaluation->nilai_teknis) }}" 
                            required
                            class="w-full px-3 py-2 text-sm font-mono font-bold bg-surface-card border border-border-subtle rounded-btn focus:outline-hidden focus:border-brand-primary text-text-primary"
                        >
                        @error('nilai_teknis')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 2. Disiplin & Etika Kerja (30%) -->
                <div class="p-4 rounded-btn border border-border-subtle bg-surface-bg/40 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-text-primary flex items-center gap-1.5">
                            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                            Disiplin & Etika
                        </span>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Bobot 30%</span>
                    </div>
                    <p class="text-[11px] text-text-secondary leading-relaxed">
                        Ketepatan waktu kehadiran, kepatuhan SOP keselamatan kerja, dan integritas profesional.
                    </p>
                    <div>
                        <label for="nilai_disiplin" class="block text-xs font-semibold text-text-primary mb-1">Nilai (0 - 100)</label>
                        <input 
                            type="number" 
                            step="0.1" 
                            min="0" 
                            max="100" 
                            id="nilai_disiplin" 
                            name="nilai_disiplin" 
                            value="{{ old('nilai_disiplin', $evaluation->nilai_disiplin) }}" 
                            required
                            class="w-full px-3 py-2 text-sm font-mono font-bold bg-surface-card border border-border-subtle rounded-btn focus:outline-hidden focus:border-brand-primary text-text-primary"
                        >
                        @error('nilai_disiplin')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 3. Kerjasama Tim & Komunikasi (30%) -->
                <div class="p-4 rounded-btn border border-border-subtle bg-surface-bg/40 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-text-primary flex items-center gap-1.5">
                            <i data-lucide="message-square" class="w-4 h-4 text-indigo-600"></i>
                            Kerjasama & Komunikasi
                        </span>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">Bobot 30%</span>
                    </div>
                    <p class="text-[11px] text-text-secondary leading-relaxed">
                        Kemampuan kolaborasi tim, keterbukaan menerima masukan, dan inisiatif komunikasi aktif.
                    </p>
                    <div>
                        <label for="nilai_kerjasama" class="block text-xs font-semibold text-text-primary mb-1">Nilai (0 - 100)</label>
                        <input 
                            type="number" 
                            step="0.1" 
                            min="0" 
                            max="100" 
                            id="nilai_kerjasama" 
                            name="nilai_kerjasama" 
                            value="{{ old('nilai_kerjasama', $evaluation->nilai_kerjasama) }}" 
                            required
                            class="w-full px-3 py-2 text-sm font-mono font-bold bg-surface-card border border-border-subtle rounded-btn focus:outline-hidden focus:border-brand-primary text-text-primary"
                        >
                        @error('nilai_kerjasama')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Live Score Calculation Box -->
            <div class="p-4 rounded-btn bg-surface-bg border border-border-subtle flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center flex-shrink-0">
                        <i data-lucide="calculator" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-text-secondary">Simulasi Nilai Akhir & Predikat</div>
                        <div class="text-xs text-text-muted">Kalkulasi bobot (40% Teknis + 30% Disiplin + 30% Kerjasama)</div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <div class="text-xs text-text-muted font-mono">Skor Terhitung</div>
                        <div class="text-2xl font-bold font-heading text-text-primary font-mono" id="previewScore">{{ number_format($evaluation->nilai_akhir, 1) }}</div>
                    </div>
                    <div class="h-8 w-px bg-border-subtle"></div>
                    <div class="text-left">
                        <div class="text-xs text-text-muted">Predikat</div>
                        <span id="previewPredicateBadge" class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full border {{ $evaluation->predicate_badge_class }}">
                            {{ $evaluation->predikat }} ({{ $evaluation->predicate_label }})
                        </span>
                    </div>
                </div>
            </div>

            <!-- Catatan Evaluasi Industri -->
            <div>
                <label for="catatan_industri" class="block text-xs font-semibold text-text-primary mb-1.5">
                    Catatan & Ulasan Pembimbing Industri (Supervisor Remarks)
                </label>
                <textarea 
                    id="catatan_industri" 
                    name="catatan_industri" 
                    rows="4" 
                    placeholder="Berikan umpan balik mengenai kelebihan siswa, pencapaian proyek selama magang, dan area peningkatan kompetensi..."
                    class="w-full p-3 text-xs sm:text-sm bg-surface-bg border border-border-subtle rounded-btn focus:outline-hidden focus:border-brand-primary text-text-primary"
                >{{ old('catatan_industri', $evaluation->catatan_industri) }}</textarea>
                <p class="text-[11px] text-text-muted mt-1">Catatan ini akan tercetak pada lembar transkrip evaluasi PKL dan sertifikat magang.</p>
                @error('catatan_industri')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Action Buttons -->
            <div class="pt-4 border-t border-border-subtle flex flex-col sm:flex-row items-center justify-end gap-3">
                <button 
                    type="submit" 
                    name="submit_action" 
                    value="draft"
                    class="w-full sm:w-auto px-4 py-2 text-xs sm:text-sm font-semibold rounded-btn border border-border-subtle bg-surface-card hover:bg-surface-bg text-text-secondary hover:text-text-primary transition-colors flex items-center justify-center gap-1.5"
                >
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Perubahan Draf</span>
                </button>

                <button 
                    type="submit" 
                    name="submit_action" 
                    value="submit"
                    class="w-full sm:w-auto px-5 py-2 text-xs sm:text-sm font-semibold rounded-btn bg-brand-primary text-white hover:bg-brand-primary/90 transition-all shadow-xs active:scale-[0.96] flex items-center justify-center gap-1.5"
                >
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>{{ $evaluation->status === 'submitted' ? 'Simpan & Kirim Ulang' : 'Kirimkan Nilai ke Sekolah' }}</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function updateLiveScore() {
        const teknis = parseFloat(document.getElementById('nilai_teknis').value) || 0;
        const disiplin = parseFloat(document.getElementById('nilai_disiplin').value) || 0;
        const kerjasama = parseFloat(document.getElementById('nilai_kerjasama').value) || 0;

        const score = (teknis * 0.40) + (disiplin * 0.30) + (kerjasama * 0.30);
        const rounded = score.toFixed(1);

        document.getElementById('previewScore').innerText = rounded;

        const badge = document.getElementById('previewPredicateBadge');
        if (score >= 85) {
            badge.innerText = 'A (Amat Baik)';
            badge.className = 'inline-flex items-center px-3 py-1 text-xs font-bold rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300';
        } else if (score >= 75) {
            badge.innerText = 'B (Baik)';
            badge.className = 'inline-flex items-center px-3 py-1 text-xs font-bold rounded-full border bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-300';
        } else if (score >= 60) {
            badge.innerText = 'C (Cukup)';
            badge.className = 'inline-flex items-center px-3 py-1 text-xs font-bold rounded-full border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300';
        } else {
            badge.innerText = 'D (Kurang)';
            badge.className = 'inline-flex items-center px-3 py-1 text-xs font-bold rounded-full border bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300';
        }
    }

    document.getElementById('nilai_teknis').addEventListener('input', updateLiveScore);
    document.getElementById('nilai_disiplin').addEventListener('input', updateLiveScore);
    document.getElementById('nilai_kerjasama').addEventListener('input', updateLiveScore);

    // Initial run
    updateLiveScore();
</script>
@endsection
