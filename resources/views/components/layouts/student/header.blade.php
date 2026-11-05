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

    <!-- Center Section: Quick Search Form (Redirects to /student/jobs?search=...) -->
    <div class="hidden md:flex items-center flex-1 max-w-md mx-6">
        <form action="{{ route('student.jobs.index') }}" method="GET" class="w-full relative">
            <i data-lucide="search" class="w-4 h-4 text-text-secondary absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input 
                type="text" 
                name="q" 
                id="global-search-input"
                placeholder="Cari lowongan, keahlian, atau mitra DUDI... (Ctrl + K)" 
                value="{{ request('q', request('search')) }}"
                class="w-full pl-9 pr-14 py-1.5 text-xs rounded-full bg-surface-bg border border-border-default/50 focus:border-brand-primary focus:bg-surface-card focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary transition-all outline-none"
            >
            <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-0.5 pointer-events-none text-[10px] font-semibold text-text-secondary bg-surface-card border border-border-subtle px-1.5 py-0.5 rounded">
                <span>Ctrl</span>
                <span>K</span>
            </div>
        </form>
    </div>

    <!-- Right Section: Actions & Profile Menu -->
    <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
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
