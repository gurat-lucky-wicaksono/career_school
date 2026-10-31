@extends('layouts.student')

@section('title', 'Lamaran Saya & Status Seleksi')

@section('content')
<div class="space-y-6">
    <!-- 1. Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Lamaran Saya
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Pantau progres peninjauan berkas portofolio, jadwal wawancara, dan keputusan seleksi dari mitra industri.
            </p>
        </div>

        <x-ui.button variant="primary" size="md" href="{{ route('student.jobs.index') }}">
            <i data-lucide="compass" class="w-4 h-4 mr-2"></i>
            Jelajahi Lowongan Lainnya
        </x-ui.button>
    </div>

    <!-- 2. Status Filter Tabs & Search Bar -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-4 shadow-sm space-y-4">
        <!-- Status Tabs Bar -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 custom-scrollbar text-xs">
            @php
                $activeTab = request('status', 'all');
                $tabs = [
                    'all' => ['label' => 'Semua', 'count' => $statusCounts['all']],
                    'tertunda' => ['label' => 'Menunggu Review', 'count' => $statusCounts['tertunda']],
                    'ditinjau' => ['label' => 'Sedang Ditinjau', 'count' => $statusCounts['ditinjau']],
                    'wawancara' => ['label' => 'Wawancara', 'count' => $statusCounts['wawancara'], 'highlight' => true],
                    'diterima' => ['label' => 'Diterima', 'count' => $statusCounts['diterima']],
                    'ditolak' => ['label' => 'Belum Lolos', 'count' => $statusCounts['ditolak']],
                ];
            @endphp

            @foreach ($tabs as $key => $tab)
                @php
                    $isSelected = ($key === 'all' && !request()->has('status')) || request('status') === $key;
                @endphp
                <a 
                    href="{{ $key === 'all' ? route('student.applications.index') : route('student.applications.index', ['status' => $key]) }}"
                    class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $isSelected ? 'bg-brand-primary text-white font-semibold shadow-sm shadow-brand-primary/25' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-800' }}"
                >
                    <span>{{ $tab['label'] }}</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $isSelected ? 'bg-white/20 text-white' : (!empty($tab['highlight']) && $tab['count'] > 0 ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300' : 'bg-slate-200/70 dark:bg-neutral-700 text-slate-700 dark:text-slate-300') }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('student.applications.index') }}" class="flex items-center gap-3 pt-2 border-t border-slate-100 dark:border-white/5">
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Cari berdasarkan nama posisi atau nama mitra industri..." 
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 placeholder-slate-400 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-all outline-none"
                >
            </div>

            <x-ui.button variant="secondary" size="md" type="submit">
                Cari
            </x-ui.button>
            @if (request()->has('q'))
                <a href="{{ route('student.applications.index', request('status') ? ['status' => request('status')] : []) }}" class="p-2 rounded-xl border border-slate-200 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-neutral-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors" title="Reset Pencarian">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- 3. Applications List -->
    <div class="space-y-4">
        @forelse ($applications as $application)
            @php
                $isInterview = $application->status === 'wawancara';
                $isAccepted = $application->status === 'diterima';
                $isRejected = $application->status === 'ditolak';
                $isReviewed = $application->status === 'ditinjau';

                $statusBadgeConfig = [
                    'tertunda' => ['label' => 'Menunggu Review', 'class' => 'bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-slate-300 border-slate-200/80 dark:border-neutral-700'],
                    'ditinjau' => ['label' => 'Sedang Ditinjau', 'class' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border-sky-200/80 dark:border-sky-800/60'],
                    'wawancara' => ['label' => 'Panggilan Wawancara', 'class' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border-indigo-200/80 dark:border-indigo-800/60 font-bold'],
                    'diterima' => ['label' => 'Lolos Seleksi / Diterima', 'class' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200/80 dark:border-emerald-800/60 font-bold'],
                    'ditolak' => ['label' => 'Belum Lolos', 'class' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border-rose-200/80 dark:border-rose-800/60'],
                ];
                $badge = $statusBadgeConfig[$application->status] ?? $statusBadgeConfig['tertunda'];
            @endphp

            <div class="bg-surface-card border {{ $isInterview ? 'border-indigo-300 dark:border-indigo-800/80 shadow-md shadow-indigo-500/5' : 'border-slate-200/80 dark:border-white/10 shadow-sm' }} rounded-2xl p-5 hover:border-slate-300 dark:hover:border-white/20 transition-all flex flex-col justify-between group">
                <div class="space-y-4">
                    <!-- Top Info Row -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3.5 min-w-0">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-neutral-800 border border-slate-200/60 dark:border-white/10 text-slate-700 dark:text-slate-200 font-bold text-base flex items-center justify-center flex-shrink-0 shadow-sm">
                                {{ strtoupper(substr($application->job->company->nama ?? 'P', 0, 2)) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-semibold text-brand-primary">
                                        {{ $application->job->company->nama ?? 'Mitra Industri' }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $application->job->jenis === 'magang' ? 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' }}">
                                        {{ $application->job->jenis === 'magang' ? 'Magang / PKL' : 'Penuh Waktu' }}
                                    </span>
                                </div>

                                <h3 class="text-base font-bold text-slate-900 dark:text-white mt-0.5 truncate hover:text-brand-primary transition-colors">
                                    <a href="{{ route('student.applications.show', $application) }}">
                                        {{ $application->job->judul }}
                                    </a>
                                </h3>

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                        {{ $application->job->lokasi }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                        Diajukan: {{ $application->created_at->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge & Match Score -->
                        <div class="flex sm:flex-col items-start sm:items-end justify-between sm:justify-center gap-1.5 pt-2 sm:pt-0 border-t sm:border-0 border-slate-100 dark:border-white/5">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $badge['class'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $isInterview ? 'bg-indigo-500 animate-pulse' : ($isAccepted ? 'bg-emerald-500' : ($isRejected ? 'bg-rose-500' : 'bg-slate-400')) }}"></span>
                                <span>{{ $badge['label'] }}</span>
                            </span>

                            @if ($application->skor_kecocokan)
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                    <i data-lucide="sparkles" class="w-3 h-3 text-brand-primary"></i>
                                    Skor Kecocokan: <strong class="text-slate-700 dark:text-slate-200">{{ round($application->skor_kecocokan) }}%</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- HRD Note Alert Box (if available) -->
                    @if ($application->catatan_hrd)
                        <div class="p-3.5 rounded-xl border {{ $isInterview ? 'bg-indigo-50/70 dark:bg-indigo-950/30 border-indigo-200/80 dark:border-indigo-800/40' : 'bg-slate-50 dark:bg-neutral-800/60 border-slate-200/60 dark:border-white/5' }} space-y-1">
                            <div class="flex items-center gap-1.5 text-xs font-bold {{ $isInterview ? 'text-indigo-900 dark:text-indigo-200' : 'text-slate-900 dark:text-slate-200' }}">
                                <i data-lucide="{{ $isInterview ? 'calendar-check-2' : 'message-square' }}" class="w-4 h-4"></i>
                                <span>Catatan / Instruksi HRD:</span>
                            </div>
                            <p class="text-xs {{ $isInterview ? 'text-indigo-950/90 dark:text-indigo-300/90 font-medium' : 'text-slate-600 dark:text-slate-300' }} leading-relaxed whitespace-pre-line">
                                {{ $application->catatan_hrd }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Footer & Action Buttons -->
                <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-white/5 flex items-center justify-between gap-3">
                    <span class="text-[11px] text-slate-400">
                        ID Lamaran: <span class="font-mono">#APP-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </span>

                    <div class="flex items-center gap-2">
                        @if ($application->status === 'tertunda')
                            <form method="POST" action="{{ route('student.applications.destroy', $application) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan lamaran ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-xl border border-rose-200 dark:border-rose-900/50 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-xs font-semibold transition-colors">
                                    Batalkan
                                </button>
                            </form>
                        @endif

                        <a 
                            href="{{ route('student.applications.print', $application) }}" 
                            target="_blank"
                            title="Cetak Bukti Pendaftaran (PDF)"
                            class="p-2 rounded-xl border border-slate-200/80 dark:border-white/10 text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-neutral-800 transition-colors"
                        >
                            <i data-lucide="printer" class="w-4 h-4"></i>
                        </a>

                        <x-ui.button variant="primary" size="sm" href="{{ route('student.applications.show', $application) }}">
                            <span>Pelacakan Detail</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1.5"></i>
                        </x-ui.button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-neutral-800 flex items-center justify-center mx-auto text-slate-400">
                    <i data-lucide="inbox" class="w-7 h-7"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white mt-4">
                    Belum Ada Lamaran Pada Kategori Ini
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                    Anda belum mengajukan lamaran atau tidak ada lamaran yang cocok dengan kriteria filter saat ini.
                </p>
                <div class="mt-4">
                    <x-ui.button variant="primary" size="sm" href="{{ route('student.jobs.index') }}">
                        Eksplorasi Lowongan Sekarang
                    </x-ui.button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- 4. Pagination -->
    @if ($applications->hasPages())
        <div class="pt-4 flex justify-center">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection
