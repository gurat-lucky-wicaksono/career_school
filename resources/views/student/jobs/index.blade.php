@extends('layouts.student')

@section('title', 'Eksplorasi Lowongan Industri')

@section('content')
<div class="space-y-6">
    <!-- 1. Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Eksplorasi Lowongan Industri
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Temukan peluang PKL dan karier dari mitra industri terverifikasi dengan sistem rekomendasi cerdas KAK 15.
            </p>
        </div>
    </div>

    <!-- 2. Search & Filter Bar Card -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('student.jobs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-center">
            <!-- Keyword Search (5 Cols) -->
            <div class="lg:col-span-4 relative">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Cari posisi, kata kunci, atau mitra industri..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 placeholder-slate-400 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-all outline-none"
                >
            </div>

            <!-- Job Type (2 Cols) -->
            <div class="lg:col-span-2">
                <select 
                    name="jenis" 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-all outline-none"
                >
                    <option value="">Semua Jenis</option>
                    <option value="magang" {{ request('jenis') === 'magang' ? 'selected' : '' }}>Magang / PKL</option>
                    <option value="penuh_waktu" {{ request('jenis') === 'penuh_waktu' ? 'selected' : '' }}>Penuh Waktu</option>
                </select>
            </div>

            <!-- Location (2 Cols) -->
            <div class="lg:col-span-2">
                <input 
                    type="text" 
                    name="lokasi" 
                    value="{{ request('lokasi') }}" 
                    placeholder="Kota / Lokasi..." 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 placeholder-slate-400 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-all outline-none"
                >
            </div>

            <!-- Sort By (2 Cols) -->
            <div class="lg:col-span-2">
                <select 
                    name="sort" 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-all outline-none"
                >
                    <option value="match_desc" {{ request('sort', 'match_desc') === 'match_desc' ? 'selected' : '' }}>Kecocokan Tertinggi</option>
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="salary_desc" {{ request('sort') === 'salary_desc' ? 'selected' : '' }}>Tunjangan Tertinggi</option>
                </select>
            </div>

            <!-- Actions (2 Cols) -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <x-ui.button variant="primary" size="md" type="submit" class="w-full justify-center">
                    Cari
                </x-ui.button>
                @if (request()->hasAny(['q', 'jenis', 'lokasi', 'sort']))
                    <a href="{{ route('student.jobs.index') }}" class="p-2.5 rounded-xl border border-slate-200 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-neutral-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 3. Results Header -->
    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 px-1">
        <span>Menampilkan <strong class="text-slate-800 dark:text-slate-200 font-semibold">{{ $jobs->total() }}</strong> posisi lowongan industri</span>
        <span class="flex items-center gap-1 text-[11px] text-brand-primary font-medium">
            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
            Formula KAK 15 Terapan (Bobot Keahlian 60%)
        </span>
    </div>

    <!-- 4. Job Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @forelse ($jobs as $job)
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm hover:border-slate-300 dark:hover:border-white/20 transition-all flex flex-col justify-between group">
                <div class="space-y-4">
                    <!-- Card Top: Company info & Match Badge -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-neutral-800 border border-slate-200/60 dark:border-white/10 flex items-center justify-center flex-shrink-0 text-slate-700 dark:text-slate-200 font-bold text-base shadow-sm">
                                {{ strtoupper(substr($job->company->nama ?? 'P', 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-semibold text-brand-primary block truncate">
                                    {{ $job->company->nama ?? 'Mitra Industri' }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mt-0.5 truncate hover:text-brand-primary transition-colors">
                                    <a href="{{ route('student.jobs.show', $job) }}">
                                        {{ $job->judul }}
                                    </a>
                                </h3>
                            </div>
                        </div>

                        <!-- Match Pill Badge -->
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold flex-shrink-0 {{ $job->match_score >= 80 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60' : ($job->match_score >= 60 ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200/80 dark:border-blue-800/60' : 'bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-slate-300') }}">
                            <i data-lucide="sparkles" class="w-3 h-3"></i>
                            <span>{{ $job->match_percentage }}</span>
                        </span>
                    </div>

                    <!-- Meta info row -->
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                            {{ $job->lokasi }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                            {{ $job->jenis === 'magang' ? 'Magang / PKL' : 'Penuh Waktu' }}
                        </span>
                        @if ($job->gaji_min)
                            <span class="flex items-center gap-1 font-medium text-slate-700 dark:text-slate-200">
                                <i data-lucide="wallet" class="w-3.5 h-3.5 text-slate-400"></i>
                                Rp {{ number_format($job->gaji_min, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>

                    <!-- Description Preview -->
                    <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed">
                        {{ $job->deskripsi }}
                    </p>

                    <!-- Required Skills -->
                    <div class="space-y-1.5 pt-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kebutuhan Keahlian</span>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @foreach ($job->skills->take(4) as $skill)
                                @php
                                    $isMatched = in_array($skill->nama, $job->match_details['matched_skills'] ?? []);
                                @endphp
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-medium border {{ $isMatched ? 'bg-emerald-50 text-emerald-700 border-emerald-200/70 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40' : 'bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300 border-transparent' }}">
                                    @if ($isMatched)
                                        <i data-lucide="check" class="w-2.5 h-2.5 inline-block mr-0.5"></i>
                                    @endif
                                    {{ $skill->nama }}
                                </span>
                            @endforeach
                            @if ($job->skills->count() > 4)
                                <span class="text-[10px] text-slate-400">
                                    +{{ $job->skills->count() - 4 }} lainnya
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card Footer / Actions -->
                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between gap-3">
                    <div class="text-[11px] text-slate-400">
                        @if ($job->batas_lamaran)
                            <span>Batas: {{ $job->batas_lamaran->translatedFormat('d M Y') }}</span>
                        @else
                            <span>Kuota: {{ $job->kuota ?? 'Fleksibel' }} orang</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        @if ($job->is_applied)
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-white/10">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>
                                Sudah Dilamar
                            </span>
                        @else
                            <x-ui.button variant="primary" size="sm" href="{{ route('student.jobs.show', $job) }}">
                                Lihat & Lamar
                            </x-ui.button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-neutral-800 flex items-center justify-center mx-auto text-slate-400">
                    <i data-lucide="search-x" class="w-7 h-7"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white mt-4">
                    Tidak ditemukan lowongan yang cocok
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                    Coba sesuaikan kata kunci pencarian, filter jenis lowongan, atau bersihkan filter untuk menampilkan seluruh posisi.
                </p>
                <div class="mt-4">
                    <x-ui.button variant="secondary" size="sm" href="{{ route('student.jobs.index') }}">
                        Reset Pencarian
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
