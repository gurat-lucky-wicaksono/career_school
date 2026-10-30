<div 
    x-data="{
        open: false,
        loading: false,
        unreadCount: {{ auth()->check() ? auth()->user()->notifications()->where('dibaca', false)->count() : 0 }},
        notifications: [],
        init() {
            // Pre-load notifications count on init
        },
        toggle() {
            this.open = !this.open;
            if (this.open && this.notifications.length === 0) {
                this.fetchNotifications();
            }
        },
        fetchNotifications() {
            this.loading = true;
            fetch('{{ route('notifications.index') }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
            })
            .catch(() => {})
            .finally(() => {
                this.loading = false;
                if (window.lucide) setTimeout(() => window.lucide.createIcons(), 50);
            });
        },
        markAsRead(id, url) {
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.unreadCount = data.unread_count || 0;
                window.location.href = url;
            })
            .catch(() => {
                window.location.href = url;
            });
        },
        markAllRead() {
            fetch('{{ route('notifications.markAllRead') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(() => {
                this.unreadCount = 0;
                this.notifications.forEach(n => n.dibaca = true);
            })
            .catch(() => {});
        }
    }"
    @click.away="open = false"
    class="relative inline-block text-left"
>
    <!-- Bell Trigger Button -->
    <button
        type="button"
        @click="toggle()"
        class="relative w-9 h-9 sm:w-10 sm:h-10 rounded-xl border border-slate-200/80 dark:border-white/10 bg-surface-card text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:border-slate-300 dark:hover:border-white/20 transition-all flex items-center justify-center shadow-xs"
        aria-label="Buka Notifikasi"
    >
        <i data-lucide="bell" class="w-4 h-4 sm:w-4.5 sm:h-4.5"></i>

        <!-- Unread Badge -->
        <span
            x-show="unreadCount > 0"
            x-text="unreadCount > 99 ? '99+' : unreadCount"
            class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white font-extrabold text-[10px] flex items-center justify-center ring-2 ring-white dark:ring-slate-900 shadow-xs animate-pulse"
            style="display: none;"
        ></span>
    </button>

    <!-- Dropdown Panel -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-surface-card border border-slate-200/80 dark:border-white/10 shadow-xl z-50 overflow-hidden divide-y divide-slate-100 dark:divide-white/5"
        style="display: none;"
    >
        <!-- Header -->
        <div class="px-4 py-3 flex items-center justify-between bg-slate-50/70 dark:bg-white/[0.02]">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Notifikasi</span>
                <span
                    x-show="unreadCount > 0"
                    x-text="`${unreadCount} baru`"
                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-primary/20 text-brand-secondary dark:text-brand-primary"
                ></span>
            </div>

            <button
                type="button"
                x-show="unreadCount > 0"
                @click="markAllRead()"
                class="text-[11px] font-medium text-brand-secondary dark:text-brand-primary hover:underline"
            >
                Tandai semua dibaca
            </button>
        </div>

        <!-- Notification List -->
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-white/5">
            <!-- Loading Spinner -->
            <template x-if="loading">
                <div class="py-8 text-center text-xs text-slate-400">
                    Memuat notifikasi...
                </div>
            </template>

            <!-- Empty State -->
            <template x-if="!loading && notifications.length === 0">
                <div class="py-8 text-center text-slate-400 space-y-1">
                    <i data-lucide="bell-off" class="w-6 h-6 mx-auto text-slate-300 dark:text-slate-600"></i>
                    <p class="text-xs">Tidak ada notifikasi baru.</p>
                </div>
            </template>

            <!-- Items -->
            <template x-for="item in notifications" :key="item.id">
                <a
                    :href="item.url"
                    @click.prevent="markAsRead(item.id, item.url)"
                    class="block px-4 py-3 transition-colors hover:bg-slate-50/80 dark:hover:bg-white/[0.03]"
                    :class="item.dibaca ? 'opacity-70' : 'bg-brand-primary/[0.04] dark:bg-white/[0.02]'"
                >
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center text-slate-600 dark:text-slate-300 shrink-0 mt-0.5">
                            <i data-lucide="bell" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1 min-w-0 space-y-1">
                            <p 
                                class="text-xs text-slate-800 dark:text-slate-200 line-clamp-2 leading-relaxed"
                                :class="item.dibaca ? 'font-normal' : 'font-semibold'"
                                x-text="item.judul"
                            ></p>
                            <span class="text-[10px] text-slate-400 block" x-text="item.created_at"></span>
                        </div>
                        <template x-if="!item.dibaca">
                            <span class="w-2 h-2 rounded-full bg-brand-primary shrink-0 mt-1.5"></span>
                        </template>
                    </div>
                </a>
            </template>
        </div>

        <!-- Footer -->
        <div class="p-2.5 bg-slate-50/70 dark:bg-white/[0.02] text-center">
            <a
                href="{{ route('notifications.index') }}"
                class="block py-1.5 text-xs font-semibold text-brand-secondary dark:text-brand-primary hover:underline"
            >
                Buka Pusat Notifikasi Lengkap
            </a>
        </div>
    </div>
</div>
