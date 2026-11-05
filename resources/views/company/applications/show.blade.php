@extends('layouts.company')

@section('title', 'Tinjau Berkas Pelamar & Portofolio')

@section('content')
<div class="space-y-6">
    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <x-ui.button 
            variant="secondary" 
            size="sm" 
            href="{{ route('company.applications.index') }}"
        >
            <i data-lucide="arrow-left" class="w-3.5 h-3.5 mr-1.5"></i>
            Kembali ke daftar pelamar
        </x-ui.button>

        <span class="text-xs text-text-muted">
            Diajukan: {{ $application->created_at ? $application->created_at->format('d M Y, H:i') : '-' }} WIB
        </span>
    </div>

    <!-- 1. Candidate Hero Header Card -->
    <div class="bg-surface-card border border-border-subtle rounded-card p-6 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4 min-w-0">
                <div class="w-16 h-16 rounded-full bg-brand-primary text-text-on-brand-primary font-bold text-xl flex items-center justify-center flex-shrink-0 shadow-xs">
                    {{ strtoupper(substr($application->student->user->name ?? 'S', 0, 2)) }}
                </div>

                <div class="space-y-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h1 class="text-xl font-heading font-bold text-text-primary">
                            {{ $application->student->user->name }}
                        </h1>

                        @php
                            $statusClasses = [
                                'tertunda' => 'bg-neutral-100 text-text-secondary dark:bg-neutral-800 border-border-subtle',
                                'ditinjau' => 'bg-state-warning-bg text-state-warning-text border-state-warning-border',
                                'wawancara' => 'bg-state-info-bg text-state-info-text border-state-info-border',
                                'diterima' => 'bg-state-success-bg text-state-success-text border-state-success-border',
                                'ditolak' => 'bg-state-danger-bg text-state-danger-text border-state-danger-border',
                            ];
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border capitalize {{ $statusClasses[$application->status] ?? 'bg-neutral-100' }}">
                            Status: {{ $application->status }}
                        </span>
                    </div>

                    <p class="text-xs text-text-secondary">
                        {{ $application->student->major->nama ?? 'Program Vokasi' }} &bull; NISN: <span class="font-mono">{{ $application->student->nisn }}</span>
                    </p>

                    <p class="text-[11px] text-text-muted flex flex-wrap items-center gap-3 pt-1">
                        <span class="inline-flex items-center gap-1">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-text-muted"></i>
                            <span>{{ $application->student->user->email }}</span>
                        </span>
                        @if ($application->student->user->profile?->telepon)
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1">
                                <i data-lucide="phone" class="w-3.5 h-3.5 text-text-muted"></i>
                                <span>{{ $application->student->user->profile->telepon }}</span>
                            </span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Job Match Score Highlight Box -->
            <div class="p-4 rounded-card bg-surface-bg border border-border-subtle flex items-center gap-4 flex-shrink-0">
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-text-muted block">
                        Skor job matching
                    </span>
                    <span class="text-xs text-text-secondary font-medium">
                        Kesesuaian kriteria
                    </span>
                </div>

                @php
                    $score = (float)$application->skor_kecocokan;
                    $scoreBadge = 'text-state-success-text';
                    if ($score < 50) $scoreBadge = 'text-text-muted';
                    elseif ($score < 75) $scoreBadge = 'text-state-warning-text';
                @endphp
                <div class="text-3xl font-extrabold font-heading {{ $scoreBadge }} flex items-center gap-1">
                    <span>{{ number_format($score, 0) }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Dual Grid: Analisis Kriteria & Portofolio Siswa -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Detail Keahlian & Portofolio (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Kompetensi Syarat vs Dimiliki -->
            <div class="bg-surface-card border border-border-subtle rounded-card p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-border-subtle">
                    <h3 class="text-xs font-heading font-bold text-text-primary uppercase tracking-wider">
                        Kesesuaian syarat keahlian
                    </h3>
                    <span class="text-[11px] text-text-muted">Posisi: {{ $application->job->judul }}</span>
                </div>

                <div class="space-y-2.5">
                    @php
                        $studentSkillIds = $application->student->skills->pluck('id')->toArray();
                    @endphp

                    @forelse ($application->job->skills as $jobSkill)
                        @php
                            $isMatched = in_array($jobSkill->id, $studentSkillIds);
                            $studentSkillRecord = $application->student->skills->firstWhere('id', $jobSkill->id);
                        @endphp
                        <div class="p-3 rounded-btn border {{ $isMatched ? 'bg-state-success-bg/40 border-state-success-border' : 'bg-surface-bg border-border-subtle' }} flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-btn {{ $isMatched ? 'bg-state-success-bg text-state-success-text' : 'bg-neutral-200 dark:bg-neutral-700 text-text-muted' }} flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="{{ $isMatched ? 'check' : 'x' }}" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-text-primary">
                                        {{ $jobSkill->nama }}
                                    </span>
                                    <span class="text-[10px] text-text-muted block">
                                        Bobot: {{ $jobSkill->pivot->bobot }}/5 &bull; {{ $jobSkill->pivot->wajib ? 'Syarat wajib' : 'Pendukung' }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-right flex-shrink-0">
                                @if ($isMatched)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-state-success-bg text-state-success-text border border-state-success-border">
                                        Tingkat: {{ $studentSkillRecord->pivot->tingkat ?? 1 }}/5
                                    </span>
                                    @if ($studentSkillRecord && $studentSkillRecord->pivot->tervalidasi)
                                        <span class="block text-[9px] text-state-success-text font-medium mt-0.5">
                                            Divalidasi guru
                                        </span>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-neutral-100 dark:bg-neutral-800 text-text-muted">
                                        Belum tercatat
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-text-muted italic py-2">Lowongan ini tidak menentukan syarat keahlian spesifik.</p>
                    @endforelse
                </div>
            </div>

            <!-- Portofolio Karya Siswa -->
            <div class="bg-surface-card border border-border-subtle rounded-card p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-border-subtle">
                    <h3 class="text-xs font-heading font-bold text-text-primary uppercase tracking-wider">
                        Portofolio & bukti karya siswa
                    </h3>
                    <span class="text-[11px] text-text-muted">{{ $application->student->portfolios->count() }} Karya</span>
                </div>

                <div class="space-y-3">
                    @forelse ($application->student->portfolios as $portfolio)
                        <div class="p-4 rounded-btn border border-border-subtle bg-surface-bg space-y-2">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-heading font-bold text-text-primary">
                                    {{ $portfolio->judul }}
                                </h4>
                                @if ($portfolio->kategori)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-neutral-100 dark:bg-neutral-800 text-text-secondary">
                                        {{ $portfolio->kategori }}
                                    </span>
                                @endif
                            </div>

                            @if ($portfolio->deskripsi)
                                <p class="text-xs text-text-secondary line-clamp-2 leading-relaxed">
                                    {{ $portfolio->deskripsi }}
                                </p>
                            @endif

                            @if ($portfolio->tautan)
                                <a 
                                    href="{{ $portfolio->tautan }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-brand-secondary dark:text-brand-primary hover:underline pt-1"
                                >
                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    <span>Tinjau proyek / tautan karya</span>
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-text-muted">
                            Siswa belum mengunggah portofolio proyek.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Formulir Tindakan Status & Catatan HRD (5 Cols) -->
        <div class="lg:col-span-5 bg-surface-card border border-border-subtle rounded-card p-6 shadow-xs space-y-5">
            <h3 class="text-xs font-heading font-bold text-text-primary uppercase tracking-wider pb-3 border-b border-border-subtle">
                Tindakan seleksi HRD
            </h3>

            <form method="POST" action="{{ route('company.applications.update-status', $application) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1.5">
                        Ubah status lamaran
                    </label>
                    <select 
                        name="status" 
                        required
                        class="w-full h-10 px-3 text-xs rounded-btn bg-surface-bg border border-border-subtle text-text-primary focus:outline-none focus:ring-1 focus:ring-brand-primary"
                    >
                        <option value="tertunda" {{ $application->status === 'tertunda' ? 'selected' : '' }}>Tertunda (Antrean baru)</option>
                        <option value="ditinjau" {{ $application->status === 'ditinjau' ? 'selected' : '' }}>Sedang ditinjau</option>
                        <option value="wawancara" {{ $application->status === 'wawancara' ? 'selected' : '' }}>Panggil wawancara / uji teknis</option>
                        <option value="diterima" {{ $application->status === 'diterima' ? 'selected' : '' }}>Diterima bekerja / magang</option>
                        <option value="ditolak" {{ $application->status === 'ditolak' ? 'selected' : '' }}>Belum memenuhi syarat (Ditolak)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1.5">
                        Catatan HRD / pesan untuk siswa (opsional)
                    </label>
                    <textarea 
                        name="catatan_hrd" 
                        rows="4"
                        placeholder="Contoh: Portofolio sangat rapi. Silakan hadir wawancara teknis via Zoom pada hari Kamis pukul 10.00 WIB..."
                        class="w-full p-3 text-xs rounded-btn bg-surface-bg border border-border-subtle text-text-primary placeholder:text-text-muted focus:outline-none focus:ring-1 focus:ring-brand-primary resize-none"
                    >{{ old('catatan_hrd', $application->catatan_hrd) }}</textarea>
                </div>

                <x-ui.button 
                    variant="primary" 
                    type="submit" 
                    class="w-full justify-center"
                >
                    Perbarui status seleksi
                </x-ui.button>
            </form>

            @if ($application->pesan)
                <div class="pt-4 border-t border-border-subtle space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-text-muted block">
                        Pesan pengantar dari siswa:
                    </span>
                    <p class="text-xs text-text-secondary italic bg-surface-bg p-3 rounded-btn border border-border-subtle">
                        "{{ $application->pesan }}"
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
