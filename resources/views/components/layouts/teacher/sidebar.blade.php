@php
    $currentRoute = Route::currentRouteName() ?? '';
    $user = auth()->user();
    $teacher = $user?->teacher;

    $pendingSkillsCount = \App\Models\StudentSkill::where('tervalidasi', false)->count();

    $navSections = [
        [
            'category' => 'Menu Utama',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'teacher.dashboard',
                    'icon' => 'layout-dashboard',
                    'badge' => null,
                ],
            ],
        ],
        [
            'category' => 'Bimbingan & Validasi',
            'items' => [
                [
                    'label' => 'Validasi Keahlian',
                    'route' => 'teacher.skills.index',
                    'icon' => 'award',
                    'badge' => $pendingSkillsCount > 0 ? (string)$pendingSkillsCount : null,
                ],
                [
                    'label' => 'Siswa Bimbingan PKL',
                    'route' => 'teacher.mentoring.index',
                    'icon' => 'users',
                    'badge' => null,
                ],
                [
                    'label' => 'Tinjau Portofolio',
                    'route' => 'teacher.portfolios.index',
                    'icon' => 'folder-kanban',
                    'badge' => null,
                ],
            ],
        ],
        [
            'category' => 'Jejaring & Komunitas',
            'items' => [
                [
                    'label' => 'Feed Komunitas',
                    'route' => 'feed.index',
                    'icon' => 'message-square',
                    'badge' => null,
                ],
                [
                    'label' => 'Kiriman Tersimpan',
                    'route' => 'bookmarks.index',
                    'icon' => 'bookmark',
                    'badge' => null,
                ],
            ],
        ],
        [
            'category' => 'Lainnya',
            'items' => [
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
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden"
    style="display: none;"
></div>

<!-- Sidebar Container -->
<aside 
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full lg:translate-x-0': !sidebarOpen,
        'lg:w-20': sidebarCollapsed,
        'lg:w-64': !sidebarCollapsed
    }"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-surface-card border-r border-slate-200/80 dark:border-white/10 flex flex-col transition-all duration-300 ease-in-out shadow-sm"
>
    <!-- Brand Header -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-slate-100 dark:border-white/5 flex-shrink-0">
        <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 overflow-hidden group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-primary to-emerald-600 flex items-center justify-center flex-shrink-0 shadow-sm shadow-brand-primary/20 text-white font-bold">
                <img src="{{ asset('images/logos/logo-career-school.png') }}" alt="Career School Logo" class="w-6 h-6 object-contain">
            </div>
            <div x-show="!sidebarCollapsed" class="flex flex-col min-w-0 transition-opacity duration-200">
                <span class="font-bold text-sm text-slate-900 dark:text-white truncate font-display tracking-tight leading-snug">
                    Portal Guru
                </span>
                <span class="text-[10px] font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 truncate">
                    SMKN 1 Cirebon
                </span>
            </div>
        </a>

        <!-- Mobile Close Button -->
        <button 
            type="button" 
            @click="sidebarOpen = false" 
            class="lg:hidden w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-neutral-800 flex items-center justify-center transition-colors"
        >
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- Navigation Menu Items -->
    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6 custom-scrollbar">
        @foreach ($navSections as $section)
            <div class="space-y-1">
                <!-- Section Header (Only if expanded) -->
                <div x-show="!sidebarCollapsed" class="px-3 mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400">
                        {{ $section['category'] }}
                    </span>
                </div>

                @foreach ($section['items'] as $item)
                    @php
                        $isActive = str_starts_with($currentRoute, $item['route']) && $item['route'] !== 'landing';
                    @endphp
                    <a 
                        href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                        @if (!empty($item['external'])) target="_blank" rel="noopener noreferrer" @endif
                        title="{{ $item['label'] }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium transition-all group relative {{ $isActive ? 'bg-brand-primary text-white font-semibold shadow-sm shadow-brand-primary/25' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-800/60 hover:text-slate-900 dark:hover:text-white' }}"
                    >
                        <i 
                            data-lucide="{{ $item['icon'] }}" 
                            class="w-4 h-4 flex-shrink-0 transition-transform group-hover:scale-110 {{ $isActive ? 'text-white' : 'text-slate-400 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-200' }}"
                        ></i>

                        <span x-show="!sidebarCollapsed" class="truncate flex-1">
                            {{ $item['label'] }}
                        </span>

                        @if ($item['badge'])
                            <span 
                                x-show="!sidebarCollapsed"
                                class="px-1.5 py-0.5 text-[10px] font-bold rounded-full {{ $isActive ? 'bg-white text-brand-primary' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' }}"
                            >
                                {{ $item['badge'] }}
                            </span>
                        @endif

                        <!-- Tooltip on collapsed state -->
                        <div 
                            x-show="sidebarCollapsed" 
                            class="fixed left-20 px-2.5 py-1.5 bg-slate-900 text-white text-[11px] font-medium rounded-lg shadow-lg pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap dark:bg-neutral-800 border border-slate-700 dark:border-white/10"
                        >
                            {{ $item['label'] }}
                            @if ($item['badge'])
                                <span class="ml-1 text-[9px] text-amber-300 font-bold">({{ $item['badge'] }})</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endforeach
    </div>

    <!-- Teacher User Profile Footer Card -->
    <div class="p-3 border-t border-slate-100 dark:border-white/5 flex-shrink-0">
        <div class="p-2 rounded-xl bg-slate-50 dark:bg-neutral-800/50 flex items-center justify-between gap-2 border border-slate-200/50 dark:border-white/5">
            <div class="flex items-center gap-2.5 min-w-0 overflow-hidden flex-1 group">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-emerald-600 to-teal-700 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 shadow-sm">
                    {{ strtoupper(substr($user->name ?? 'G', 0, 1)) }}
                </div>
                <div x-show="!sidebarCollapsed" class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-100 truncate">
                        {{ $user->name ?? 'Guru Kejuruan' }}
                    </p>
                    <p class="text-[10px] text-slate-400 dark:text-slate-400 truncate">
                        {{ $teacher?->mata_pelajaran ?? 'PPLG SMKN 1' }}
                    </p>
                </div>
            </div>

            <!-- Quick Logout Form -->
            <form method="POST" action="{{ route('logout') }}" x-show="!sidebarCollapsed" class="flex-shrink-0">
                @csrf
                <button 
                    type="submit" 
                    title="Keluar Akun"
                    class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 flex items-center justify-center transition-colors"
                >
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
