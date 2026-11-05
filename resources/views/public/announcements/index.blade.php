@extends('layouts.landing')

@section('title', 'Pusat Informasi & Pengumuman BKK SMKN 1 Cirebon')

@section('content')
<div class="py-12 sm:py-16 bg-surface-bg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <!-- Hero Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-primary/10 text-brand-secondary dark:text-brand-primary border border-brand-primary/20">
                <i data-lucide="megaphone" class="w-3.5 h-3.5"></i>
                <span>Pusat informasi resmi BKK</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-text-primary tracking-tight font-heading">
                Pengumuman Bursa Kerja Khusus
            </h1>
            <p class="text-sm sm:text-base text-text-secondary">
                Akses informasi resmi terkait rekrutmen mitra DUDI, pembekalan Praktik Kerja Lapangan (PKL), jadwal seleksi bersama, dan panduan karier vokasi SMKN 1 Cirebon.
            </p>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-4 sm:p-5 shadow-xs max-w-4xl mx-auto">
            <form method="GET" action="{{ route('public.announcements') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2 relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari kata kunci pengumuman..."
                        class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-input border border-border-default/60 bg-surface-bg text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-colors"
                    >
                    <i data-lucide="search" class="w-4 h-4 text-text-secondary absolute left-3.5 top-3"></i>
                </div>

                <div class="flex items-center gap-2">
                    <select
                        name="prioritas"
                        class="w-full px-3 py-2.5 text-xs sm:text-sm rounded-input border border-border-default/60 bg-surface-bg text-text-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-colors"
                        onchange="this.form.submit()"
                    >
                        <option value="">Semua prioritas</option>
                        <option value="penting" {{ request('prioritas') === 'penting' ? 'selected' : '' }}>Penting (wajib)</option>
                        <option value="normal" {{ request('prioritas') === 'normal' ? 'selected' : '' }}>Informasi reguler</option>
                        <option value="rendah" {{ request('prioritas') === 'rendah' ? 'selected' : '' }}>Informasi tambahan</option>
                    </select>

                    <button type="submit" class="px-5 py-2.5 rounded-btn bg-brand-primary text-text-on-brand-primary font-semibold text-xs sm:text-sm hover:brightness-105 transition-all active:scale-[0.96] shadow-xs">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Announcements Grid -->
        @if ($announcements->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($announcements as $item)
                    <article class="bg-surface-card border border-border-subtle rounded-card p-6 shadow-xs hover:border-brand-primary/50 transition-all flex flex-col justify-between group">
                        <div>
                            <!-- Header tags -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                @if ($item->prioritas === 'penting')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-state-danger-bg text-state-danger-text border border-rose-500/30">
                                        Penting
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-surface-bg text-text-secondary border border-border-subtle">
                                        Informasi BKK
                                    </span>
                                @endif

                                <span class="text-[11px] text-text-secondary">
                                    {{ $item->mulai_tampil ? $item->mulai_tampil->format('d M Y') : $item->created_at->format('d M Y') }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h2 class="text-base font-bold text-text-primary font-heading group-hover:text-brand-secondary dark:group-hover:text-brand-primary transition-colors line-clamp-2 mb-2">
                                <a href="{{ route('public.announcements.show', $item) }}">
                                    {{ $item->judul }}
                                </a>
                            </h2>

                            <!-- Content snippet -->
                            <p class="text-xs text-text-secondary line-clamp-3 leading-relaxed">
                                {{ Str::limit(strip_tags($item->isi), 120) }}
                            </p>
                        </div>

                        <!-- Footer -->
                        <div class="pt-4 mt-4 border-t border-border-subtle flex items-center justify-between text-xs">
                            <span class="text-[11px] text-text-secondary">
                                Oleh: {{ $item->author->name ?? 'Admin BKK' }}
                            </span>

                            <a href="{{ route('public.announcements.show', $item) }}" class="inline-flex items-center gap-1 font-semibold text-brand-secondary dark:text-brand-primary hover:underline">
                                Baca selengkapnya
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $announcements->links() }}
            </div>
        @else
            <div class="py-16 text-center bg-surface-card border border-border-subtle rounded-card max-w-xl mx-auto p-8 shadow-xs">
                <div class="w-16 h-16 rounded-card bg-surface-bg text-text-secondary flex items-center justify-center mx-auto mb-4 border border-border-subtle">
                    <i data-lucide="inbox" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-bold text-text-primary font-heading">Tidak ada pengumuman ditemukan</h3>
                <p class="text-xs text-text-secondary mt-1 max-w-sm mx-auto mb-4">
                    Saat ini belum ada pengumuman baru yang sesuai dengan kriteria pencarian Anda.
                </p>
                <a href="{{ route('public.announcements') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-btn bg-surface-bg border border-border-default/60 text-xs font-semibold text-text-primary hover:border-text-primary transition-all active:scale-[0.96]">
                    Reset pencarian
                </a>
            </div>
        @endif
    </div>
</div>
@endsection