@extends('layouts.community')

@section('title', 'Kiriman Tersimpan - Koleksi Komunitas')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Koleksi Kiriman Tersimpan
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kumpulan postingan materi belajar, info proyek, dan diskusi komunitas yang Anda simpan.
            </p>
        </div>

        <a href="{{ route('feed.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-neutral-700 transition-colors shadow-xs">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            Kembali ke Feed
        </a>
    </div>

    <!-- Bookmarks Grid -->
    @if ($bookmarks->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($bookmarks as $item)
                @php
                    $post = $item->post;
                @endphp
                @if ($post)
                    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm flex flex-col justify-between space-y-4 hover:shadow-md transition-shadow">
                        <div>
                            <!-- Header Author & Category -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-brand-primary/20 text-brand-secondary dark:text-brand-primary font-bold text-xs flex items-center justify-center flex-shrink-0">
                                        {{ strtoupper(substr($post->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('community.profile', $post->user->username) }}" class="font-bold text-xs text-slate-900 dark:text-white hover:underline block truncate max-w-[150px]">
                                            {{ $post->user->name }}
                                        </a>
                                        <span class="text-[10px] text-slate-400">
                                            @ {{ $post->user->username }}
                                        </span>
                                    </div>
                                </div>

                                @if ($post->category)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                                        {{ $post->category->nama }}
                                    </span>
                                @endif
                            </div>

                            <!-- Post text -->
                            <p class="text-xs text-slate-700 dark:text-slate-300 line-clamp-3 leading-relaxed">
                                {{ $post->isi }}
                            </p>

                            @if ($post->media)
                                <div class="mt-3 rounded-xl overflow-hidden max-h-48 border border-slate-100 dark:border-white/5">
                                    <img src="{{ asset('storage/' . $post->media) }}" alt="Lampiran" class="w-full h-full object-cover">
                                </div>
                            @endif
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs">
                            <span class="text-[11px] text-slate-400">
                                Disimpan: {{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}
                            </span>

                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('posts.bookmark', $post) }}">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-xs font-semibold transition-colors" title="Hapus dari tersimpan">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="mt-8">
            {{ $bookmarks->links() }}
        </div>
    @else
        <div class="p-16 rounded-3xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-center shadow-sm space-y-3">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-neutral-800 text-slate-400 flex items-center justify-center mx-auto mb-2">
                <i data-lucide="bookmark-x" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Belum Ada Kiriman Tersimpan</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                Temukan materi belajar atau proyek menarik di linimasa feed lalu klik tombol simpan untuk menyimpannya di sini.
            </p>
            <div class="pt-2">
                <a href="{{ route('feed.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-primary text-neutral-900 font-bold text-xs hover:brightness-105 transition-all shadow-sm">
                    Jelajahi Feed Komunitas
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
