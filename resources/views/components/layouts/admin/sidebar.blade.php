@php
    $currentRoute = Route::currentRouteName() ?? '';
    $pendingCompaniesCount = \App\Models\Company::where('status_verifikasi', 'menunggu')->count();

    $navSections = [
        [
            'category' => 'Menu Utama',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'admin.dashboard',
                    'icon' => 'layout-dashboard',
                    'badge' => null,
                ],
            ],
        ],
        [
            'category' => 'Data Master',
            'items' => [
                [
                    'label' => 'Data Guru',
                    'route' => 'admin.teachers.index',
                    'icon' => 'user-check',
                    'badge' => null,
                ],
                [
                    'label' => 'Data Siswa',
                    'route' => 'admin.students.index',
                    'icon' => 'graduation-cap',
                    'badge' => null,
                ],
                [
                    'label' => 'Data Jurusan',
                    'route' => 'admin.majors.index',
                    'icon' => 'book-open',
                    'badge' => null,
                ],
                [
                    'label' => 'Data Kelas',
                    'route' => 'admin.classes.index',
                    'icon' => 'school',
                    'badge' => null,
                ],
                [
                    'label' => 'Master Keahlian',
                    'route' => 'admin.skills.index',
                    'icon' => 'award',
                    'badge' => null,
                ],
            ],
        ],
        [
            'category' => 'Kemitraan Industri',
            'items' => [
                [
                    'label' => 'Mitra Industri',
                    'route' => 'admin.companies.index',
                    'icon' => 'building-2',
                    'badge' => null,
                ],
                [
                    'label' => 'Verifikasi Mitra',
                    'route' => 'admin.companies.verification',
                    'icon' => 'shield-check',
                    'badge' => $pendingCompaniesCount > 0 ? (string)$pendingCompaniesCount : null,
                ],
                [
                    'label' => 'Lowongan Kerja',
                    'route' => 'admin.jobs.index',
                    'icon' => 'briefcase',
                    'badge' => null,
                ],
            ],
        ],
        [
            'category' => 'Informasi BKK & Komunitas',
            'items' => [
                [
                    'label' => 'Pengumuman BKK',
                    'route' => 'admin.announcements.index',
                    'icon' => 'megaphone',
                    'badge' => null,
                ],
                [
                    'label' => 'Agenda Kegiatan',
                    'route' => 'admin.agendas.index',
                    'icon' => 'calendar',
                    'badge' => null,
                ],
                [
                    'label' => 'Feed Komunitas Vokasi',
                    'route' => 'feed.index',
                    'icon' => 'message-square',
                    'badge' => null,
                ],
            ],
        ],
        [
            'category' => 'Sistem & Portal',
            'items' => [
                [
                    'label' => 'Pengaturan Sistem',
                    'route' => 'admin.settings',
                    'icon' => 'settings',
                    'badge' => null,
                ],
                [
                    'label' => 'Lihat Portal Publik',
                    'route' => 'landing',
                    'icon' => 'external-link',
                    'badge' => null,
                    'external' => true,
                ],
            ],
        ],
    ];
@endphp

<!-- Mobile Backdrop -->
<div 
    x-show="sidebarOpen" 
    x-cloak 
    @click="sidebarOpen = false" 
    class="fixed inset-0 z-40 bg-neutral-900/60 backdrop-blur-sm lg:hidden transition-opacity"
    x-transition:enter="ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
></div>

<!-- Dual-Mode Sidebar Container -->
<aside 
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full lg:translate-x-0': !sidebarOpen,
        'lg:w-64': !sidebarCollapsed,
        'lg:w-20': sidebarCollapsed
    }"
    class="fixed inset-y-0 left-0 z-50 flex flex-col bg-surface-card border-r border-slate-200/80 dark:border-white/10 transition-all duration-300 ease-in-out select-none shadow-sm"
