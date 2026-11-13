@extends('layouts.community')

@section('title', $profileUser->name . ' (@' . $profileUser->username . ') - Profil Komunitas')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    
    <!-- Profile Header Card -->
    <div class="bg-surface-card border border-border-subtle rounded-card p-6 sm:p-8 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6 text-center sm:text-left">
            <div class="flex flex-col sm:flex-row items-center gap-5">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-brand-primary text-text-on-brand-primary font-bold text-2xl flex items-center justify-center flex-shrink-0 shadow-xs">
                    {{ strtoupper(substr($profileUser->name, 0, 2)) }}
                </div>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <h1 class="text-xl sm:text-2xl font-heading font-bold text-text-primary">
                            {{ $profileUser->name }}
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-primary/10 text-brand-secondary dark:text-brand-primary border border-brand-primary/20 capitalize">
                            {{ $profileUser->role->nama ?? 'Pengguna' }}
                        </span>
                    </div>

                    <p class="text-xs sm:text-sm text-text-muted">
                        @ {{ $profileUser->username }}
                    </p>

                    @if ($profileUser->profile && $profileUser->profile->bio)
                        <p class="text-xs sm:text-sm text-text-secondary mt-2 max-w-lg leading-relaxed">
                            {{ $profileUser->profile->bio }}
                        </p>
                    @endif

                    <!-- Details if Student / Teacher / Company -->
                    @if ($profileUser->student && $profileUser->student->schoolClass)
                        <p class="text-xs text-text-muted pt-1">
                            Kelas: <strong>{{ $profileUser->student->schoolClass->nama }}</strong> &bull; Angkatan {{ $profileUser->student->angkatan }}
                        </p>
                    @elseif ($profileUser->teacher)
                        <p class="text-xs text-text-muted pt-1">
                            Guru: <strong>{{ $profileUser->teacher->mata_pelajaran }}</strong> &bull; NIP: {{ $profileUser->teacher->nip }}
                        </p>
                    @elseif ($profileUser->company)
                        <p class="text-xs text-text-muted pt-1">
                            Mitra industri: <strong>{{ $profileUser->company->bidang_industri }}</strong> &bull; {{ $profileUser->company->alamat }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Follow / Edit Profile Action Button -->
            <div class="flex-shrink-0">
                @if (auth()->id() === $profileUser->id)
                    @php
                        $editProfileUrl = route('landing');
                        if (auth()->user()->isSiswa()) $editProfileUrl = route('student.profile');
                        elseif (auth()->user()->isPerusahaan()) $editProfileUrl = route('company.profile');
                    @endphp
                    <a href="{{ $editProfileUrl }}" class="inline-flex items-center px-4 py-2 rounded-btn bg-neutral-100 dark:bg-neutral-800 text-text-primary text-xs font-semibold hover:bg-neutral-200 dark:hover:bg-neutral-700 active:scale-[0.96] transition-transform shadow-xs">
                        Edit profil
                    </a>
                @else
                    <form method="POST" action="{{ route('users.follow', $profileUser) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 rounded-btn font-semibold text-xs active:scale-[0.96] transition-transform shadow-xs {{ $isFollowing ? 'bg-neutral-100 dark:bg-neutral-800 text-text-secondary hover:bg-state-danger-bg hover:text-state-danger-text' : 'bg-brand-primary text-text-on-brand-primary hover:brightness-105' }}">
                            <span>{{ $isFollowing ? 'Mengikuti' : 'Ikuti' }}</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <hr class="border-border-subtle">

        <!-- Counters -->
        <div class="flex items-center justify-around sm:justify-start sm:gap-10 text-xs">
            <div>
                <span class="block text-base sm:text-lg font-heading font-extrabold text-text-primary">{{ $stats['posts_count'] }}</span>
                <span class="text-text-muted">Kiriman</span>
            </div>
            <div>
                <span class="block text-base sm:text-lg font-heading font-extrabold text-text-primary">{{ $stats['followers_count'] }}</span>
                <span class="text-text-muted">Pengikut</span>
            </div>
            <div>
                <span class="block text-base sm:text-lg font-heading font-extrabold text-text-primary">{{ $stats['following_count'] }}</span>
                <span class="text-text-muted">Mengikuti</span>
            </div>
        </div>
    </div>

    <!-- Posts Header Tab -->
    <div class="flex items-center justify-between border-b border-border-subtle pb-3">
        <h2 class="text-sm font-heading font-bold text-text-primary">
            Kiriman dari {{ $profileUser->name }} ({{ $posts->total() }})
        </h2>
    </div>

    <!-- User Posts Stream -->
    <div class="space-y-6">
        @forelse ($posts as $post)
            <article class="bg-surface-card border border-border-subtle rounded-card p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-neutral-100 dark:bg-neutral-800 text-text-primary font-bold text-xs flex items-center justify-center border border-border-subtle flex-shrink-0">
                            {{ strtoupper(substr($post->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <h4 class="font-heading font-bold text-xs sm:text-sm text-text-primary">
                                {{ $post->user->name }}
                            </h4>
                            <p class="text-[11px] text-text-muted mt-0.5">
                                {{ $post->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    @if ($post->category)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-state-success-bg text-state-success-text border border-state-success-border">
                            {{ $post->category->nama }}
                        </span>
                    @endif
                </div>

                <div class="text-xs sm:text-sm text-text-secondary leading-relaxed whitespace-pre-line">
                    {{ $post->isi }}
                </div>

                @if ($post->media)
                    <div class="rounded-card overflow-hidden border border-border-subtle bg-neutral-900/5">
                        <img src="{{ asset('storage/' . $post->media) }}" alt="Lampiran kiriman" class="w-full max-h-96 object-cover">
                    </div>
                @endif

                <div class="pt-2 border-t border-border-subtle flex items-center justify-between text-xs text-text-secondary">
                    <div class="flex items-center gap-4">
                        <form method="POST" action="{{ route('posts.like', $post) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 hover:text-state-danger-text transition-colors {{ $post->isLikedBy(auth()->user()) ? 'text-state-danger-text font-bold' : '' }}">
                                <i data-lucide="heart" class="w-4 h-4 {{ $post->isLikedBy(auth()->user()) ? 'fill-current' : '' }}"></i>
                                <span>{{ $post->like_count }}</span>
                            </button>
                        </form>

                        <span class="inline-flex items-center gap-1.5">
                            <i data-lucide="message-square" class="w-4 h-4"></i>
                            <span>{{ $post->comment_count }} komentar</span>
                        </span>
                    </div>

                    <form method="POST" action="{{ route('posts.bookmark', $post) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 hover:text-brand-primary transition-colors {{ $post->isBookmarkedBy(auth()->user()) ? 'text-brand-secondary dark:text-brand-primary font-bold' : '' }}">
                            <i data-lucide="bookmark" class="w-4 h-4 {{ $post->isBookmarkedBy(auth()->user()) ? 'fill-current' : '' }}"></i>
                            <span>{{ $post->isBookmarkedBy(auth()->user()) ? 'Tersimpan' : 'Simpan' }}</span>
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="p-12 text-center bg-surface-card border border-border-subtle rounded-card shadow-xs">
                <p class="text-xs text-text-muted">Pengguna ini belum mempublikasikan kiriman.</p>
            </div>
        @endforelse

        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection
