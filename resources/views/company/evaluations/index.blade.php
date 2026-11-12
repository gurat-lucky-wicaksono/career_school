@extends('layouts.company')

@section('title', 'Evaluasi Kinerja PKL Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header Summary & KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-surface-card border border-border-subtle p-5 rounded-btn shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-text-muted uppercase tracking-wider">Total Siswa Magang</p>
                    <h3 class="text-2xl font-bold font-heading text-text-primary mt-1">{{ $stats['total_accepted'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-text-secondary mt-2">Siswa berstatus diterima PKL</p>
        </div>

        <div class="bg-surface-card border border-border-subtle p-5 rounded-btn shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-text-muted uppercase tracking-wider">Sudah Dievaluasi</p>
                    <h3 class="text-2xl font-bold font-heading text-text-primary mt-1">{{ $stats['total_evaluated'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <i data-lucide="file-check" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-text-secondary mt-2">Draf atau telah dikirim</p>
        </div>

        <div class="bg-surface-card border border-border-subtle p-5 rounded-btn shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-text-muted uppercase tracking-wider">Terverifikasi Sekolah</p>
                    <h3 class="text-2xl font-bold font-heading text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['total_verified'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="award" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-text-secondary mt-2">Telah disahkan guru pembimbing</p>
        </div>

        <div class="bg-surface-card border border-border-subtle p-5 rounded-btn shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-text-muted uppercase tracking-wider">Belum Dievaluasi</p>
                    <h3 class="text-2xl font-bold font-heading text-amber-600 dark:text-amber-400 mt-1">{{ $stats['total_pending'] }}</h3>
                </div>
                <div class="w-10 h-10 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-xs text-text-secondary mt-2">Menunggu input nilai pembimbing</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-surface-card border border-border-subtle rounded-btn p-4 shadow-xs">
        <form method="GET" action="{{ route('company.evaluations.index') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="relative w-full sm:w-80">
                <i data-lucide="search" class="w-4 h-4 text-text-muted absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari siswa, NISN, posisi..."
                    class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm bg-surface-bg border border-border-subtle rounded-btn focus:outline-hidden focus:border-brand-primary text-text-primary"
                >
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <div class="min-w-[200px] w-full sm:w-auto">
                    <select 
                        name="eval_status" 
                        onchange="this.form.submit()" 
                        class="choices-select w-full"
                    >
                        <option value="">Semua Status Evaluasi</option>
                        <option value="belum" {{ request('eval_status') === 'belum' ? 'selected' : '' }}>Belum Dievaluasi</option>
                        <option value="draft" {{ request('eval_status') === 'draft' ? 'selected' : '' }}>Draf</option>
                        <option value="submitted" {{ request('eval_status') === 'submitted' ? 'selected' : '' }}>Menunggu Verifikasi Guru</option>
                        <option value="verified" {{ request('eval_status') === 'verified' ? 'selected' : '' }}>Terverifikasi Sekolah</option>
                    </select>
                </div>

                @if(request()->filled('search') || request()->filled('eval_status'))
                    <x-ui.button variant="secondary" size="sm" href="{{ route('company.evaluations.index') }}">
                        Reset
                    </x-ui.button>
                @endif
            </div>
        </form>
    </div>

    <!-- Table of Interns -->
    <div class="bg-surface-card border border-border-subtle rounded-btn shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-border-subtle flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold font-heading text-text-primary">Daftar Siswa Magang / PKL</h2>
                <p class="text-xs text-text-secondary mt-0.5">Kelola penilaian aspek teknis, etika kerja, dan soft skills siswa</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-border-subtle bg-surface-bg/50 text-text-muted font-semibold text-[11px] uppercase tracking-wider">
                        <th class="px-5 py-3">Siswa Praktikan</th>
                        <th class="px-5 py-3">Posisi PKL</th>
                        <th class="px-5 py-3 text-center">Nilai Akhir</th>
                        <th class="px-5 py-3 text-center">Predikat</th>
                        <th class="px-5 py-3">Status Evaluasi</th>
                        <th class="px-5 py-3 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @forelse ($interns as $app)
                        @php
                            $eval = $app->evaluation;
                        @endphp
                        <tr class="hover:bg-surface-bg/60 transition-colors">
                            <!-- Siswa -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-neutral-100 dark:bg-neutral-800 border border-border-subtle text-text-primary font-bold text-xs flex items-center justify-center flex-shrink-0">
                                        {{ strtoupper(substr($app->student->user->name ?? 'S', 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-heading font-bold text-text-primary truncate">
                                            {{ $app->student->user->name ?? 'Siswa' }}
                                        </div>
                                        <div class="text-[11px] text-text-muted truncate font-sans">
                                            {{ $app->student->major->nama ?? 'Jurusan' }} &bull; NISN: {{ $app->student->nisn }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Posisi -->
                            <td class="px-5 py-3.5">
                                <span class="font-medium text-text-primary">{{ $app->job->judul ?? 'Magang Industri' }}</span>
                                <div class="text-[11px] text-text-muted">{{ $app->job->lokasi ?? 'Kantor Perusahaan' }}</div>
                            </td>

                            <!-- Nilai Akhir -->
                            <td class="px-5 py-3.5 text-center">
                                @if($eval)
                                    <span class="font-bold font-sans text-base text-text-primary">{{ number_format($eval->nilai_akhir, 1) }}</span>
                                    <div class="text-[10px] text-text-muted">Skor / 100</div>
                                @else
                                    <span class="text-xs text-text-muted italic">-</span>
                                @endif
                            </td>

                            <!-- Predikat -->
                            <td class="px-5 py-3.5 text-center">
                                @if($eval)
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold rounded-full border {{ $eval->predicate_badge_class }}">
                                        {{ $eval->predikat }} ({{ $eval->predicate_label }})
                                    </span>
                                @else
                                    <span class="text-xs text-text-muted italic">Belum dinilai</span>
                                @endif
                            </td>

                            <!-- Status Evaluasi -->
                            <td class="px-5 py-3.5">
                                @if($eval)
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
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full border bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-900 dark:text-slate-400 dark:border-slate-800">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        <span>Belum Dievaluasi</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                @if(!$eval)
                                    <x-ui.button variant="primary" size="sm" href="{{ route('company.evaluations.create', $app) }}">
                                        Beri Nilai
                                    </x-ui.button>
                                @elseif($eval->status === 'draft')
                                    <x-ui.button variant="secondary" size="sm" href="{{ route('company.evaluations.edit', $eval) }}">
                                        Lanjutkan Draf
                                    </x-ui.button>
                                @elseif($eval->status === 'submitted')
                                    <div class="inline-flex items-center gap-1.5">
                                        <x-ui.button variant="secondary" size="sm" href="{{ route('company.evaluations.edit', $eval) }}">
                                            Edit
                                        </x-ui.button>
                                        <x-ui.button variant="secondary" size="sm" href="{{ route('company.evaluations.show', $eval) }}">
                                            Detail
                                        </x-ui.button>
                                    </div>
                                @else
                                    <x-ui.button variant="secondary" size="sm" href="{{ route('company.evaluations.show', $eval) }}">
                                        Transkrip Sah
                                    </x-ui.button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-text-muted">
                                <i data-lucide="users" class="w-10 h-10 mx-auto mb-2 text-text-muted/40"></i>
                                <p class="text-sm font-semibold">Belum ada siswa magang yang ditemukan</p>
                                <p class="text-xs text-text-secondary mt-1">Siswa akan muncul di sini setelah status lamarannya disetujui sebagai Diterima</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($interns->hasPages())
            <div class="px-5 py-4 border-t border-border-subtle">
                {{ $interns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
