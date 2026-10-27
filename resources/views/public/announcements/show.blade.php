@extends('layouts.landing')

@section('title', $announcement->judul . ' - Pengumuman BKK SMKN 1 Cirebon')

@section('content')
<div class="py-12 sm:py-16 bg-surface-bg">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Back Navigation -->
        <div>
            <a href="{{ route('public.announcements') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Daftar Pengumuman
            </a>
        </div>

        <!-- Announcement Article Card -->
        <article class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-3xl p-6 sm:p-10 shadow-sm space-y-6">
            <!-- Header Meta -->
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    @if ($announcement->prioritas === 'penting')
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                            Pemberitahuan Penting
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            Informasi Resmi BKK
                        </span>
                    @endif

                    <span class="text-slate-300 dark:text-neutral-700">&bull;</span>
                    <span class="text-slate-500 dark:text-slate-400">
                        Dipublikasikan: {{ $announcement->mulai_tampil ? $announcement->mulai_tampil->format('d F Y') : $announcement->created_at->format('d F Y') }}
                    </span>
                    <span class="text-slate-300 dark:text-neutral-700">&bull;</span>
                    <span class="text-slate-500 dark:text-slate-400">
                        Oleh: {{ $announcement->author->name ?? 'Admin BKK SMKN 1 Cirebon' }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight font-sans leading-tight">
                    {{ $announcement->judul }}
                </h1>
            </div>

            <hr class="border-slate-100 dark:border-white/5">

            <!-- Body Content -->
            <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 text-sm sm:text-base leading-relaxed whitespace-pre-line">
                {{ $announcement->isi }}
            </div>

            <!-- Lampiran Berkas Download -->
            @if ($announcement->lampiran)
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-neutral-800/50 border border-slate-200 dark:border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                                Berkas Dokumen Lampiran Resmi
                            </h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ basename($announcement->lampiran) }}
                            </p>
                        </div>
                    </div>

                    <a 
                        href="{{ asset('storage/' . $announcement->lampiran) }}" 
                        target="_blank" 
                        download
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-brand-primary text-neutral-900 font-semibold text-xs sm:text-sm hover:brightness-105 transition-all shadow-sm flex-shrink-0"
                    >
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Unduh Berkas
                    </a>
                </div>
            @endif
        </article>

        <!-- Other Announcements -->
        @if ($recentAnnouncements->count() > 0)
            <div class="space-y-4 pt-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                    Pengumuman Lainnya
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($recentAnnouncements as $item)
                        <a href="{{ route('public.announcements.show', $item) }}" class="p-4 rounded-2xl bg-surface-card border border-slate-200/80 dark:border-white/10 hover:shadow-sm transition-all group block">
                            <span class="text-[10px] font-semibold text-slate-400 block mb-1">
                                {{ $item->created_at->format('d M Y') }}
                            </span>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-brand-secondary dark:group-hover:text-brand-primary line-clamp-2">
                                {{ $item->judul }}
                            </h4>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
