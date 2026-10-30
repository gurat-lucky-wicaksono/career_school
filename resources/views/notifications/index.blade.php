@extends('layouts.landing')

@section('title', 'Pusat Notifikasi — Career School SMKN 1 Cirebon')

@section('content')
<div class="py-12 sm:py-16 bg-surface-bg min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-primary/10 text-brand-secondary dark:text-brand-primary border border-brand-primary/20 mb-2">
                    <i data-lucide="bell" class="w-3.5 h-3.5"></i>
                    <span>Pusat Notifikasi Akun</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Pemberitahuan & Aktivitas
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1">
                    Informasi penting terkait lamaran industri, pengesahan kompetensi guru, dan interaksi jejaring komunitas.
                </p>
            </div>

            @if($unreadCount > 0)
                <div class="shrink-0">
                    <form method="POST" action="{{ route('notifications.markAllRead') }}">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200/80 dark:border-white/10 bg-surface-card text-xs font-semibold text-slate-700 dark:text-slate-200 hover:border-brand-primary hover:text-brand-secondary dark:hover:text-brand-primary transition-all shadow-xs"
                        >
                            <i data-lucide="check-check" class="w-4 h-4 text-emerald-500"></i>
                            <span>Tandai Semua Dibaca</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Alert Flash -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs sm:text-sm flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0 text-xs">
                @php
                    $currentStatus = request('status', '');
                    $currentJenis = request('jenis', '');
                @endphp

                <a
                    href="{{ route('notifications.index') }}"
                    class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all {{ $currentStatus === '' && $currentJenis === '' ? 'bg-brand-primary text-slate-950 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5' }}"
                >
                    Semua ({{ $totalCount }})
                </a>

                <a
                    href="{{ route('notifications.index', ['status' => 'unread']) }}"
                    class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-1.5 {{ $currentStatus === 'unread' ? 'bg-brand-primary text-slate-950 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5' }}"
                >
                    <span>Belum Dibaca</span>
                    @if($unreadCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-red-500 text-white">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>

                <a
                    href="{{ route('notifications.index', ['jenis' => 'lamaran']) }}"
                    class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all {{ $currentJenis === 'lamaran' ? 'bg-brand-primary text-slate-950 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5' }}"
                >
                    Lamaran & Karier
                </a>

                <a
                    href="{{ route('notifications.index', ['jenis' => 'keahlian']) }}"
                    class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all {{ $currentJenis === 'keahlian' ? 'bg-brand-primary text-slate-950 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5' }}"
                >
                    Validasi Keahlian
                </a>

                <a
                    href="{{ route('notifications.index', ['jenis' => 'komunitas']) }}"
                    class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all {{ $currentJenis === 'komunitas' ? 'bg-brand-primary text-slate-950 font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5' }}"
                >
                    Jejaring Komunitas
                </a>
            </div>
        </div>

        <!-- Notification List -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-3xl shadow-sm overflow-hidden divide-y divide-slate-100 dark:divide-white/5">
            @forelse($notifications as $item)
                <div class="p-5 sm:p-6 transition-colors flex items-start gap-4 {{ $item->dibaca ? 'opacity-80' : 'bg-brand-primary/[0.03] dark:bg-white/[0.02]' }}">
                    <!-- Icon Box -->
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 mt-0.5 border {{ $item->jenis_badge_class }}">
                        <i data-lucide="{{ $item->icon }}" class="w-5 h-5"></i>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0 space-y-1.5">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs sm:text-sm text-slate-900 dark:text-white leading-relaxed {{ $item->dibaca ? 'font-medium' : 'font-bold' }}">
                                {{ $item->judul }}
                            </p>
                            @if(!$item->dibaca)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-primary/20 text-brand-secondary dark:text-brand-primary shrink-0">
                                    Baru
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-400 pt-1">
                            <span>{{ $item->created_at->diffForHumans() }}</span>
                            <span>&bull;</span>
                            <span class="capitalize">{{ $item->jenis }}</span>

                            <div class="ml-auto flex items-center gap-3">
                                <form method="POST" action="{{ route('notifications.read', $item->id) }}" class="inline">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="text-xs font-semibold text-brand-secondary dark:text-brand-primary hover:underline inline-flex items-center gap-1"
                                    >
                                        <span>Buka Tautan</span>
                                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/10 text-slate-400 mx-auto flex items-center justify-center">
                        <i data-lucide="bell-off" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-base">Tidak Ada Notifikasi</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                        Anda tidak memiliki pemberitahuan yang sesuai dengan filter ini saat ini.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="pt-4">
                {{ $notifications->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
