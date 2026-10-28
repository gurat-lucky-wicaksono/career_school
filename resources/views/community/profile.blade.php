@extends('layouts.community')

@section('title', $profileUser->name . ' (@' . $profileUser->username . ') - Profil Komunitas')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    
    <!-- Profile Header Card -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-6 text-center sm:text-left">
            <div class="flex flex-col sm:flex-row items-center gap-5">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-brand-primary/20 text-brand-secondary dark:text-brand-primary font-bold text-2xl flex items-center justify-center border-2 border-brand-primary/40 flex-shrink-0 shadow-sm">
                    {{ strtoupper(substr($profileUser->name, 0, 2)) }}
                </div>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">
                            {{ $profileUser->name }}
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-primary/10 text-brand-secondary dark:text-brand-primary border border-brand-primary/20 capitalize">
                            {{ $profileUser->role->nama ?? 'Pengguna' }}
                        </span>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-400">
                        @ {{ $profileUser->username }}
                    </p>

                    @if ($profileUser->profile && $profileUser->profile->bio)
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mt-2 max-w-lg leading-relaxed">
                            {{ $profileUser->profile->bio }}
                        </p>
                    @endif

                    <!-- Details if Student / Teacher / Company -->
                    @if ($profileUser->student && $profileUser->student->schoolClass)
                        <p class="text-xs text-slate-500 dark:text-slate-400 pt-1">
                            Kelas: <strong>{{ $profileUser->student->schoolClass->nama }}</strong> &bull; Angkatan {{ $profileUser->student->angkatan }}
                        </p>
                    @elseif ($profileUser->teacher)
                        <p class="text-xs text-slate-500 dark:text-slate-400 pt-1">
                            Guru: <strong>{{ $profileUser->teacher->mata_pelajaran }}</strong> &bull; NIP: {{ $profileUser->teacher->nip }}
                        </p>
                    @elseif ($profileUser->company)
                        <p class="text-xs text-slate-500 dark:text-slate-400 pt-1">
                            Mitra Industri: <strong>{{ $profileUser->company->bidang_industri }}</strong> &bull; {{ $profileUser->company->alamat }}
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
                    <a href="{{ $editProfileUrl }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-neutral-700 transition-colors shadow-xs">
                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        Edit Profil
                    </a>
                @else
                    <form method="POST" action="{{ route('users.follow', $profileUser) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-bold text-xs transition-all shadow-sm {{ $isFollowing ? 'bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-200 hover:bg-rose-50 hover:text-rose-600' : 'bg-brand-primary text-neutral-900 hover:brightness-105 active:scale-95' }}">
                            <i data-lucide="{{ $isFollowing ? 'user-check' : 'user-plus' }}" class="w-4 h-4"></i>
                            <span>{{ $isFollowing ? 'Mengikuti' : 'Ikuti' }}</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <hr class="border-slate-100 dark:border-white/5">

        <!-- Counters -->
        <div class="flex items-center justify-around sm:justify-start sm:gap-10 text-xs">
            <div>
                <span class="block text-base sm:text-lg font-extrabold text-slate-900 dark:text-white">{{ $stats['posts_count'] }}</span>
                <span class="text-slate-400">Kiriman</span>
            </div>
            <div>
                <span class="block text-base sm:text-lg font-extrabold text-slate-900 dark:text-white">{{ $stats['followers_count'] }}</span>
                <span class="text-slate-400">Pengikut</span>
            </div>
            <div>
                <span class="block text-base sm:text-lg font-extrabold text-slate-900 dark:text-white">{{ $stats['following_count'] }}</span>
                <span class="text-slate-400">Mengikuti</span>
            </div>
        </div>
    </div>

    <!-- Posts Header Tab -->
    <div class="flex items-center justify-between border-b border-slate-200/80 dark:border-white/10 pb-3">
        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
            Kiriman dari {{ $profileUser->name }} ({{ $posts->total() }})
        </h2>
    </div>

    <!-- User Posts Stream -->
    <div class="space-y-6">
        @forelse ($posts as $post)
            <article class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 sm:p-6 shadow-sm space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-neutral-300 font-bold text-xs flex items-center justify-center border border-slate-200 dark:border-neutral-700 flex-shrink-0">
                            {{ strtoupper(substr($post->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white">
                                {{ $post->user->name }}
                            </h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $post->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    @if ($post->category)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            {{ $post->category->nama }}
                        </span>
                    @endif
                </div>

                <div class="text-xs sm:text-sm text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line">
                    {{ $post->isi }}
                </div>

                @if ($post->media)
                    <div class="rounded-xl overflow-hidden border border-slate-100 dark:border-white/5 bg-slate-900/5">
                        <img src="{{ asset('storage/' . $post->media) }}" alt="Lampiran Kiriman" class="w-full max-h-96 object-cover">
                    </div>
                @endif

                <div class="pt-2 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-4">
                        <form method="POST" action="{{ route('posts.like', $post) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 hover:text-rose-500 transition-colors {{ $post->isLikedBy(auth()->user()) ? 'text-rose-600 font-bold' : '' }}">
                                <i data-lucide="heart" class="w-4 h-4 {{ $post->isLikedBy(auth()->user()) ? 'fill-current' : '' }}"></i>
                                <span>{{ $post->like_count }}</span>
                            </button>
                        </form>

                        <span class="inline-flex items-center gap-1.5">
                            <i data-lucide="message-square" class="w-4 h-4"></i>
                            <span>{{ $post->comment_count }} Komentar</span>
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
            <div class="p-12 text-center bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm">
                <p class="text-xs text-slate-400">Pengguna ini belum mempublikasikan kiriman.</p>
            </div>
        @endforelse

        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection
