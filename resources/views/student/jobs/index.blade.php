@extends('layouts.student')

@section('title', 'Eksplorasi Lowongan Industri')

@section('content')
<div class="space-y-6">
    <!-- 1. Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-text-primary font-heading tracking-tight">
                Eksplorasi Lowongan Industri
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Temukan peluang PKL dan karier dari mitra industri terverifikasi dengan sistem rekomendasi cerdas KAK 15.
            </p>
        </div>
    </div>

    <!-- 2. Search & Filter Bar Card -->
    <div class="bg-surface-card border border-border-subtle rounded-card p-5 shadow-xs">
        <form method="GET" action="{{ route('student.jobs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-center">
            <!-- Keyword Search (4 Cols) -->
            <div class="lg:col-span-4 relative">
                <i data-lucide="search" class="w-4 h-4 text-text-secondary absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Cari posisi, kata kunci, atau mitra industri..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-input border border-border-default/60 bg-surface-bg text-text-primary placeholder:text-text-secondary/60 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-colors outline-none"
                >
            </div>

            <!-- Job Type (2 Cols) -->
            <div class="lg:col-span-2">
                <select 
                    name="jenis" 
                    class="w-full px-3 py-2.5 rounded-input border border-border-default/60 bg-surface-bg text-text-primary text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-colors outline-none"
                >
                    <option value="">Semua jenis</option>
                    <option value="magang" {{ request('jenis') === 'magang' ? 'selected' : '' }}>Magang / PKL</option>
                    <option value="penuh_waktu" {{ request('jenis') === 'penuh_waktu' ? 'selected' : '' }}>Penuh waktu</option>
                </select>
            </div>

            <!-- Location (2 Cols) -->
            <div class="lg:col-span-2">
                <input 
                    type="text" 
                    name="lokasi" 
                    value="{{ request('lokasi') }}" 
                    placeholder="Kota / lokasi..." 
                    class="w-full px-3 py-2.5 rounded-input border border-border-default/60 bg-surface-bg text-text-primary placeholder:text-text-secondary/60 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-colors outline-none"
                >
            </div>

            <!-- Sort By (2 Cols) -->
            <div class="lg:col-span-2">
                <select 
                    name="sort" 
                    class="w-full px-3 py-2.5 rounded-input border border-border-default/60 bg-surface-bg text-text-primary text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-colors outline-none"
                >
                    <option value="match_desc" {{ request('sort', 'match_desc') === 'match_desc' ? 'selected' : '' }}>Kecocokan tertinggi</option>
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="salary_desc" {{ request('sort') === 'salary_desc' ? 'selected' : '' }}>Tunjangan tertinggi</option>
                </select>
            </div>

            <!-- Actions (2 Cols) -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <x-ui.button variant="primary" size="md" type="submit" class="w-full justify-center">
                    Cari
                </x-ui.button>
                @if (request()->hasAny(['q', 'jenis', 'lokasi', 'sort']))
                    <a href="{{ route('student.jobs.index') }}" class="p-2.5 rounded-btn border border-border-default/60 hover:bg-surface-bg text-text-secondary hover:text-text-primary transition-all active:scale-[0.96]" title="Reset filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 3. Results Header -->
    <div class="flex items-center justify-between text-xs text-text-secondary px-1">
        <span>Menampilkan <strong class="text-text-primary font-semibold">{{ $jobs->total() }}</strong> posisi lowongan industri</span>
        <span class="flex items-center gap-1 text-[11px] text-brand-secondary dark:text-brand-primary font-medium">
            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
            Formula KAK 15 terapan (bobot keahlian 60%)
        </span>
    </div>

    <!-- 4. Job Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @forelse ($jobs as $job)
            <div class="bg-surface-card border border-border-subtle rounded-card p-5 shadow-xs hover:border-brand-primary/50 transition-all flex flex-col justify-between group">
                <div class="space-y-4">
                    <!-- Card Top: Company info & Match Badge -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <div class="w-12 h-12 rounded-btn bg-brand-primary text-text-on-brand-primary flex items-center justify-center flex-shrink-0 font-bold font-heading text-base shadow-xs">
                                {{ strtoupper(substr($job->company->nama ?? 'P', 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-semibold text-brand-secondary dark:text-brand-primary block truncate">
                                    {{ $job->company->nama ?? 'Mitra industri' }}
                                </span>
                                <h3 class="text-base font-bold text-text-primary font-heading mt-0.5 truncate hover:text-brand-secondary dark:hover:text-brand-primary transition-colors">
                                    <a href="{{ route('student.jobs.show', $job) }}">
                                        {{ $job->judul }}
                                    </a>
                                </h3>
                            </div>
                        </div>

                        <!-- Match Pill Badge -->
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold flex-shrink-0 {{ $job->match_score >= 80 ? 'bg-state-success-bg text-state-success-text border border-emerald-500/30' : ($job->match_score >= 60 ? 'bg-state-info-bg text-state-info-text border border-sky-500/30' : 'bg-surface-bg text-text-secondary border border-border-subtle') }}">
                            <i data-lucide="sparkles" class="w-3 h-3"></i>
                            <span>{{ $job->match_percentage }}</span>
                        </span>
                    </div>

                    <!-- Meta info row -->
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-text-secondary">
                        <span class="flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-text-secondary"></i>
                            {{ $job->lokasi }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-text-secondary"></i>
                            {{ $job->jenis === 'magang' ? 'Magang / PKL' : 'Penuh waktu' }}
                        </span>
                        @if ($job->gaji_min)
                            <span class="flex items-center gap-1 font-medium text-text-primary">
                                <i data-lucide="wallet" class="w-3.5 h-3.5 text-text-secondary"></i>
                                Rp {{ number_format($job->gaji_min, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    <!-- Description Preview -->
                    <p class="text-xs text-text-secondary line-clamp-2 leading-relaxed">
                        {{ $job->deskripsi }}
                    </p>

                    <!-- Required Skills -->
                    <div class="space-y-1.5 pt-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-text-secondary/70">Kebutuhan keahlian</span>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @foreach ($job->skills->take(4) as $skill)
                                @php
                                    $isMatched = in_array($skill->nama, $job->match_details['matched_skills'] ?? []);
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $isMatched ? 'bg-state-success-bg text-state-success-text border-emerald-500/30' : 'bg-surface-bg text-text-secondary border-border-subtle' }}">
                                    @if ($isMatched)
                                        <i data-lucide="check" class="w-2.5 h-2.5 inline-block mr-0.5"></i>
                                    @endif
                                    {{ $skill->nama }}
                                </span>
                            @endforeach
                            @if ($job->skills->count() > 4)
                                <span class="text-[10px] text-text-secondary">
                                    +{{ $job->skills->count() - 4 }} lainnya
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card Footer / Actions -->
                <div class="mt-5 pt-4 border-t border-border-subtle flex items-center justify-between gap-3">
                    <div class="text-[11px] text-text-secondary">
                        @if ($job->batas_lamaran)
                            <span>Batas: {{ $job->batas_lamaran->translatedFormat('d M Y') }}</span>
                        @else
                            <span>Kuota: {{ $job->kuota ?? 'Fleksibel' }} orang</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        @if ($job->is_applied)
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-btn text-xs font-semibold bg-surface-bg text-text-secondary border border-border-subtle">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>
                                Sudah dilamar
                            </span>
                        @else
                            <x-ui.button variant="primary" size="sm" href="{{ route('student.jobs.show', $job) }}">
                                Lihat & lamar
                            </x-ui.button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-surface-card border border-border-subtle rounded-card p-12 text-center shadow-xs">
                <div class="w-14 h-14 rounded-card bg-surface-bg flex items-center justify-center mx-auto text-text-secondary border border-border-subtle">
                    <i data-lucide="search-x" class="w-7 h-7"></i>
                </div>
                <h3 class="text-base font-bold text-text-primary font-heading mt-4">
                    Tidak ditemukan lowongan yang cocok
                </h3>
                <p class="text-xs text-text-secondary mt-1 max-w-md mx-auto">
                    Coba sesuaikan kata kunci pencarian, filter jenis lowongan, atau bersihkan filter untuk menampilkan seluruh posisi.
                </p>
                <div class="mt-4">
                    <x-ui.button variant="secondary" size="sm" href="{{ route('student.jobs.index') }}">
                        Reset pencarian
                    </x-ui.button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- 5. Pagination Links -->
    @if ($jobs->hasPages())
        <div class="pt-4 flex justify-center">
            {{ $jobs->links() }}
        </div>
    @endif
</div>
@endsection
