@extends('layouts.community')

@section('title', 'Kiriman Tersimpan - Koleksi Komunitas')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-heading font-bold text-text-primary tracking-tight">
                Koleksi Kiriman Tersimpan
            </h1>
            <p class="text-xs text-text-secondary mt-1">
                Kumpulan postingan materi belajar, info proyek, dan diskusi komunitas yang Anda simpan.
            </p>
        </div>

        <a href="{{ route('feed.index') }}" class="inline-flex items-center px-4 py-2 rounded-btn bg-neutral-100 dark:bg-neutral-800 text-text-primary text-xs font-semibold hover:bg-neutral-200 dark:hover:bg-neutral-700 active:scale-[0.96] transition-transform shadow-xs">
            Kembali ke feed
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
                    <div class="bg-surface-card border border-border-subtle rounded-card p-5 shadow-xs flex flex-col justify-between space-y-4 hover:shadow-sm transition-shadow">
                        <div>
                            <!-- Header Author & Category -->
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-brand-primary text-text-on-brand-primary font-bold text-xs flex items-center justify-center flex-shrink-0 shadow-xs">
                                        {{ strtoupper(substr($post->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('community.profile', $post->user->username) }}" class="font-heading font-bold text-xs text-text-primary hover:underline block truncate max-w-[150px]">
                                            {{ $post->user->name }}
                                        </a>
                                        <span class="text-[10px] text-text-muted">
                                            @ {{ $post->user->username }}
                                        </span>
                                    </div>
                                </div>

                                @if ($post->category)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-state-success-bg text-state-success-text border border-state-success-border">
                                        {{ $post->category->nama }}
                                    </span>
                                @endif
                            </div>

                            <!-- Post text -->
                            <p class="text-xs text-text-secondary line-clamp-3 leading-relaxed">
                                {{ $post->isi }}
                            </p>

                            @if ($post->media)
                                <div class="mt-3 rounded-card overflow-hidden max-h-48 border border-border-subtle">
                                    <img src="{{ asset('storage/' . $post->media) }}" alt="Lampiran" class="w-full h-full object-cover">
                                </div>
                            @endif
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-3 border-t border-border-subtle flex items-center justify-between text-xs">
                            <span class="text-[11px] text-text-muted">
                                Disimpan: {{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}
                            </span>

                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('posts.bookmark', $post) }}">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-btn text-state-danger-text hover:bg-state-danger-bg text-xs font-semibold active:scale-[0.96] transition-transform" title="Hapus dari tersimpan">
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
        <div class="p-16 rounded-card bg-surface-card border border-border-subtle text-center shadow-xs space-y-3">
            <div class="w-16 h-16 rounded-card bg-neutral-100 dark:bg-neutral-800 text-text-muted flex items-center justify-center mx-auto mb-2">
                <i data-lucide="bookmark-x" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base font-heading font-bold text-text-primary">Belum ada kiriman tersimpan</h3>
            <p class="text-xs text-text-secondary max-w-sm mx-auto">
                Temukan materi belajar atau proyek menarik di linimasa feed lalu klik tombol simpan untuk menyimpannya di sini.
            </p>
        </div>
    @endif
</div>
@endsection