>
    <!-- Sidebar Header & Branding -->
    <div class="h-16 flex items-center px-4 border-b border-slate-100 dark:border-white/5 transition-all duration-300"
         :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
        
        <!-- Logo & Title -->
        <a href="{{ route('admin.dashboard') ?? url('/admin') }}" class="flex items-center gap-3 overflow-hidden">
            <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-neutral-800 p-1.5 flex items-center justify-center border border-slate-200/60 dark:border-white/10 flex-shrink-0">
                <img src="{{ asset('images/logos/logo-career-school.png') }}" alt="Career School Logo" class="w-full h-full object-contain">
            </div>
            
            <div x-show="!sidebarCollapsed" x-cloak class="transition-opacity duration-200 whitespace-nowrap">
                <span class="block text-sm font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                    Career School
                </span>
                <span class="block text-[11px] font-medium text-brand-secondary dark:text-brand-primary">
                    SMKN 1 Cirebon
                </span>
            </div>
        </a>

        <!-- Collapse Toggle Button (Desktop Only) -->
        <button 
            type="button" 
            x-show="!sidebarCollapsed" 
            x-cloak
            @click="toggleSidebar()" 
            aria-label="Ciutkan Sidebar"
            class="hidden lg:flex w-7 h-7 rounded-lg border border-slate-200/80 dark:border-white/10 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-neutral-800 items-center justify-center transition-colors"
        >
            <i data-lucide="chevrons-left" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- Sidebar Navigation Scrollable Body -->
    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6 custom-scrollbar">
        @foreach ($navSections as $section)
            <div>
                <!-- Category Heading -->
                <div x-show="!sidebarCollapsed" x-cloak class="px-3 mb-2">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-neutral-500 font-sans">
                        {{ $section['category'] }}
                    </p>
                </div>
                <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-slate-100 dark:border-white/5"></div>

                <!-- Nav Items -->
                <nav class="space-y-1">
                    @foreach ($section['items'] as $item)
                        @php
                            $hasRoute = Route::has($item['route']);
                            $isExact = $currentRoute === $item['route'];
                            $isResourceSub = str_ends_with($item['route'], '.index') 
                                && str_starts_with($currentRoute, substr($item['route'], 0, -6) . '.')
                                && $currentRoute !== 'admin.companies.verification';
                            $isActive = $hasRoute && ($isExact || $isResourceSub);
                            $url = $hasRoute ? route($item['route']) : '#';
                        @endphp

                        <a 
                            href="{{ $url }}" 
                            title="{{ $item['label'] }}"
                            @if (!empty($item['external'])) target="_blank" rel="noopener noreferrer" @endif
                            :class="{
                                'justify-center px-0': sidebarCollapsed,
                                'px-3': !sidebarCollapsed,
                                'bg-brand-primary text-neutral-900 font-semibold shadow-sm': {{ $isActive ? 'true' : 'false' }},
                                'text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-neutral-800/80 hover:text-slate-900 dark:hover:text-white font-medium': !{{ $isActive ? 'true' : 'false' }}
                            }"
                            class="relative flex items-center h-10 rounded-xl transition-all duration-150 group text-sm"
                        >
                            <!-- Nav Icon -->
                            <div class="flex items-center justify-center flex-shrink-0" :class="sidebarCollapsed ? 'w-full' : 'w-5 mr-3'">
                                <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 transition-transform group-hover:scale-105"></i>
                            </div>

                            <!-- Nav Label & Badge (Expanded) -->
                            <div x-show="!sidebarCollapsed" x-cloak class="flex-1 flex items-center justify-between overflow-hidden whitespace-nowrap">
                                <span class="truncate">
                                    {{ $item['label'] }}
                                </span>

                                @if (!empty($item['badge']))
                                    <span class="ml-2 px-2 py-0.5 text-[11px] font-bold rounded-full {{ $isActive ? 'bg-neutral-900 text-white' : 'bg-brand-secondary text-white dark:bg-brand-primary dark:text-neutral-900' }}">
                                        {{ $item['badge'] }}
                                    </span>
                                @endif
                            </div>

                            <!-- Collapsed Notification Dot -->
                            @if (!empty($item['badge']))
                                <span 
                                    x-show="sidebarCollapsed" 
                                    x-cloak 
                                    class="absolute top-2 right-2 w-2 h-2 rounded-full bg-brand-secondary dark:bg-brand-primary ring-2 ring-surface-card"
                                ></span>
                            @endif
                        </a>
                    @endforeach
                </nav>
            </div>
        @endforeach
    </div>

    <!-- Sidebar Bottom Footer: Logout & Version/Expand -->
    <div class="p-3 border-t border-slate-100 dark:border-white/5 space-y-1.5">
        <!-- Logout Form & Button -->
        <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <button 
                type="button" 
                onclick="confirmSidebarLogout()"
                title="Keluar Akun"
                aria-label="Keluar Akun"
                :class="{
                    'justify-center px-0': sidebarCollapsed,
                    'px-3': !sidebarCollapsed
                }"
                class="w-full h-10 rounded-xl text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 flex items-center transition-all duration-150 group text-sm font-medium border border-transparent hover:border-rose-200 dark:hover:border-rose-900/40"
            >
                <div class="flex items-center justify-center flex-shrink-0" :class="sidebarCollapsed ? 'w-full' : 'w-5 mr-3'">
                    <i data-lucide="log-out" class="w-4 h-4 transition-transform group-hover:scale-105"></i>
                </div>
                <span x-show="!sidebarCollapsed" x-cloak class="truncate">
                    Keluar Akun
                </span>
            </button>
        </form>

        <!-- Expand Button for Collapsed Mode -->
        <button 
            type="button" 
            x-show="sidebarCollapsed" 
            x-cloak
            @click="toggleSidebar()" 
            aria-label="Perluas Sidebar"
            class="hidden lg:flex w-full h-10 rounded-xl border border-slate-200/80 dark:border-white/10 text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-neutral-800 items-center justify-center transition-colors"
        >
            <i data-lucide="chevrons-right" class="w-4 h-4"></i>
        </button>

        <div x-show="!sidebarCollapsed" x-cloak class="text-center pt-1">
            <p class="text-[11px] text-slate-400 dark:text-neutral-500">
                Career School v1.0 &copy; {{ date('Y') }}
            </p>
        </div>
    </div>
</aside>
