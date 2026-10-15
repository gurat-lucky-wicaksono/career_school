@php
    $currentRoute = Route::currentRouteName() ?? '';
    $user = auth()->user();
    $company = $user?->company;
    
    $pendingAppsCount = 0;
    if ($company) {
        $pendingAppsCount = \App\Models\Application::whereIn('job_id', $company->jobs()->pluck('id'))
            ->where('status', 'tertunda')
            ->count();
    }

    $navSections = [
        [
            'category' => 'Menu Utama',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'company.dashboard',
                    'icon' => 'layout-dashboard',
                    'badge' => null,
                ],
            ],
        ],
        [
            'category' => 'Rekrutmen & Talenta',
            'items' => [
                [
                    'label' => 'Lowongan Kerja',
                    'route' => 'company.jobs.index',
                    'icon' => 'briefcase',
                    'badge' => null,
                ],
                [
                    'label' => 'Pelamar Masuk',
                    'route' => 'company.applications.index',
                    'icon' => 'users',
                    'badge' => $pendingAppsCount > 0 ? (string)$pendingAppsCount : null,
                ],
            ],
        ],
        [
            'category' => 'Profil & Portal',
            'items' => [
                [
                    'label' => 'Profil Perusahaan',
                    'route' => 'company.profile',
                    'icon' => 'building-2',
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
    x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 backdrop-blur-xs z-40 lg:hidden"
    aria-hidden="true"
></div>

<!-- Dual-Mode Responsive Sidebar (Collapsible Desktop + Drawer Mobile) -->
<aside 
    id="company-sidebar"
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full lg:translate-x-0': !sidebarOpen,
        'lg:w-20': sidebarCollapsed,
        'lg:w-64': !sidebarCollapsed
    }"
    class="fixed top-0 bottom-0 left-0 z-50 flex flex-col bg-surface-card border-r border-slate-200/80 dark:border-white/10 transition-all duration-300 ease-in-out w-64 select-none font-sans"
    aria-label="Sidebar Navigasi Mitra"
>
    <!-- Brand Header -->
    <div class="h-16 flex items-center px-4 border-b border-slate-200/80 dark:border-white/10 overflow-hidden flex-shrink-0 transition-colors">
        <a href="{{ route('company.dashboard') }}" class="flex items-center gap-3 w-full group">
            <!-- Icon / Logo Container with young lime background -->
            <div class="w-10 h-10 rounded-xl bg-brand-primary flex items-center justify-center flex-shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                <i data-lucide="briefcase" class="w-5 h-5 text-neutral-900 stroke-[2.2]"></i>
            </div>

            <!-- Title & Subtitle (Hidden when collapsed) -->
            <div 
                x-show="!sidebarCollapsed" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-1"
                x-transition:enter-end="opacity-100 translate-x-0"
                class="truncate transition-opacity"
            >
                <div class="font-bold text-sm text-slate-900 dark:text-white tracking-tight leading-none truncate">
                    {{ $company->nama ?? 'Mitra Industri' }}
                </div>
                <div class="text-[11px] font-medium text-slate-500 dark:text-neutral-400 mt-1 flex items-center gap-1.5 truncate">
                    @if ($company && $company->isVerified())
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-semibold">DUDI Terverifikasi</span>
                    @else
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span class="text-amber-600 dark:text-amber-400 font-semibold">Menunggu Verifikasi</span>
                    @endif
                </div>
            </div>
        </a>
    </div>

    <!-- Navigation Menu Items -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 custom-scrollbar">
        @foreach ($navSections as $section)
            <div class="space-y-1">
                <!-- Section Category Header -->
                <div 
                    x-show="!sidebarCollapsed"
                    class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-neutral-500 mb-1"
                >
                    {{ $section['category'] }}
                </div>

                <!-- Section Items List -->
                <div class="space-y-1">
                    @foreach ($section['items'] as $item)
                        @php
                            $routePrefix = explode('.', $item['route'])[0] . '.' . (explode('.', $item['route'])[1] ?? '');
                            $isActive = ($currentRoute === $item['route']) || (str_starts_with($currentRoute, $routePrefix) && !empty($item['route']));
                        @endphp

                        <a 
                            href="{{ route($item['route']) }}"
                            @click="if (window.innerWidth < 1024) sidebarOpen = false"
                            title="{{ $item['label'] }}"
                            class="group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ $isActive ? 'bg-brand-primary text-neutral-900 shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-neutral-800/80 hover:text-slate-900 dark:hover:text-white' }}"
                            :class="{ 'justify-center px-0': sidebarCollapsed }"
                        >
                            <i 
                                data-lucide="{{ $item['icon'] }}" 
                                class="w-4 h-4 flex-shrink-0 transition-transform group-hover:scale-110 {{ $isActive ? 'text-neutral-900 stroke-[2.2]' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-200' }}"
                            ></i>

                            <!-- Label (Hidden on mini mode) -->
                            <span 
                                x-show="!sidebarCollapsed" 
                                class="truncate flex-1 tracking-tight"
                            >
                                {{ $item['label'] }}
                            </span>

                            <!-- Notification Count Badge -->
                            @if (!empty($item['badge']))
                                <span 
                                    x-show="!sidebarCollapsed"
                                    class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $isActive ? 'bg-neutral-900 text-brand-primary' : 'bg-brand-primary text-neutral-900' }}"
                                >
                                    {{ $item['badge'] }}
                                </span>
                            @endif

                            <!-- Floating Tooltip for Mini Sidebar Mode -->
                            <div 
                                x-show="sidebarCollapsed"
                                class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-900 text-white text-[11px] font-medium rounded-lg shadow-xl opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap hidden lg:block"
                            >
                                {{ $item['label'] }}
                                @if (!empty($item['badge']))
                                    <span class="ml-1 text-brand-primary font-bold">({{ $item['badge'] }})</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    <!-- Sidebar Bottom Footer -->
    <div class="p-3 border-t border-slate-200/80 dark:border-white/10 flex-shrink-0">
        <!-- Quick Logout Button -->
        <button 
            type="button" 
            onclick="confirmSidebarLogout()" 
            title="Keluar dari Akun"
            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors"
            :class="{ 'justify-center px-0': sidebarCollapsed }"
        >
            <i data-lucide="log-out" class="w-4 h-4 flex-shrink-0"></i>
            <span x-show="!sidebarCollapsed" class="truncate">Keluar Akun</span>
        </button>

        <!-- Hidden Logout Form -->
        <form id="sidebar-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
            @csrf
        </form>
    </div>
</aside>
