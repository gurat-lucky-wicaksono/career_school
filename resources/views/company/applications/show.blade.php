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
            Kembali ke Daftar Pelamar
        </x-ui.button>

        <span class="text-xs text-slate-400">
            Diajukan: {{ $application->created_at ? $application->created_at->format('d M Y, H:i') : '-' }} WIB
        </span>
    </div>

    <!-- 1. Candidate Hero Header Card -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4 min-w-0">
                <div class="w-16 h-16 rounded-2xl bg-brand-primary/20 border border-brand-primary/40 text-brand-secondary dark:text-brand-primary font-bold text-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                    {{ strtoupper(substr($application->student->user->name ?? 'S', 0, 2)) }}
                </div>

                <div class="space-y-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white font-sans">
                            {{ $application->student->user->name }}
                        </h1>

                        @php
                            $statusClasses = [
                                'tertunda' => 'bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-slate-300',
                                'ditinjau' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/60',
                                'wawancara' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200/60',
                                'diterima' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60',
                                'ditolak' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/60',
                            ];
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold capitalize {{ $statusClasses[$application->status] ?? 'bg-slate-100' }}">
                            Status: {{ $application->status }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-600 dark:text-slate-300">
                        {{ $application->student->major->nama ?? 'Program Vokasi' }} &bull; NISN: <span class="font-mono">{{ $application->student->nisn }}</span>
                    </p>

                    <p class="text-[11px] text-slate-500 dark:text-slate-400 flex flex-wrap items-center gap-3 pt-1">
                        <span class="inline-flex items-center gap-1">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span>{{ $application->student->user->email }}</span>
                        </span>
                        @if ($application->student->user->profile?->telepon)
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1">
                                <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>{{ $application->student->user->profile->telepon }}</span>
                            </span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Job Match Score Highlight Box -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 flex items-center gap-4 flex-shrink-0">
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                        Skor Job Matching
                    </span>
                    <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">
                        Kesesuaian Kriteria
                    </span>
                </div>

                @php
                    $score = (float)$application->skor_kecocokan;
                    $scoreBadge = 'text-emerald-600 dark:text-emerald-400';
                    if ($score < 50) $scoreBadge = 'text-slate-500';
                    elseif ($score < 75) $scoreBadge = 'text-amber-500';
                @endphp
                <div class="text-3xl font-extrabold font-sans {{ $scoreBadge }} flex items-center gap-1">
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
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/5">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        Kesesuaian Syarat Keahlian
                    </h3>
                    <span class="text-[11px] text-slate-400">Posisi: {{ $application->job->judul }}</span>
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
                        <div class="p-3 rounded-xl border {{ $isMatched ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200/70 dark:border-emerald-800/40' : 'bg-slate-50/60 dark:bg-neutral-800/40 border-slate-200/60 dark:border-white/5' }} flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg {{ $isMatched ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300' : 'bg-slate-200 dark:bg-neutral-700 text-slate-400' }} flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="{{ $isMatched ? 'check' : 'x' }}" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-100">
                                        {{ $jobSkill->nama }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block">
                                        Bobot: {{ $jobSkill->pivot->bobot }}/5 &bull; {{ $jobSkill->pivot->wajib ? 'Syarat Wajib' : 'Pendukung' }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-right flex-shrink-0">
                                @if ($isMatched)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200">
                                        Tingkat: {{ $studentSkillRecord->pivot->tingkat ?? 1 }}/5
                                    </span>
                                    @if ($studentSkillRecord && $studentSkillRecord->pivot->tervalidasi)
                                        <span class="block text-[9px] text-emerald-600 dark:text-emerald-400 font-medium mt-0.5">
                                            Divalidasi Guru
                                        </span>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 dark:bg-neutral-800 text-slate-500">
                                        Belum Tercatat
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic py-2">Lowongan ini tidak menentukan syarat keahlian spesifik.</p>
                    @endforelse
                </div>
            </div>

            <!-- Portofolio Karya Siswa -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/5">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        Portofolio & Bukti Karya Siswa
                    </h3>
                    <span class="text-[11px] text-slate-400">{{ $application->student->portfolios->count() }} Karya</span>
                </div>

                <div class="space-y-3">
                    @forelse ($application->student->portfolios as $portfolio)
                        <div class="p-4 rounded-xl border border-slate-200/70 dark:border-white/5 bg-slate-50/50 dark:bg-neutral-800/30 space-y-2">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                    {{ $portfolio->judul }}
                                </h4>
                                @if ($portfolio->kategori)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-200 dark:bg-neutral-700 text-slate-600 dark:text-slate-300">
                                        {{ $portfolio->kategori }}
                                    </span>
                                @endif
                            </div>

                            @if ($portfolio->deskripsi)
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
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
                                    <span>Tinjau Proyek / Tautan Karya</span>
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            Siswa belum mengunggah portofolio proyek.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Formulir Tindakan Status & Catatan HRD (5 Cols) -->
        <div class="lg:col-span-5 bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-5">
            <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-100 dark:border-white/5">
                Tindakan Seleksi HRD
            </h3>

            <form method="POST" action="{{ route('company.applications.update-status', $application) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Ubah Status Lamaran
                    </label>
                    <select 
                        name="status" 
                        required
                        class="w-full h-10 px-3 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >
                        <option value="tertunda" {{ $application->status === 'tertunda' ? 'selected' : '' }}>Tertunda (Antrean Baru)</option>
                        <option value="ditinjau" {{ $application->status === 'ditinjau' ? 'selected' : '' }}>Sedang Ditinjau</option>
                        <option value="wawancara" {{ $application->status === 'wawancara' ? 'selected' : '' }}>Panggil Wawancara / Uji Teknis</option>
                        <option value="diterima" {{ $application->status === 'diterima' ? 'selected' : '' }}>Diterima Bekerja / Magang</option>
                        <option value="ditolak" {{ $application->status === 'ditolak' ? 'selected' : '' }}>Belum Memenuhi Syarat (Ditolak)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Catatan HRD / Pesan untuk Siswa (Opsional)
                    </label>
                    <textarea 
                        name="catatan_hrd" 
                        rows="4"
                        placeholder="Contoh: Portofolio sangat rapi. Silakan hadir wawancara teknis via Zoom pada hari Kamis pukul 10.00 WIB..."
                        class="w-full p-3 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >{{ old('catatan_hrd', $application->catatan_hrd) }}</textarea>
                </div>

                <x-ui.button 
                    variant="primary" 
                    type="submit" 
                    class="w-full"
                >
                    Perbarui Status Seleksi
                </x-ui.button>
            </form>

            @if ($application->pesan)
                <div class="pt-4 border-t border-slate-100 dark:border-white/5 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                        Pesan Pengantar dari Siswa:
                    </span>
                    <p class="text-xs text-slate-600 dark:text-slate-300 italic bg-slate-50 dark:bg-neutral-800/40 p-3 rounded-xl border border-slate-200/60 dark:border-white/5">
                        "{{ $application->pesan }}"
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
