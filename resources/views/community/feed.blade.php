@extends('layouts.community')

@section('title', 'Linimasa Komunitas & Feed Vokasi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- 1. Left Sidebar: Profil Pengguna & Kategori (Col 3) -->
        <aside class="hidden lg:block lg:col-span-3 space-y-6 sticky top-24">
            <!-- User Mini Profile Card -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-brand-primary/20 text-brand-secondary dark:text-brand-primary font-bold text-sm flex items-center justify-center border border-brand-primary/30 flex-shrink-0">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-slate-900 dark:text-white truncate text-sm">
                            <a href="{{ route('community.profile', $user->username) }}" class="hover:underline">
                                {{ $user->name }}
                            </a>
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                            @ {{ $user->username }}
                        </p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-300 mt-1 capitalize">
                            {{ $user->role->nama ?? 'Pengguna' }}
                        </span>
                    </div>
                </div>

                <hr class="border-slate-100 dark:border-white/5">

                <!-- User Stats -->
                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                    <div>
                        <span class="block font-bold text-slate-900 dark:text-white">{{ $userStats['posts_count'] }}</span>
                        <span class="text-[10px] text-slate-400">Kiriman</span>
                    </div>
                    <div>
                        <span class="block font-bold text-slate-900 dark:text-white">{{ $userStats['followers_count'] }}</span>
                        <span class="text-[10px] text-slate-400">Pengikut</span>
                    </div>
                    <div>
                        <span class="block font-bold text-slate-900 dark:text-white">{{ $userStats['following_count'] }}</span>
                        <span class="text-[10px] text-slate-400">Mengikuti</span>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="{{ route('community.profile', $user->username) }}" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-neutral-700 transition-colors">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                        Lihat Profil Saya
                    </a>
                </div>
            </div>

            <!-- Categories Filter Menu -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm space-y-3">
                <h4 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                    Kategori Komunitas
                </h4>
                <div class="space-y-1 text-xs">
                    <a 
                        href="{{ route('feed.index') }}" 
                        class="flex items-center justify-between px-3 py-2 rounded-xl {{ !request('kategori') ? 'bg-brand-primary/10 text-brand-secondary dark:text-brand-primary font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-neutral-800' }} transition-colors"
                    >
                        <span>Semua Kategori</span>
                    </a>
                    @foreach ($categories as $cat)
                        <a 
                            href="{{ route('feed.index', ['kategori' => $cat->slug]) }}" 
                            class="flex items-center justify-between px-3 py-2 rounded-xl {{ request('kategori') === $cat->slug ? 'bg-brand-primary/10 text-brand-secondary dark:text-brand-primary font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-neutral-800' }} transition-colors"
                        >
                            <span class="truncate">{{ $cat->nama }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-slate-100 dark:bg-neutral-800 text-slate-500 dark:text-slate-400">
                                {{ $cat->posts_count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>

        <!-- 2. Main Center: Create Post & Timeline Feed (Col 6) -->
        <main class="lg:col-span-6 space-y-6">
            
            <!-- Create Post Card -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-primary/20 text-brand-secondary dark:text-brand-primary font-bold text-xs flex items-center justify-center border border-brand-primary/30 flex-shrink-0">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <form method="POST" action="{{ route('feed.store') }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            
                            <textarea 
                                name="isi" 
                                rows="3" 
                                required
                                placeholder="Bagikan proyek kejuruan, pertanyaan teknis, materi belajar, atau kabar magang Anda... Gunakan #tagar"
                                class="w-full p-3 text-xs sm:text-sm rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary resize-none transition-all"
                            >{{ old('isi') }}</textarea>

                            <!-- Options: Category & Media -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                                <div class="flex items-center gap-2">
                                    <!-- Category Select -->
                                    <select 
                                        name="category_id" 
                                        class="px-2.5 py-1.5 text-xs rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-brand-primary"
                                    >
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                                        @endforeach
                                    </select>

                                    <!-- Media Attachment Button -->
                                    <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-700 dark:text-slate-300 text-xs hover:bg-slate-100 dark:hover:bg-neutral-700 transition-colors">
                                        <i data-lucide="image" class="w-3.5 h-3.5 text-emerald-500"></i>
                                        <span>Foto / Karya</span>
                                        <input type="file" name="media" accept="image/*" class="hidden">
                                    </label>
                                </div>

                                <button 
                                    type="submit" 
                                    class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-xl bg-brand-primary text-neutral-900 font-semibold text-xs hover:brightness-105 active:scale-[0.98] transition-all shadow-sm"
                                >
                                    <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                    Bagikan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Feed Tabs (Semua Kiriman vs Mengikuti) -->
            <div class="flex items-center justify-between border-b border-slate-200/80 dark:border-white/10 pb-3 text-xs">
                <div class="flex items-center gap-2">
                    @php
                        $currentTab = request('tab', 'all');
                    @endphp
                    <a 
                        href="{{ route('feed.index', array_merge(request()->except('tab'), ['tab' => 'all'])) }}" 
                        class="px-4 py-2 rounded-xl font-bold transition-all {{ $currentTab === 'all' ? 'bg-surface-card text-brand-secondary dark:text-brand-primary shadow-xs border border-slate-200/80 dark:border-white/10' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}"
                    >
                        Semua Kiriman
                    </a>
                    <a 
                        href="{{ route('feed.index', array_merge(request()->except('tab'), ['tab' => 'following'])) }}" 
                        class="px-4 py-2 rounded-xl font-bold transition-all {{ $currentTab === 'following' ? 'bg-surface-card text-brand-secondary dark:text-brand-primary shadow-xs border border-slate-200/80 dark:border-white/10' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}"
                    >
                        Mengikuti
                    </a>
                </div>

                @if (request('kategori') || request('tag') || request('q'))
                    <a href="{{ route('feed.index') }}" class="text-[11px] font-semibold text-rose-500 hover:underline">
                        Reset Filter
                    </a>
                @endif
            </div>

            <!-- Active Tag / Category Banner -->
            @if (request('tag'))
                <div class="p-3 rounded-xl bg-brand-primary/10 border border-brand-primary/20 text-xs font-semibold text-brand-secondary dark:text-brand-primary flex items-center justify-between">
                    <span>Menampilkan kiriman dengan tagar: <strong>#{{ request('tag') }}</strong></span>
                    <a href="{{ route('feed.index') }}" class="text-[11px] hover:underline">&times; Hapus</a>
                </div>
            @endif

            <!-- Post List -->
            @forelse ($posts as $post)
                <article class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 sm:p-6 shadow-sm space-y-4">
                    <!-- Post Header -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('community.profile', $post->user->username) }}" class="w-10 h-10 rounded-full bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-neutral-300 font-bold text-xs flex items-center justify-center border border-slate-200 dark:border-neutral-700 flex-shrink-0">
                                {{ strtoupper(substr($post->user->name, 0, 2)) }}
                            </a>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white">
                                        <a href="{{ route('community.profile', $post->user->username) }}" class="hover:underline">
                                            {{ $post->user->name }}
                                        </a>
                                    </h4>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300 capitalize">
                                        {{ $post->user->role->nama ?? 'Warga Sekolah' }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    @ {{ $post->user->username }} &bull; {{ $post->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        <!-- Category or Delete Option -->
                        <div class="flex items-center gap-2">
                            @if ($post->category)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    {{ $post->category->nama }}
                                </span>
                            @endif

                            @if (auth()->id() === $post->user_id || auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('feed.destroy', $post) }}" onsubmit="return confirm('Hapus kiriman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-500 transition-colors" title="Hapus kiriman">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Post Body (with regex autolinking for hashtags) -->
                    <div class="text-xs sm:text-sm text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line">
                        @php
                            $formattedText = preg_replace_callback('/#([a-zA-Z0-9_\x{0080}-\x{FFFF}]+)/u', function($matches) {
                                $tag = $matches[1];
                                $url = route('feed.index', ['tag' => $tag]);
                                return '<a href="' . e($url) . '" class="text-brand-secondary dark:text-brand-primary font-semibold hover:underline">#' . e($tag) . '</a>';
                            }, e($post->isi));
                        @endphp
                        {!! $formattedText !!}
                    </div>

                    <!-- Media Attachment -->
                    @if ($post->media)
                        <div class="rounded-xl overflow-hidden border border-slate-100 dark:border-white/5 bg-slate-900/5">
                            <img src="{{ asset('storage/' . $post->media) }}" alt="Lampiran Kiriman" class="w-full max-h-96 object-cover hover:scale-[1.01] transition-transform duration-200">
                        </div>
                    @endif

                    <!-- Interactions Toolbar -->
                    <div class="pt-2 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-4">
                            <!-- Like Button -->
                            <form method="POST" action="{{ route('posts.like', $post) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 hover:text-rose-500 transition-colors {{ $post->isLikedBy(auth()->user()) ? 'text-rose-600 font-bold' : '' }}">
                                    <i data-lucide="heart" class="w-4 h-4 {{ $post->isLikedBy(auth()->user()) ? 'fill-current' : '' }}"></i>
                                    <span>{{ $post->like_count }}</span>
                                </button>
                            </form>

                            <!-- Comment Count Display -->
                            <span class="inline-flex items-center gap-1.5">
                                <i data-lucide="message-square" class="w-4 h-4"></i>
                                <span>{{ $post->comment_count }} Komentar</span>
                            </span>
                        </div>

                        <!-- Bookmark Button -->
                        <form method="POST" action="{{ route('posts.bookmark', $post) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 hover:text-brand-primary transition-colors {{ $post->isBookmarkedBy(auth()->user()) ? 'text-brand-secondary dark:text-brand-primary font-bold' : '' }}">
                                <i data-lucide="bookmark" class="w-4 h-4 {{ $post->isBookmarkedBy(auth()->user()) ? 'fill-current' : '' }}"></i>
                                <span>{{ $post->isBookmarkedBy(auth()->user()) ? 'Tersimpan' : 'Simpan' }}</span>
                            </button>
                        </form>
                    </div>

                    <!-- Comments Section -->
                    <div x-data="{ showReply: false, replyToId: null }" class="pt-3 border-t border-slate-100 dark:border-white/5 space-y-3">
                        <!-- Add Comment Form -->
                        <form method="POST" action="{{ route('posts.comments.store', $post) }}" class="flex items-center gap-2">
                            @csrf
                            <input 
                                type="text" 
                                name="isi" 
                                required 
                                maxlength="1000"
                                placeholder="Tuliskan komentar atau tanggapan..."
                                class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-primary"
                            >
                            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-200 font-semibold text-xs hover:bg-slate-200 dark:hover:bg-neutral-700 transition-colors flex-shrink-0">
                                Kirim
                            </button>
                        </form>

                        <!-- Comments Stream -->
                        @if ($post->comments->count() > 0)
                            <div class="space-y-2.5 pt-1">
                                @foreach ($post->comments->take(4) as $comment)
                                    <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5 text-xs space-y-1.5">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('community.profile', $comment->user->username) }}" class="font-bold text-slate-900 dark:text-white hover:underline">
                                                    {{ $comment->user->name }}
                                                </a>
                                                <span class="text-[10px] text-slate-400">
                                                    {{ $comment->created_at ? $comment->created_at->diffForHumans() : '' }}
                                                </span>
                                            </div>

                                            @if (auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                                                <form method="POST" action="{{ route('comments.destroy', $comment) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-slate-400 hover:text-rose-500 text-[10px]" title="Hapus komentar">
                                                        &times;
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                                            {{ $comment->isi }}
                                        </p>

                                        <!-- Nested Replies -->
                                        @if ($comment->replies->count() > 0)
                                            <div class="pl-4 border-l-2 border-brand-primary/30 space-y-2 pt-1 mt-1">
                                                @foreach ($comment->replies as $reply)
                                                    <div class="text-[11px] space-y-0.5">
                                                        <div class="flex items-center gap-1.5">
                                                            <a href="{{ route('community.profile', $reply->user->username) }}" class="font-bold text-slate-900 dark:text-white hover:underline">
                                                                {{ $reply->user->name }}
                                                            </a>
                                                            <span class="text-[9px] text-slate-400">
                                                                {{ $reply->created_at ? $reply->created_at->diffForHumans() : '' }}
                                                            </span>
                                                        </div>
                                                        <p class="text-slate-600 dark:text-slate-300">{{ $reply->isi }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="p-12 text-center bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-neutral-800 text-slate-400 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="message-square-off" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Belum Ada Kiriman</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                        Jadilah yang pertama membagikan hasil proyek kejuruan atau pertanyaan diskusi di komunitas vokasi ini.
                    </p>
                </div>
            @endforelse

            <!-- Pagination -->
            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        </main>

        <!-- 3. Right Sidebar: Tagar Populer & Saran Mengikuti (Col 3) -->
        <aside class="hidden lg:block lg:col-span-3 space-y-6 sticky top-24">
            
            <!-- Popular Hashtags Card -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                    <i data-lucide="hash" class="w-4 h-4 text-brand-secondary dark:text-brand-primary"></i>
                    <span>Tagar Populer</span>
                </div>
                <div class="flex flex-wrap gap-1.5 text-xs">
                    @forelse ($popularHashtags as $hash)
                        <a 
                            href="{{ route('feed.index', ['tag' => $hash->tag]) }}" 
                            class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-neutral-800 hover:bg-brand-primary/10 hover:text-brand-secondary dark:hover:text-brand-primary transition-colors text-[11px] font-medium text-slate-700 dark:text-slate-300"
                        >
                            #{{ $hash->tag }}
                            <span class="text-[10px] text-slate-400">({{ $hash->jumlah_pakai }})</span>
                        </a>
                    @empty
                        <span class="text-xs text-slate-400">Belum ada tagar aktif.</span>
                    @endforelse
                </div>
            </div>

            <!-- Suggested Users to Follow -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                    <i data-lucide="user-plus" class="w-4 h-4 text-blue-500"></i>
                    <span>Saran Jejaring</span>
                </div>
                <div class="space-y-3 text-xs">
                    @forelse ($suggestedUsers as $sugUser)
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-200 font-bold text-[11px] flex items-center justify-center border border-slate-200 dark:border-neutral-700 flex-shrink-0">
                                    {{ strtoupper(substr($sugUser->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('community.profile', $sugUser->username) }}" class="font-bold text-slate-900 dark:text-white hover:underline truncate block">
                                        {{ $sugUser->name }}
                                    </a>
                                    <span class="text-[10px] text-slate-400 truncate block capitalize">
                                        {{ $sugUser->role->nama ?? 'Warga' }}
                                    </span>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('users.follow', $sugUser) }}">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-brand-primary text-neutral-900 font-bold text-[10px] hover:brightness-105 transition-all">
                                    Ikuti
                                </button>
                            </form>
                        </div>
                    @empty
                        <span class="text-xs text-slate-400">Belum ada rekomendasi baru.</span>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
