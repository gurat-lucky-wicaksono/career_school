@extends('layouts.teacher')

@section('title', 'Validasi & Evaluasi Nilai PKL')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-heading font-bold text-text-primary tracking-tight">
                Validasi & Pengesahan Nilai PKL
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Tinjau lembar evaluasi kinerja magang dari supervisor mitra industri (DUDI) dan sahkan transkrip nilai siswa.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <x-ui.button variant="secondary" size="md" href="{{ route('teacher.mentoring.index') }}">
                Data Siswa Bimbingan
            </x-ui.button>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-surface-card border border-border-subtle p-5 rounded-btn shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-text-muted uppercase tracking-wider">Menunggu Validasi</p>
                    <h3 class="text-2xl font-bold font-heading text-indigo-600 dark:text-indigo-400 mt-1">{{ $stats['total_submitted'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-text-secondary mt-2">Nilai terkirim dari supervisor DUDI</p>
        </div>

        <div class="bg-surface-card border border-border-subtle p-5 rounded-btn shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-text-muted uppercase tracking-wider">Telah Disahkan</p>
                    <h3 class="text-2xl font-bold font-heading text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['total_verified'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="award" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-text-secondary mt-2">Transkrip sah dan sertifikat terbit</p>
        </div>

        <div class="bg-surface-card border border-border-subtle p-5 rounded-btn shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-text-muted uppercase tracking-wider">Rata-rata Nilai</p>
                    <h3 class="text-2xl font-bold font-heading text-text-primary mt-1">{{ $stats['average_score'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center">
                    <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-text-secondary mt-2">Skor komposit seluruh siswa PKL</p>
        </div>

        <div class="bg-surface-card border border-border-subtle p-5 rounded-btn shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-text-muted uppercase tracking-wider">Predikat Amat Baik (A)</p>
                    <h3 class="text-2xl font-bold font-heading text-text-primary mt-1">{{ $stats['grade_a_count'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-text-secondary mt-2">Nilai komposit di atas 85.0</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-surface-card border border-border-subtle rounded-card p-4 shadow-xs">
        <form method="GET" action="{{ route('teacher.evaluations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <div class="lg:col-span-5">
                <label for="eval-search" class="block text-xs font-semibold text-text-primary mb-1.5">
                    Cari Siswa / DUDI
                </label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-text-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input 
                        type="text" 
                        id="eval-search"
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Cari nama siswa, NISN, mitra DUDI..."
                        class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-full border border-border-default bg-surface-bg text-text-primary placeholder:text-text-muted focus:outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 shadow-xs transition-all"
                    >
                </div>
            </div>

            <div class="lg:col-span-3">
                <label for="eval-status" class="block text-xs font-semibold text-text-primary mb-1.5">
                    Status Evaluasi
                </label>
                <select 
                    id="eval-status"
                    name="status" 
                    class="choices-select w-full"
                >
                    <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Telah Disahkan</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draf Industri</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label for="eval-major" class="block text-xs font-semibold text-text-primary mb-1.5">
                    Konsentrasi Keahlian
                </label>
                <select 
                    id="eval-major"
                    name="major_id" 
                    class="choices-select w-full"
                >
                    <option value="">Semua Jurusan</option>
                    @foreach($majors as $m)
                        <option value="{{ $m->id }}" {{ request('major_id') == $m->id ? 'selected' : '' }}>{{ $m->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2 flex items-center gap-2">
                <x-ui.button variant="primary" type="submit" class="flex-1 justify-center">
                    Filter
                </x-ui.button>
                @if(request()->filled('search') || (request()->filled('status') && request('status') !== 'all') || request()->filled('major_id'))
                    <a href="{{ route('teacher.evaluations.index') }}" class="h-10 w-10 rounded-full border border-border-default hover:border-text-secondary/70 hover:bg-surface-bg text-text-secondary hover:text-text-primary flex items-center justify-center transition-all shadow-xs shrink-0" title="Reset filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Evaluations -->
    <div class="bg-surface-card border border-border-subtle rounded-btn shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-border-subtle flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold font-heading text-text-primary">Daftar Berkas Penilaian Kinerja Magang</h2>
                <p class="text-xs text-text-secondary mt-0.5">Sahkan berkas evaluasi untuk merilis sertifikat kelulusan PKL bagi siswa</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-border-subtle bg-surface-bg/50 text-text-muted font-semibold text-[11px] uppercase tracking-wider">
                        <th class="px-5 py-3">Siswa Praktikan</th>
                        <th class="px-5 py-3">Mitra Industri (DUDI)</th>
                        <th class="px-5 py-3 text-center">Nilai Komposit</th>
                        <th class="px-5 py-3 text-center">Predikat</th>
                        <th class="px-5 py-3">Status Verifikasi</th>
                        <th class="px-5 py-3 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @forelse ($evaluations as $eval)
                        <tr class="hover:bg-surface-bg/60 transition-colors">
                            <!-- Siswa -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-brand-primary/10 text-brand-primary font-bold text-xs flex items-center justify-center flex-shrink-0">
                                        {{ strtoupper(substr($eval->student->user->name ?? 'S', 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-heading font-bold text-text-primary truncate">
                                            {{ $eval->student->user->name ?? 'Siswa' }}
                                        </div>
                                        <div class="text-[11px] text-text-muted truncate font-sans">
                                            {{ $eval->student->major->nama ?? 'Jurusan' }} &bull; {{ $eval->student->schoolClass->nama ?? 'Kelas' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Mitra -->
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-text-primary">{{ $eval->company->nama ?? 'Mitra Industri' }}</div>
                                <div class="text-[11px] text-text-muted truncate">{{ $eval->application->job->judul ?? 'Magang' }}</div>
                            </td>

                            <!-- Nilai -->
                            <td class="px-5 py-3.5 text-center">
                                <span class="font-bold font-sans text-base text-text-primary">{{ number_format($eval->nilai_akhir, 1) }}</span>
                                <div class="text-[10px] text-text-muted">Skor / 100</div>
                            </td>

                            <!-- Predikat -->
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold rounded-full border {{ $eval->predicate_badge_class }}">
                                    {{ $eval->predikat }} ({{ $eval->predicate_label }})
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full border {{ $eval->status_badge_class }}">
                                    @if($eval->status === 'verified')
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                    @elseif($eval->status === 'submitted')
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                    @else
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                    @endif
                                    <span>{{ $eval->status_label }}</span>
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                @if($eval->status === 'submitted')
                                    <x-ui.button 
                                        variant="primary"
                                        size="sm"
                                        href="{{ route('teacher.evaluations.show', $eval) }}" 
                                    >
                                        Tinjau & Validasi
                                    </x-ui.button>
                                @else
                                    <x-ui.button 
                                        variant="secondary"
                                        size="sm"
                                        href="{{ route('teacher.evaluations.show', $eval) }}" 
                                    >
                                        Lihat Detail
                                    </x-ui.button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-text-muted">
                                <i data-lucide="file-check-2" class="w-10 h-10 mx-auto mb-2 text-text-muted/40"></i>
                                <p class="text-sm font-semibold">Belum ada berkas penilaian PKL</p>
                                <p class="text-xs text-text-secondary mt-1">Nilai akan tampil di sini saat supervisor mitra industri telah mengirimkan formulir evaluasi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($evaluations->hasPages())
            <div class="px-5 py-4 border-t border-border-subtle">
                {{ $evaluations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
