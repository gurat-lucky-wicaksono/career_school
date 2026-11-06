@php
    $currentRoute = Route::currentRouteName() ?? '';
    $user = auth()->user();
    $teacher = $user?->teacher;

    $pendingSkillsCount = \App\Models\StudentSkill::where('tervalidasi', false)->count();
    $pendingEvaluationsCount = \App\Models\InternshipEvaluation::where('status', 'submitted')->count();

    $navSections = [
        [
            'category' => 'Menu utama',
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
            'category' => 'Bimbingan & validasi',
            'items' => [
                [
                    'label' => 'Validasi keahlian',
                    'route' => 'teacher.skills.index',
                    'icon' => 'award',
                    'badge' => $pendingSkillsCount > 0 ? (string)$pendingSkillsCount : null,
                ],
                [
                    'label' => 'Siswa bimbingan PKL',
                    'route' => 'teacher.mentoring.index',
                    'icon' => 'users',
                    'badge' => null,
                ],
                [
                    'label' => 'Tinjau portofolio',
                    'route' => 'teacher.portfolios.index',
                    'icon' => 'folder-kanban',
                    'badge' => null,
                ],
                [
                    'label' => 'Evaluasi & Nilai PKL',
                    'route' => 'teacher.evaluations.index',
                    'icon' => 'file-check-2',
                    'badge' => $pendingEvaluationsCount > 0 ? (string)$pendingEvaluationsCount : null,
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
        'lg:w-20': sidebarCollapsed,
        'lg:w-64': !sidebarCollapsed
    }"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-surface-card border-r border-border-subtle flex flex-col transition-all duration-300 ease-in-out shadow-xs"
>
    <!-- Brand Header -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-border-subtle flex-shrink-0">
        <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 overflow-hidden group">
            <div class="w-10 h-10 rounded-btn bg-brand-primary flex items-center justify-center flex-shrink-0 shadow-xs">
                <img src="{{ asset('images/logos/logo-career-school.png') }}" alt="Career School Logo" class="w-6 h-6 object-contain">
            </div>
            <div x-show="!sidebarCollapsed" class="flex flex-col min-w-0 transition-opacity duration-200">
                <span class="font-bold text-sm text-text-primary truncate font-heading tracking-tight leading-snug">
                    Portal Guru
                </span>
                <span class="text-[10px] font-semibold uppercase tracking-wider text-brand-secondary dark:text-brand-primary truncate">
                    SMKN 1 Cirebon
                </span>
            </div>
        </a>

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
            <div class="space-y-1">
                <!-- Section Header (Only if expanded) -->
                <div x-show="!sidebarCollapsed" class="px-3 mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-text-secondary/70">
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
                        class="flex items-center gap-3 px-3 py-2.5 rounded-btn text-xs transition-all duration-150 group relative active:scale-[0.98] {{ $isActive ? 'bg-brand-primary text-text-on-brand-primary font-bold shadow-xs' : 'text-text-secondary hover:bg-surface-bg hover:text-text-primary font-medium' }}"
                    >
                        <i 
                            data-lucide="{{ $item['icon'] }}" 
                            class="w-4 h-4 flex-shrink-0 transition-transform group-hover:scale-105 {{ $isActive ? 'text-text-on-brand-primary' : 'text-text-secondary group-hover:text-text-primary' }}"
                        ></i>

                        <span x-show="!sidebarCollapsed" class="truncate flex-1">
                            {{ $item['label'] }}
                        </span>

                        @if ($item['badge'])
                            <span 
                                x-show="!sidebarCollapsed"
                                class="px-1.5 py-0.5 text-[10px] font-bold rounded-full {{ $isActive ? 'bg-neutral-900 text-brand-primary' : 'bg-state-warning-bg text-state-warning-text' }}"
                            >
                                {{ $item['badge'] }}
                            </span>
                        @endif

                        <!-- Tooltip on collapsed state -->
                        <div 
                            x-show="sidebarCollapsed" 
                            class="fixed left-20 px-2.5 py-1.5 bg-neutral-900 text-neutral-50 text-[11px] font-medium rounded-btn shadow-lg pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap border border-neutral-700"
                        >
                            {{ $item['label'] }}
                            @if ($item['badge'])
                                <span class="ml-1 text-[9px] text-brand-primary font-bold">({{ $item['badge'] }})</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endforeach
    </div>

    <!-- Teacher User Profile Footer Card -->
    <div class="p-3 border-t border-border-subtle flex-shrink-0">
        <div class="p-2 rounded-btn bg-surface-bg flex items-center justify-between gap-2 border border-border-subtle">
            <div class="flex items-center gap-2.5 min-w-0 overflow-hidden flex-1 group">
                <div class="w-8 h-8 rounded-full bg-brand-primary text-text-on-brand-primary font-bold font-heading text-xs flex items-center justify-center flex-shrink-0 shadow-xs">
                    {{ strtoupper(substr($user->name ?? 'G', 0, 1)) }}
                </div>
                <div x-show="!sidebarCollapsed" class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-text-primary truncate">
                        {{ $user->name ?? 'Guru kejuruan' }}
                    </p>
                    <p class="text-[10px] text-text-secondary truncate">
                        {{ $teacher?->mata_pelajaran ?? 'PPLG SMKN 1' }}
                    </p>
                </div>
            </div>

            <!-- Quick Logout Form -->
            <form method="POST" action="{{ route('logout') }}" x-show="!sidebarCollapsed" class="flex-shrink-0">
                @csrf
                <button 
                    type="submit" 
                    title="Keluar dari akun"
                    aria-label="Keluar dari akun"
                    class="w-7 h-7 rounded-btn text-text-secondary hover:text-state-danger-text hover:bg-state-danger-bg/20 flex items-center justify-center transition-all active:scale-[0.96]"
                >
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
