@extends('layouts.admin')

@section('title', 'Pusat Notifikasi')

@section('content')
<div class="space-y-6">
    <!-- Top Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Pusat Notifikasi
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Pantau seluruh aktivitas kemitraan industri, bursa kerja, dan status sistem sekolah secara real-time
            </p>
        </div>

        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('admin.notifications.markAllRead') }}">
                @csrf
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-full border border-border-default hover:border-brand-primary bg-surface-card hover:bg-surface-bg text-text-primary text-xs font-semibold flex items-center gap-2 transition-all shadow-xs"
                >
                    <i data-lucide="check-check" class="w-3.5 h-3.5 text-brand-secondary dark:text-brand-primary"></i>
                    <span>Tandai Semua Dibaca</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 custom-scrollbar">
        <a 
            href="{{ route('admin.notifications.index') }}" 
            class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ !request('jenis') && !request('status') ? 'bg-brand-primary text-neutral-900' : 'bg-surface-card border border-border-default text-text-secondary hover:text-text-primary' }}"
        >
            Semua
        </a>
        <a 
            href="{{ route('admin.notifications.index', ['status' => 'unread']) }}" 
            class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ request('status') === 'unread' ? 'bg-brand-primary text-neutral-900' : 'bg-surface-card border border-border-default text-text-secondary hover:text-text-primary' }}"
        >
            Belum Dibaca ({{ $unreadCount }})
        </a>
        <a 
            href="{{ route('admin.notifications.index', ['jenis' => 'kemitraan']) }}" 
            class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ request('jenis') === 'kemitraan' ? 'bg-brand-primary text-neutral-900' : 'bg-surface-card border border-border-default text-text-secondary hover:text-text-primary' }}"
        >
            Kemitraan
        </a>
        <a 
            href="{{ route('admin.notifications.index', ['jenis' => 'lowongan']) }}" 
            class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ request('jenis') === 'lowongan' ? 'bg-brand-primary text-neutral-900' : 'bg-surface-card border border-border-default text-text-secondary hover:text-text-primary' }}"
        >
            Lowongan
        </a>
        <a 
            href="{{ route('admin.notifications.index', ['jenis' => 'siswa']) }}" 
            class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ request('jenis') === 'siswa' ? 'bg-brand-primary text-neutral-900' : 'bg-surface-card border border-border-default text-text-secondary hover:text-text-primary' }}"
        >
            Siswa
        </a>
        <a 
            href="{{ route('admin.notifications.index', ['jenis' => 'sistem']) }}" 
            class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ request('jenis') === 'sistem' ? 'bg-brand-primary text-neutral-900' : 'bg-surface-card border border-border-default text-text-secondary hover:text-text-primary' }}"
        >
            Sistem
        </a>
    </div>

    <!-- Notification Cards List -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm divide-y divide-slate-100 dark:divide-white/5 overflow-hidden">
        @forelse ($notifications as $notif)
            @php
                $icon = match($notif->jenis) {
                    'kemitraan' => 'building-2',
                    'lowongan' => 'briefcase',
                    'siswa' => 'graduation-cap',
                    'guru' => 'user-check',
                    default => 'bell',
                };
                $badgeVariant = match($notif->jenis) {
                    'kemitraan' => 'primary',
                    'lowongan' => 'info',
                    'siswa' => 'success',
                    default => 'default',
                };
            @endphp
            <div class="p-4 sm:p-5 flex items-start justify-between gap-4 transition-colors {{ $notif->dibaca ? 'hover:bg-slate-50/60 dark:hover:bg-neutral-800/30' : 'bg-brand-primary/5 hover:bg-brand-primary/10' }}">
                <div class="flex items-start gap-3.5">
                    <!-- Icon Bubble -->
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $notif->dibaca ? 'bg-slate-100 dark:bg-neutral-800 text-slate-500 dark:text-slate-400' : 'bg-brand-primary/20 text-brand-secondary dark:text-brand-primary font-bold' }}">
                        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                    </div>

                    <!-- Content -->
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold uppercase tracking-wider px-2 py-0.5 rounded-md bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300">
                                {{ ucfirst($notif->jenis) }}
                            </span>
                            @if (!$notif->dibaca)
                                <span class="w-2 h-2 rounded-full bg-brand-primary animate-pulse"></span>
                                <span class="text-[11px] font-semibold text-brand-secondary dark:text-brand-primary">Baru</span>
                            @endif
                            <span class="text-[11px] text-slate-400 dark:text-neutral-500">
                                {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-100 leading-snug">
                            {{ $notif->judul }}
                        </p>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <form method="POST" action="{{ route('admin.notifications.read', $notif->id) }}">
                        @csrf
                        <button 
                            type="submit" 
                            class="px-4 py-2 rounded-full text-xs font-semibold bg-surface-bg hover:bg-slate-200/60 dark:hover:bg-neutral-800 border border-border-default text-text-primary transition-all flex items-center gap-1.5 shadow-xs"
                            title="Buka detail"
                        >
                            <span>Buka</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="py-16 text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-neutral-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                    <i data-lucide="bell-off" class="w-6 h-6"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Tidak Ada Notifikasi</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                    Seluruh pemberitahuan sistem dan aktivitas sekolah telah ditinjau atau tidak ditemukan dengan filter yang dipilih.
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($notifications->hasPages())
        <div class="pt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
