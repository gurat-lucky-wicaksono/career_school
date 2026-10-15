@props([
    'breadcrumbs' => [],
])

<header class="h-16 bg-surface-card/95 backdrop-blur-md border-b border-slate-200/80 dark:border-white/10 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 transition-all duration-150">
    <!-- Left Section: Mobile Toggle & Breadcrumbs -->
    <div class="flex items-center gap-3 sm:gap-4 overflow-hidden flex-shrink-0">
        <!-- Mobile Sidebar Hamburger -->
        <button 
            type="button" 
            @click="sidebarOpen = !sidebarOpen" 
            aria-label="Buka Menu Navigasi"
            class="lg:hidden w-9 h-9 rounded-xl border border-slate-200/80 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-neutral-800 flex items-center justify-center transition-colors"
        >
            <i data-lucide="menu" class="w-4 h-4"></i>
        </button>

        <!-- Desktop Mini Toggle -->
        <button 
            type="button" 
            @click="toggleSidebar()" 
            aria-label="Ubah Lebar Sidebar"
            class="hidden lg:flex w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-neutral-800 items-center justify-center transition-colors"
        >
            <i data-lucide="panel-left" class="w-4 h-4"></i>
        </button>

        <!-- Breadcrumbs Component -->
        <div class="truncate">
            <x-ui.breadcrumb :items="$breadcrumbs" />
        </div>
    </div>

    <!-- Center Section: Quick Search Command Bar (Alpine.js Interactive Command Palette) -->
    <div 
        x-data="{
            query: '',
            open: false,
            loading: false,
            groups: [],
            flatItems: [],
            selectedIndex: 0,
            async doSearch() {
                const q = this.query.trim();
                if (q.length < 2) {
                    this.groups = [];
                    this.flatItems = [];
                    this.open = false;
                    this.loading = false;
                    return;
                }
                this.loading = true;
                this.open = true;
                try {
                    const res = await fetch('{{ route('admin.search') }}?q=' + encodeURIComponent(q), {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    this.groups = data.groups || [];
                    const flat = [];
                    this.groups.forEach(g => {
                        (g.items || []).forEach(item => flat.push(item));
                    });
                    this.flatItems = flat;
                    this.selectedIndex = flat.length > 0 ? 0 : -1;
                } catch (e) {
                    this.groups = [];
                    this.flatItems = [];
                } finally {
                    this.loading = false;
                    this.$nextTick(() => {
                        if (window.lucide && window.lucide.createIcons) window.lucide.createIcons();
                    });
                }
            },
            navigateNext() {
                if (this.flatItems.length === 0) return;
                this.selectedIndex = (this.selectedIndex + 1) % this.flatItems.length;
                this.scrollSelected();
            },
            navigatePrev() {
                if (this.flatItems.length === 0) return;
                this.selectedIndex = (this.selectedIndex - 1 + this.flatItems.length) % this.flatItems.length;
                this.scrollSelected();
            },
            selectItem(item) {
                if (item && item.url) {
                    window.location.href = item.url;
                }
            },
            selectCurrent() {
                if (this.selectedIndex >= 0 && this.flatItems[this.selectedIndex]) {
                    this.selectItem(this.flatItems[this.selectedIndex]);
                } else if (this.query.trim().length > 0) {
                    window.location.href = '{{ route('admin.students.index') }}?search=' + encodeURIComponent(this.query.trim());
                }
            },
            scrollSelected() {
                this.$nextTick(() => {
                    const el = this.$refs.resultsList?.querySelector('[data-selected=true]');
                    if (el) el.scrollIntoView({ block: 'nearest' });
                });
            },
            clearSearch() {
                this.query = '';
                this.groups = [];
                this.flatItems = [];
                this.open = false;
                this.$refs.searchInput.focus();
            }
        }"
        @click.outside="open = false"
        @keydown.escape.window="open = false"
        class="relative hidden md:flex items-center flex-1 max-w-md xl:max-w-lg mx-4 lg:mx-8"
    >
        <div class="relative w-full">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input 
                x-ref="searchInput"
                id="global-search-input"
                type="text" 
                x-model="query"
                @input.debounce.250ms="doSearch()"
                @focus="if (query.trim().length >= 2) { open = true; if (flatItems.length === 0) doSearch(); }"
                @keydown.down.prevent="if (!open && query.trim().length >= 2) { open = true; } else { navigateNext(); }"
                @keydown.up.prevent="navigatePrev()"
                @keydown.enter.prevent="selectCurrent()"
                @keydown.escape="open = false; $el.blur()"
                placeholder="Cari siswa, guru, jurusan, kelas, lowongan..." 
                autocomplete="off"
                spellcheck="false"
                class="w-full h-9 pl-9 pr-16 text-xs rounded-xl bg-slate-100/70 dark:bg-neutral-800/70 border border-slate-200/70 dark:border-white/10 text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-primary/50 focus:border-brand-primary transition-all shadow-inner"
            >
            
            <div class="absolute right-2.5 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                <template x-if="query.length > 0">
                    <button 
                        type="button" 
                        @click="clearSearch()"
                        aria-label="Hapus kata kunci"
                        class="w-4 h-4 rounded-full bg-slate-200/80 dark:bg-neutral-700 text-slate-500 hover:text-slate-800 dark:text-neutral-400 dark:hover:text-white flex items-center justify-center transition-colors text-[10px]"
                    >
                        <i data-lucide="x" class="w-2.5 h-2.5"></i>
                    </button>
                </template>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-medium text-slate-400 dark:text-neutral-500 bg-surface-card border border-slate-200/60 dark:border-white/10 shadow-xs pointer-events-none">
                    Ctrl K
                </span>
            </div>
        </div>

        <!-- Floating Results Dropdown -->
        <div 
            x-show="open" 
            x-cloak 
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
            class="absolute left-0 right-0 top-full mt-2 bg-surface-card border border-slate-200/90 dark:border-white/10 shadow-2xl rounded-2xl overflow-hidden z-50 font-sans"
        >
            <!-- Loading Indicator -->
            <template x-if="loading">
                <div class="p-6 text-center text-xs text-slate-400 flex items-center justify-center gap-2">
                    <div class="w-4 h-4 border-2 border-brand-primary border-t-transparent rounded-full animate-spin"></div>
                    <span>Mencari data...</span>
                </div>
            </template>

            <!-- Empty Results -->
            <template x-if="!loading && query.trim().length >= 2 && flatItems.length === 0">
                <div class="py-8 text-center px-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-neutral-800/80 text-slate-400 flex items-center justify-center mx-auto mb-2">
                        <i data-lucide="search-x" class="w-5 h-5"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                        Tidak ditemukan hasil untuk "<span x-text="query"></span>"
                    </p>
                    <p class="text-[11px] text-slate-400 dark:text-neutral-500 mt-1 max-w-xs mx-auto">
                        Coba kata kunci lain seperti nama siswa, guru, jurusan (PPLG, DKV), atau mitra industri.
                    </p>
                </div>
            </template>

            <!-- Grouped Results -->
            <template x-if="!loading && flatItems.length > 0">
                <div x-ref="resultsList" class="max-h-96 overflow-y-auto divide-y divide-slate-100 dark:divide-white/5 custom-scrollbar">
                    <template x-for="group in groups" :key="group.category">
                        <div class="py-1">
                            <div class="px-3.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-neutral-500 flex items-center justify-between">
                                <span x-text="group.category"></span>
                                <span class="text-[9px] font-normal lowercase text-slate-400 dark:text-neutral-600" x-text="group.items.length + ' hasil'"></span>
                            </div>

                            <div class="space-y-0.5 px-1.5">
                                <template x-for="item in group.items" :key="item.url + item.title">
                                    <div 
                                        @click="selectItem(item)"
                                        @mouseenter="selectedIndex = flatItems.indexOf(item)"
                                        :data-selected="flatItems[selectedIndex] === item ? 'true' : 'false'"
                                        class="w-full flex items-center justify-between p-2 rounded-xl text-left transition-colors cursor-pointer group"
                                        :class="flatItems[selectedIndex] === item ? 'bg-brand-primary/15 text-slate-900 dark:text-white' : 'hover:bg-slate-100/70 dark:hover:bg-neutral-800/60 text-slate-700 dark:text-slate-300'"
                                    >
                                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                            <div 
                                                class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors"
                                                :class="flatItems[selectedIndex] === item ? 'bg-brand-primary/30 text-brand-secondary dark:text-brand-primary font-bold' : 'bg-slate-100 dark:bg-neutral-800 text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-200'"
                                            >
                                                <i :data-lucide="item.icon || 'file-text'" class="w-3.5 h-3.5"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p 
                                                    class="text-xs font-semibold truncate leading-tight"
                                                    :class="flatItems[selectedIndex] === item ? 'text-slate-900 dark:text-white' : 'text-slate-800 dark:text-slate-200'"
                                                    x-text="item.title"
                                                ></p>
                                                <p class="text-[11px] text-slate-400 dark:text-neutral-500 truncate" x-text="item.desc"></p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1 pl-2 flex-shrink-0">
                                            <span 
                                                class="text-[10px] font-medium transition-opacity"
                                                :class="flatItems[selectedIndex] === item ? 'opacity-100 text-brand-secondary dark:text-brand-primary' : 'opacity-0 group-hover:opacity-100 text-slate-400'"
                                            >
                                                Buka &rarr;
                                            </span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Command Palette Keyboard Hints -->
            <div class="px-3.5 py-2 border-t border-slate-100 dark:border-white/5 bg-slate-50/70 dark:bg-neutral-900/40 flex items-center justify-between text-[11px] text-slate-400">
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 rounded bg-surface-card border border-slate-200/80 dark:border-white/10 text-[10px] font-mono shadow-2xs">&uarr;</kbd>
                        <kbd class="px-1.5 py-0.5 rounded bg-surface-card border border-slate-200/80 dark:border-white/10 text-[10px] font-mono shadow-2xs">&darr;</kbd>
                        <span class="text-[10px] text-slate-500 dark:text-neutral-400">navigasi</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 rounded bg-surface-card border border-slate-200/80 dark:border-white/10 text-[10px] font-mono shadow-2xs">&crarr;</kbd>
                        <span class="text-[10px] text-slate-500 dark:text-neutral-400">pilih</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 rounded bg-surface-card border border-slate-200/80 dark:border-white/10 text-[10px] font-mono shadow-2xs">ESC</kbd>
                        <span class="text-[10px] text-slate-500 dark:text-neutral-400">tutup</span>
                    </span>
                </div>
                <span class="text-[10px] text-slate-400 dark:text-neutral-500 hidden sm:inline">Pencarian Terpadu SMK</span>
            </div>
        </div>
    </div>

    <!-- Right Section: Status Pill, Notifications, Cohesive Theme Toggle & Profile -->
    <div class="flex items-center gap-2.5 sm:gap-3 flex-shrink-0">
        <!-- Academic Period Status Pill -->
        <div class="hidden xl:flex items-center gap-2 px-3 py-1 rounded-xl border border-slate-200/70 dark:border-white/10 bg-slate-50 dark:bg-neutral-800/50 text-xs text-slate-600 dark:text-slate-300 shadow-xs">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span class="font-medium text-slate-700 dark:text-slate-200">TA 2025/2026</span>
            <span class="text-slate-300 dark:text-neutral-600">&bull;</span>
            <span class="text-[11px] text-slate-400 dark:text-neutral-400">Ganjil</span>
        </div>

        <!-- Mobile Search Button & Dialog (Alpine.js) -->
        <div x-data="{ mobileSearchOpen: false }">
            <button 
                type="button" 
                @click="mobileSearchOpen = true" 
                aria-label="Pencarian Cepat"
                title="Pencarian Cepat"
                class="md:hidden w-9 h-9 rounded-xl border border-slate-200/80 dark:border-white/10 bg-surface-card text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-neutral-800 flex items-center justify-center transition-all shadow-sm"
            >
                <i data-lucide="search" class="w-4 h-4"></i>
            </button>

            <!-- Mobile Search Overlay Modal -->
            <div 
                x-show="mobileSearchOpen" 
                x-cloak 
                class="fixed inset-0 z-50 p-4 bg-black/60 backdrop-blur-xs flex flex-col items-center justify-start pt-16 md:hidden font-sans"
                @keydown.escape.window="mobileSearchOpen = false"
            >
                <div 
                    @click.outside="mobileSearchOpen = false"
                    class="w-full max-w-lg bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-2xl overflow-hidden flex flex-col"
                    x-data="{
                        mQuery: '',
                        mLoading: false,
                        mGroups: [],
                        async doMobileSearch() {
                            const q = this.mQuery.trim();
                            if (q.length < 2) {
                                this.mGroups = [];
                                this.mLoading = false;
                                return;
                            }
                            this.mLoading = true;
                            try {
                                const res = await fetch('{{ route('admin.search') }}?q=' + encodeURIComponent(q), {
                                    headers: { 'Accept': 'application/json' }
                                });
                                const data = await res.json();
                                this.mGroups = data.groups || [];
                            } catch (e) {
                                this.mGroups = [];
                            } finally {
                                this.mLoading = false;
                                this.$nextTick(() => {
                                    if (window.lucide && window.lucide.createIcons) window.lucide.createIcons();
                                });
                            }
                        }
                    }"
                >
                    <div class="p-3 border-b border-slate-100 dark:border-white/10 flex items-center gap-2">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 flex-shrink-0"></i>
                        <input 
                            type="text" 
                            x-model="mQuery"
                            @input.debounce.250ms="doMobileSearch()"
                            placeholder="Cari siswa, guru, jurusan, mitra..."
                            class="flex-1 text-xs bg-transparent border-none text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none"
                            autofocus
                        >
                        <button 
                            type="button" 
                            @click="mobileSearchOpen = false" 
                            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center text-xs"
                        >
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-white/5 custom-scrollbar p-2">
                        <template x-if="mLoading">
                            <div class="p-6 text-center text-xs text-slate-400 flex items-center justify-center gap-2">
                                <div class="w-4 h-4 border-2 border-brand-primary border-t-transparent rounded-full animate-spin"></div>
                                <span>Mencari data...</span>
                            </div>
                        </template>

                        <template x-if="!mLoading && mQuery.trim().length >= 2 && mGroups.length === 0">
                            <div class="py-6 text-center px-4">
                                <p class="text-xs text-slate-500">Tidak ada hasil ditemukan</p>
                            </div>
                        </template>

                        <template x-for="grp in mGroups" :key="grp.category">
                            <div class="py-1">
                                <div class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400" x-text="grp.category"></div>
                                <template x-for="itm in grp.items" :key="itm.url + itm.title">
                                    <a 
                                        :href="itm.url"
                                        class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors"
                                    >
                                        <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-neutral-800 text-slate-500 flex items-center justify-center flex-shrink-0">
                                            <i :data-lucide="itm.icon || 'file-text'" class="w-3.5 h-3.5"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate" x-text="itm.title"></p>
                                            <p class="text-[11px] text-slate-400 truncate" x-text="itm.desc"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive Notification Bell Dropdown (Alpine.js) -->
        <div 
            x-data="{
                open: false,
                loading: false,
                unreadCount: {{ \App\Models\Notification::where('user_id', auth()->id())->where('dibaca', false)->count() }},
                notifications: [],
                fetchNotifications() {
                    if (this.notifications.length === 0) {
                        this.loading = true;
                        fetch('{{ route('admin.notifications.index') }}', {
                            headers: { 'Accept': 'application/json' }
                        })
                        .then(res => res.json())
                        .then(data => {
                            this.notifications = data.notifications || [];
                            this.unreadCount = data.unread_count ?? 0;
                            this.loading = false;
                            this.$nextTick(() => { if (window.lucide && window.lucide.createIcons) window.lucide.createIcons(); });
                        })
                        .catch(() => { this.loading = false; });
                    }
                },
                markAllRead() {
                    fetch('{{ route('admin.notifications.markAllRead') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.unreadCount = 0;
                        this.notifications.forEach(n => n.dibaca = true);
                    });
                },
                markOneRead(notif) {
                    if (!notif.dibaca) {
                        notif.dibaca = true;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                        fetch('{{ url('/admin/notifications') }}/' + notif.id + '/read', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                    }
                    if (notif.url) {
                        window.location.href = notif.url;
                    }
                }
            }" 
            @click.outside="open = false" 
            class="relative"
        >
            <button 
                type="button" 
                @click="open = !open; if(open) fetchNotifications()" 
                aria-label="Pusat Notifikasi" 
                title="Pusat Notifikasi"
                class="relative w-9 h-9 rounded-xl border border-slate-200/80 dark:border-white/10 bg-surface-card text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-neutral-800 flex items-center justify-center transition-all shadow-sm focus:outline-none"
                :class="{ 'ring-2 ring-brand-primary/50 border-brand-primary': open }"
            >
                <i data-lucide="bell" class="w-4 h-4"></i>
                <template x-if="unreadCount > 0">
                    <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-brand-primary ring-2 ring-surface-card"></span>
                    </span>
                </template>
            </button>

            <!-- Dropdown Popover Window -->
            <div 
                x-show="open" 
                x-cloak 
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-surface-card border border-slate-200/90 dark:border-white/10 shadow-2xl z-50 overflow-hidden font-sans"
            >
                <!-- Popover Header -->
                <div class="px-4 py-3.5 border-b border-slate-100 dark:border-white/5 flex items-center justify-between bg-slate-50/50 dark:bg-neutral-900/40">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white">Pusat Notifikasi</span>
                        <template x-if="unreadCount > 0">
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-primary text-neutral-900" x-text="unreadCount + ' Baru'"></span>
                        </template>
                    </div>

                    <template x-if="unreadCount > 0">
                        <button 
                            type="button" 
                            @click="markAllRead()"
                            class="text-[11px] font-medium text-brand-secondary dark:text-brand-primary hover:underline"
                        >
                            Tandai dibaca
                        </button>
                    </template>
                </div>

                <!-- Notifications List Body -->
                <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-white/5 custom-scrollbar">
                    <template x-if="loading">
                        <div class="p-6 text-center text-xs text-slate-400">
                            Memuat notifikasi...
                        </div>
                    </template>

                    <template x-if="!loading && notifications.length === 0">
                        <div class="py-8 text-center px-4">
                            <i data-lucide="bell-off" class="w-7 h-7 text-slate-300 dark:text-neutral-600 mx-auto mb-2"></i>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Tidak ada notifikasi saat ini</p>
                        </div>
                    </template>

                    <template x-for="notif in notifications" :key="notif.id">
                        <div 
                            @click="markOneRead(notif)"
                            class="p-3.5 hover:bg-slate-50/80 dark:hover:bg-neutral-800/50 cursor-pointer transition-colors flex items-start gap-3"
                            :class="!notif.dibaca ? 'bg-brand-primary/5' : ''"
                        >
                            <div 
                                class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5 text-xs font-semibold"
                                :class="!notif.dibaca ? 'bg-brand-primary/20 text-brand-secondary dark:text-brand-primary' : 'bg-slate-100 dark:bg-neutral-800 text-slate-400'"
                            >
                                <i :data-lucide="notif.icon || 'bell'" class="w-4 h-4"></i>
                            </div>

                            <div class="flex-1 min-w-0 space-y-0.5">
                                <p 
                                    class="text-xs font-medium text-slate-800 dark:text-slate-200 leading-snug line-clamp-2"
                                    :class="!notif.dibaca ? 'font-semibold text-slate-900 dark:text-white' : ''"
                                    x-text="notif.judul"
                                ></p>
                                <span class="text-[10px] text-slate-400 dark:text-neutral-500 block" x-text="notif.created_at"></span>
                            </div>

                            <template x-if="!notif.dibaca">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-primary flex-shrink-0 mt-1.5"></span>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Popover Footer -->
                <div class="p-2 border-t border-slate-100 dark:border-white/5 bg-slate-50/30 dark:bg-neutral-900/20 text-center">
                    <a 
                        href="{{ route('admin.notifications.index') }}" 
                        class="text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-brand-secondary dark:hover:text-brand-primary block py-1 transition-colors"
                    >
                        Lihat Seluruh Notifikasi &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Cohesive Dark / Light Mode Toggle Button -->
        <button 
            type="button" 
            onclick="toggleTheme()" 
            aria-label="Ganti Tema Tampilan" 
            title="Ganti Tema (Gelap/Terang)"
            class="w-9 h-9 rounded-xl border border-slate-200/80 dark:border-white/10 bg-surface-card text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-neutral-800 flex items-center justify-center transition-all shadow-sm"
        >
            <i data-lucide="sun" class="w-4 h-4 hidden dark:block text-slate-200"></i>
            <i data-lucide="moon" class="w-4 h-4 block dark:hidden text-slate-600"></i>
        </button>

        <!-- Vertical Divider -->
        <div class="h-6 w-px bg-slate-200 dark:bg-white/10 mx-0.5"></div>

        <!-- User Profile Dropdown (Alpine.js) -->
        <div x-data="{ open: false }" @click.outside="open = false" class="relative">
            <button 
                type="button" 
                @click="open = !open" 
                class="flex items-center gap-2.5 p-1 sm:pr-2.5 rounded-xl hover:bg-slate-100/80 dark:hover:bg-neutral-800/80 transition-colors focus:outline-none"
                aria-expanded="false"
                aria-haspopup="true"
            >
                <!-- Avatar Initial -->
                <div class="w-8 h-8 rounded-lg bg-brand-secondary text-white font-semibold text-xs flex items-center justify-center shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                </div>

                <!-- Name & Role -->
                <div class="hidden sm:block text-left">
                    <span class="block text-xs font-semibold text-slate-900 dark:text-white leading-tight truncate max-w-[130px]">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </span>
                    <span class="block text-[10px] text-slate-500 dark:text-slate-400 capitalize">
                        {{ auth()->user()->role?->label ?? 'Administrator' }}
                    </span>
                </div>

                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': open }"></i>
            </button>

            <!-- Dropdown Menu Card -->
            <div 
                x-show="open" 
                x-cloak 
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                class="absolute right-0 mt-2 w-52 rounded-2xl bg-surface-card border border-slate-200/80 dark:border-white/10 shadow-xl py-1.5 z-50 font-sans"
            >
                <div class="px-4 py-2 border-b border-slate-100 dark:border-white/5">
                    <p class="text-xs font-semibold text-slate-900 dark:text-white truncate">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                        {{ auth()->user()->email ?? 'admin@smkn1.sch.id' }}
                    </p>
                </div>

                <div class="py-1">
                    <a href="{{ route('landing') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-neutral-800/80 transition-colors">
                        <i data-lucide="globe" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Lihat Portal Publik</span>
                    </a>
                </div>

                <div class="border-t border-slate-100 dark:border-white/5 pt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition-colors">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                            <span class="font-medium">Keluar dari Akun</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
