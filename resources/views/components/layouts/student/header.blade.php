@props([
    'breadcrumbs' => [],
])

@php
    $user = auth()->user();
    $student = $user?->student;
@endphp

<header class="h-16 bg-surface-card/95 backdrop-blur-md border-b border-border-subtle sticky top-0 z-30 flex items-center justify-between px-4 sm:px-6 transition-all duration-150">
    <!-- Left Section: Mobile Toggle & Breadcrumbs -->
    <div class="flex items-center gap-3 sm:gap-4 overflow-hidden flex-shrink-0">
        <!-- Mobile Sidebar Hamburger -->
        <button 
            type="button" 
            @click="sidebarOpen = !sidebarOpen" 
            aria-label="Buka menu navigasi"
            class="lg:hidden w-9 h-9 rounded-btn border border-border-subtle text-text-secondary hover:bg-surface-bg flex items-center justify-center transition-all active:scale-[0.96]"
        >
            <i data-lucide="menu" class="w-4 h-4"></i>
        </button>

        <!-- Desktop Mini Toggle -->
        <button 
            type="button" 
            @click="toggleSidebar()" 
            aria-label="Ubah lebar bilah sisi"
            class="hidden lg:flex w-8 h-8 rounded-btn text-text-secondary hover:text-text-primary hover:bg-surface-bg items-center justify-center transition-all active:scale-[0.96]"
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
                    const res = await fetch('{{ route('student.search') }}?q=' + encodeURIComponent(q), {
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
                    window.location.href = '{{ route('student.jobs.index') }}?q=' + encodeURIComponent(this.query.trim());
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
            <i data-lucide="search" class="w-4 h-4 text-text-secondary absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
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
                placeholder="Cari lowongan, keahlian, atau mitra DUDI..." 
                autocomplete="off"
                spellcheck="false"
                class="w-full h-9 pl-9 pr-16 text-xs rounded-xl bg-surface-bg border border-border-default/70 text-text-primary placeholder:text-text-secondary focus:outline-none focus:ring-2 focus:ring-brand-primary/50 focus:border-brand-primary transition-all shadow-inner"
            >
            
            <div class="absolute right-2.5 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                <template x-if="query.length > 0">
                    <button 
                        type="button" 
                        @click="clearSearch()"
                        aria-label="Hapus kata kunci"
                        class="w-4 h-4 rounded-full bg-surface-card border border-border-subtle text-text-secondary hover:text-text-primary flex items-center justify-center transition-colors text-[10px]"
                    >
                        <i data-lucide="x" class="w-2.5 h-2.5"></i>
                    </button>
                </template>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-medium text-text-secondary bg-surface-card border border-border-subtle shadow-xs pointer-events-none">
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
            class="absolute left-0 right-0 top-full mt-2 bg-surface-card border border-border-subtle shadow-2xl rounded-2xl overflow-hidden z-50 font-sans"
        >
            <!-- Loading Indicator -->
            <template x-if="loading">
                <div class="p-6 text-center text-xs text-text-secondary flex items-center justify-center gap-2">
                    <div class="w-4 h-4 border-2 border-brand-primary border-t-transparent rounded-full animate-spin"></div>
                    <span>Mencari data portal siswa...</span>
                </div>
            </template>

            <!-- Empty Results -->
            <template x-if="!loading && query.trim().length >= 2 && flatItems.length === 0">
                <div class="py-8 text-center px-4">
                    <div class="w-10 h-10 rounded-xl bg-surface-bg text-text-secondary flex items-center justify-center mx-auto mb-2">
                        <i data-lucide="search-x" class="w-5 h-5"></i>
                    </div>
                    <p class="text-xs font-semibold text-text-primary">
                        Tidak ditemukan hasil untuk "<span x-text="query"></span>"
                    </p>
                    <p class="text-[11px] text-text-secondary mt-1 max-w-xs mx-auto">
                        Coba kata kunci lain seperti nama posisi lowongan, mitra industri, atau standar keahlian.
                    </p>
                </div>
            </template>

            <!-- Grouped Results -->
            <template x-if="!loading && flatItems.length > 0">
                <div x-ref="resultsList" class="max-h-96 overflow-y-auto divide-y divide-border-subtle custom-scrollbar">
                    <template x-for="group in groups" :key="group.category">
                        <div class="py-1">
                            <div class="px-3.5 py-1 text-[10px] font-bold uppercase tracking-wider text-text-secondary/70 flex items-center justify-between">
                                <span x-text="group.category"></span>
                                <span class="text-[9px] font-normal lowercase text-text-secondary" x-text="group.items.length + ' hasil'"></span>
                            </div>

                            <div class="space-y-0.5 px-1.5">
                                <template x-for="item in group.items" :key="item.url + item.title">
                                    <div 
                                        @click="selectItem(item)"
                                        @mouseenter="selectedIndex = flatItems.indexOf(item)"
                                        :data-selected="flatItems[selectedIndex] === item ? 'true' : 'false'"
                                        class="w-full flex items-center justify-between p-2 rounded-xl text-left transition-colors cursor-pointer group"
                                        :class="flatItems[selectedIndex] === item ? 'bg-brand-primary/15 text-text-primary' : 'hover:bg-surface-bg text-text-secondary hover:text-text-primary'"
                                    >
                                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                            <div 
                                                class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors"
                                                :class="flatItems[selectedIndex] === item ? 'bg-brand-primary/30 text-brand-secondary dark:text-brand-primary font-bold' : 'bg-surface-bg text-text-secondary group-hover:text-text-primary'"
                                            >
                                                <i :data-lucide="item.icon || 'file-text'" class="w-3.5 h-3.5"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p 
                                                    class="text-xs font-semibold truncate leading-tight"
                                                    :class="flatItems[selectedIndex] === item ? 'text-text-primary' : 'text-text-primary'"
                                                    x-text="item.title"
                                                ></p>
                                                <p class="text-[11px] text-text-secondary truncate" x-text="item.desc"></p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1 pl-2 flex-shrink-0">
                                            <span 
                                                class="text-[10px] font-medium transition-opacity"
                                                :class="flatItems[selectedIndex] === item ? 'opacity-100 text-brand-secondary dark:text-brand-primary' : 'opacity-0 group-hover:opacity-100 text-text-secondary'"
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
            <div class="px-3.5 py-2 border-t border-border-subtle bg-surface-bg/50 flex items-center justify-between text-[11px] text-text-secondary">
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 rounded bg-surface-card border border-border-subtle text-[10px] font-mono shadow-2xs">&uarr;</kbd>
                        <kbd class="px-1.5 py-0.5 rounded bg-surface-card border border-border-subtle text-[10px] font-mono shadow-2xs">&darr;</kbd>
                        <span class="text-[10px] text-text-secondary">navigasi</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 rounded bg-surface-card border border-border-subtle text-[10px] font-mono shadow-2xs">&crarr;</kbd>
                        <span class="text-[10px] text-text-secondary">pilih</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 rounded bg-surface-card border border-border-subtle text-[10px] font-mono shadow-2xs">ESC</kbd>
                        <span class="text-[10px] text-text-secondary">tutup</span>
                    </span>
                </div>
                <span class="text-[10px] text-text-secondary hidden sm:inline">Pencarian Siswa SMKN 1</span>
            </div>
        </div>
    </div>

    <!-- Right Section: Actions & Profile Menu -->
    <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
        <!-- Academic Period Status Pill -->
        <div class="hidden xl:flex items-center gap-2 px-3 py-1 rounded-xl border border-border-subtle bg-surface-bg text-xs text-text-secondary shadow-xs">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span class="font-medium text-text-primary">TA 2025/2026</span>
            <span class="text-text-muted">&bull;</span>
            <span class="text-[11px] text-text-secondary">Ganjil</span>
        </div>

        <!-- Mobile Search Button & Dialog (Alpine.js) -->
        <div x-data="{ mobileSearchOpen: false }">
            <button 
                type="button" 
                @click="mobileSearchOpen = true" 
                aria-label="Pencarian Cepat"
                title="Pencarian Cepat"
                class="md:hidden w-9 h-9 rounded-xl border border-border-subtle bg-surface-card text-text-secondary hover:text-text-primary hover:bg-surface-bg flex items-center justify-center transition-all shadow-sm"
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
                    class="w-full max-w-lg bg-surface-card border border-border-subtle rounded-2xl shadow-2xl overflow-hidden flex flex-col"
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
                                const res = await fetch('{{ route('student.search') }}?q=' + encodeURIComponent(q), {
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
                    <div class="p-3 border-b border-border-subtle flex items-center gap-2">
                        <i data-lucide="search" class="w-4 h-4 text-text-secondary flex-shrink-0"></i>
                        <input 
                            type="text" 
                            x-model="mQuery"
                            @input.debounce.250ms="doMobileSearch()"
                            placeholder="Cari lowongan, keahlian, mitra DUDI..."
                            class="flex-1 text-xs bg-transparent border-none text-text-primary placeholder:text-text-secondary focus:outline-none"
                            autofocus
                        >
                        <button 
                            type="button" 
                            @click="mobileSearchOpen = false" 
                            class="w-7 h-7 rounded-lg text-text-secondary hover:text-text-primary flex items-center justify-center text-xs"
                        >
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <div class="max-h-80 overflow-y-auto divide-y divide-border-subtle custom-scrollbar p-2">
                        <template x-if="mLoading">
                            <div class="p-6 text-center text-xs text-text-secondary flex items-center justify-center gap-2">
                                <div class="w-4 h-4 border-2 border-brand-primary border-t-transparent rounded-full animate-spin"></div>
                                <span>Mencari data...</span>
                            </div>
                        </template>

                        <template x-if="!mLoading && mQuery.trim().length >= 2 && mGroups.length === 0">
                            <div class="py-6 text-center px-4">
                                <p class="text-xs text-text-secondary">Tidak ada hasil ditemukan.</p>
                            </div>
                        </template>

                        <template x-for="group in mGroups" :key="'m-' + group.category">
                            <div class="py-1">
                                <div class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-text-secondary/70" x-text="group.category"></div>
                                <template x-for="item in group.items" :key="'m-item-' + item.url + item.title">
                                    <a :href="item.url" class="flex items-center gap-2 p-2 rounded-xl hover:bg-surface-bg text-xs">
                                        <i :data-lucide="item.icon || 'file-text'" class="w-4 h-4 text-text-secondary"></i>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-text-primary truncate" x-text="item.title"></p>
                                            <p class="text-[10px] text-text-secondary truncate" x-text="item.desc"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dark Mode Toggle Button -->
        <button 
            type="button" 
            onclick="window.toggleTheme()" 
            aria-label="Ganti tema tampilan"
            class="w-9 h-9 rounded-btn text-text-secondary hover:text-text-primary hover:bg-surface-bg border border-border-subtle flex items-center justify-center transition-all active:scale-[0.96]"
        >
            <i data-lucide="sun" class="w-4 h-4 hidden dark:block text-amber-400"></i>
            <i data-lucide="moon" class="w-4 h-4 block dark:hidden text-text-primary"></i>
        </button>

        <!-- Notification Bell -->
        <x-notifications.dropdown />

        <!-- Profile Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button 
                type="button" 
                @click="open = !open" 
                @click.outside="open = false" 
                class="flex items-center gap-2 p-1.5 rounded-btn hover:bg-surface-bg transition-colors"
            >
                <div class="w-8 h-8 rounded-full bg-brand-primary text-text-on-brand-primary font-bold font-heading text-xs flex items-center justify-center shadow-xs">
                    {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                </div>
                <div class="hidden sm:block text-left pr-1">
                    <span class="block text-xs font-semibold text-text-primary leading-tight">
                        {{ Str::limit($user->name ?? 'Siswa', 15) }}
                    </span>
                    <span class="block text-[10px] text-brand-secondary dark:text-brand-primary font-medium">
                        {{ $student?->schoolClass?->nama ?? 'PPLG' }}
                    </span>
                </div>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-text-secondary transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>

            <!-- Dropdown Menu -->
            <div 
                x-show="open" 
                x-transition:enter="transition ease-out duration-100" 
                x-transition:enter-start="transform opacity-0 scale-95" 
                x-transition:enter-end="transform opacity-100 scale-100" 
                x-transition:leave="transition ease-in duration-75" 
                x-transition:leave-start="transform opacity-100 scale-100" 
                x-transition:leave-end="transform opacity-0 scale-95" 
                class="absolute right-0 mt-2 w-56 bg-surface-card border border-border-subtle rounded-card shadow-xl py-2 z-50 divide-y divide-border-subtle"
                style="display: none;"
            >
                <div class="px-4 py-2.5">
                    <p class="text-xs font-semibold text-text-primary truncate">{{ $user->name }}</p>
                    <p class="text-[11px] text-text-secondary truncate mt-0.5">NISN: {{ $student?->nisn ?? '-' }}</p>
                    <p class="text-[10px] text-state-success-text font-medium mt-1">
                        Siswa aktif angkatan {{ $student?->angkatan ?? '2024' }}
                    </p>
                </div>

                <div class="py-1.5">
                    <a href="{{ route('student.profile') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-text-primary hover:bg-surface-bg transition-colors">
                        <i data-lucide="user-check" class="w-4 h-4 text-text-secondary"></i>
                        <span>Profil & keahlian saya</span>
                    </a>
                    <a href="{{ route('student.portfolios.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-text-primary hover:bg-surface-bg transition-colors">
                        <i data-lucide="folder-kanban" class="w-4 h-4 text-text-secondary"></i>
                        <span>Portofolio karya</span>
                    </a>
                    <a href="{{ route('student.applications.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-text-primary hover:bg-surface-bg transition-colors">
                        <i data-lucide="send" class="w-4 h-4 text-text-secondary"></i>
                        <span>Lamaran terkirim</span>
                    </a>
                </div>

                <div class="py-1.5">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-state-danger-text hover:bg-state-danger-bg/20 text-left font-medium transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span>Keluar dari akun</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
