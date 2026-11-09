@php
    $currentRoute = Route::currentRouteName() ?? '';
    $user = auth()->user();
    $student = $user?->student;
    
    $activeAppsCount = 0;
    if ($student) {
        $activeAppsCount = \App\Models\Application::where('student_id', $student->id)
            ->whereIn('status', ['tertunda', 'ditinjau', 'wawancara'])
            ->count();
    }

    $navSections = [
        [
            'category' => 'Menu utama',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'student.dashboard',
                    'icon' => 'layout-dashboard',
                    'badge' => null,
                ],
            ],
        ],
        [
            'category' => 'Peluang & karier',
            'items' => [
                [
                    'label' => 'Eksplorasi lowongan',
                    'route' => 'student.jobs.index',
                    'icon' => 'briefcase',
                    'badge' => null,
                ],
                [
                    'label' => 'Lamaran saya',
                    'route' => 'student.applications.index',
                    'icon' => 'send',
                    'badge' => $activeAppsCount > 0 ? (string)$activeAppsCount : null,
                ],
                [
                    'label' => 'Nilai & Sertifikat PKL',
                    'route' => 'student.evaluations.index',
                    'icon' => 'award',
                    'badge' => null,
                ],
            ],
        ],
        [
            'category' => 'Portofolio & keahlian',
            'items' => [
                [
                    'label' => 'Karya & portofolio',
                    'route' => 'student.portfolios.index',
                    'icon' => 'folder-kanban',
                    'badge' => null,
                ],
                [
                    'label' => 'Keahlian & profil',
                    'route' => 'student.profile',
                    'icon' => 'user-check',
                    'badge' => null,
                ],
            ],
        ],
        [
            'category' => 'Jejaring & komunitas',
            'items' => [
                [
                    'label' => 'Feed komunitas',
                    'route' => 'feed.index',
                    'icon' => 'message-square',
                    'badge' => null,
                ],
                [
                    'label' => 'Kiriman tersimpan',
                    'route' => 'bookmarks.index',
                    'icon' => 'bookmark',
                    'badge' => null,
                ],
                [
                    'label' => 'Tracer study alumni',
                    'route' => 'tracer.create',
                    'icon' => 'graduation-cap',
                    'badge' => null,
                ],
            ],
        ],
        [
            'category' => 'Lainnya',
            'items' => [
                [
                    'label' => 'Lihat portal publik',
                    'route' => 'landing',
                    'icon' => 'external-link',
                    'badge' => null,
                    'external' => true,
                ],
            ],
        ],
    ];
@endphp

<!-- Mobile Backdrop Drawer -->
<div 
    x-show="sidebarOpen" 
    x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-neutral-950/60 backdrop-blur-sm z-40 lg:hidden"
    style="display: none;"
></div>

<!-- Sidebar Container -->
<aside 
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full lg:translate-x-0': !sidebarOpen,
        'lg:w-64': !sidebarCollapsed,
        'lg:w-20': sidebarCollapsed
    }"
    class="fixed inset-y-0 left-0 z-50 flex flex-col bg-surface-card border-r border-border-subtle transition-all duration-300 ease-in-out select-none shadow-xs"
>
    <!-- Brand Header -->
    <div class="h-16 flex items-center px-4 border-b border-border-subtle transition-all duration-300 flex-shrink-0"
         :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
        <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 overflow-hidden group">
            <img src="{{ asset('images/logos/logo-career-school.png') }}" alt="Career School Logo" class="w-8 h-8 object-contain flex-shrink-0 group-hover:scale-105 transition-transform">
            <div x-show="!sidebarCollapsed" x-cloak class="flex flex-col min-w-0 transition-opacity duration-200 whitespace-nowrap">
                <span class="font-bold text-sm text-text-primary truncate font-heading tracking-tight leading-snug">
                    Portal Siswa
                </span>
                <span class="text-[10px] font-semibold uppercase tracking-wider text-brand-secondary dark:text-brand-primary truncate">
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
            aria-label="Ciutkan bilah sisi"
            class="hidden lg:flex w-7 h-7 rounded-btn border border-border-subtle text-text-secondary hover:text-text-primary hover:bg-surface-bg items-center justify-center transition-all active:scale-[0.96]"
        >
            <i data-lucide="chevrons-left" class="w-4 h-4"></i>
        </button>

        <!-- Mobile Close Button -->
        <button 
            type="button" 
            @click="sidebarOpen = false" 
            aria-label="Tutup menu navigasi"
            class="lg:hidden w-8 h-8 rounded-btn text-text-secondary hover:text-text-primary hover:bg-surface-bg flex items-center justify-center transition-colors"
        >
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- Navigation Menu Items -->
    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6 custom-scrollbar">
        @foreach ($navSections as $section)
            <div>
                <!-- Section Header (Only if expanded) -->
                <div x-show="!sidebarCollapsed" x-cloak class="px-3 mb-2">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-text-secondary/70 font-sans">
                        {{ $section['category'] }}
                    </p>
                </div>
                <div x-show="sidebarCollapsed" x-cloak class="my-2 border-t border-border-subtle"></div>

                <nav class="space-y-1">
                    @foreach ($section['items'] as $item)
                        @php
                            $hasRoute = Route::has($item['route']);
                            $isExact = $currentRoute === $item['route'];
                            $isResourceSub = str_ends_with($item['route'], '.index') 
                                && str_starts_with($currentRoute, substr($item['route'], 0, -6) . '.');
                            $isActive = $hasRoute && ($isExact || $isResourceSub);
                            $url = $hasRoute ? route($item['route']) : '#';
                        @endphp
                        <a 
                            href="{{ $url }}"
                            @if (!empty($item['external'])) target="_blank" rel="noopener noreferrer" @endif
                            title="{{ $item['label'] }}"
                            :class="{
                                'justify-center px-0': sidebarCollapsed,
                                'px-3': !sidebarCollapsed,
                                'bg-brand-primary text-text-on-brand-primary font-bold shadow-xs': {{ $isActive ? 'true' : 'false' }},
                                'text-text-secondary hover:bg-surface-bg hover:text-text-primary font-medium': !{{ $isActive ? 'true' : 'false' }}
                            }"
                            class="relative flex items-center h-10 rounded-btn transition-all duration-150 group text-xs active:scale-[0.98]"
                        >
                            <!-- Nav Icon -->
                            <div class="flex items-center justify-center flex-shrink-0" :class="sidebarCollapsed ? 'w-full' : 'w-5 mr-3'">
                                <i 
                                    data-lucide="{{ $item['icon'] }}" 
                                    class="w-4 h-4 transition-transform group-hover:scale-105 {{ $isActive ? 'text-text-on-brand-primary' : 'text-text-secondary group-hover:text-text-primary' }}"
                                ></i>
                            </div>

                            <!-- Nav Label & Badge (Expanded) -->
                            <div x-show="!sidebarCollapsed" x-cloak class="flex-1 flex items-center justify-between overflow-hidden whitespace-nowrap">
                                <span class="truncate">
                                    {{ $item['label'] }}
                                </span>

                                @if (!empty($item['badge']))
                                    <span class="ml-2 px-2 py-0.5 text-[11px] font-bold rounded-full {{ $isActive ? 'bg-neutral-900 text-brand-primary' : 'bg-brand-secondary text-white dark:bg-brand-primary dark:text-neutral-900' }}">
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
    <div class="p-3 border-t border-border-subtle space-y-1.5 flex-shrink-0">
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
                class="w-full h-10 rounded-btn text-state-danger-text hover:bg-state-danger-bg/20 flex items-center transition-all duration-150 group text-xs font-semibold border border-transparent hover:border-state-danger-bg active:scale-[0.96]"
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
            aria-label="Perluas bilah sisi"
            class="hidden lg:flex w-full h-10 rounded-btn border border-border-subtle text-text-secondary hover:text-text-primary hover:bg-surface-bg items-center justify-center transition-colors active:scale-[0.96]"
        >
            <i data-lucide="chevrons-right" class="w-4 h-4"></i>
        </button>

        <div x-show="!sidebarCollapsed" x-cloak class="text-center pt-1">
            <p class="text-[11px] text-text-secondary">
                Career School v1.0 &copy; {{ date('Y') }}
            </p>
        </div>
    </div>
</aside>
