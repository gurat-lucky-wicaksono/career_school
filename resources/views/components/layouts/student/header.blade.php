@props([
    'breadcrumbs' => [],
])

@php
    $user = auth()->user();
    $student = $user?->student;
@endphp

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

    <!-- Center Section: Quick Search Form (Redirects to /student/jobs?search=...) -->
    <div class="hidden md:flex items-center flex-1 max-w-md mx-6">
        <form action="{{ route('student.jobs.index') }}" method="GET" class="w-full relative">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input 
                type="text" 
                name="q" 
                id="global-search-input"
                placeholder="Cari lowongan, keahlian, atau mitra DUDI... (Ctrl + K)" 
                value="{{ request('q', request('search')) }}"
                class="w-full pl-9 pr-14 py-1.5 text-xs rounded-full bg-slate-100/80 dark:bg-neutral-800/80 border border-transparent focus:border-brand-primary focus:bg-white dark:focus:bg-neutral-900 focus:ring-2 focus:ring-brand-primary/20 text-slate-800 dark:text-slate-100 placeholder-slate-400 transition-all outline-none"
            >
            <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-0.5 pointer-events-none text-[10px] font-semibold text-slate-400 bg-slate-200/60 dark:bg-neutral-700/60 px-1.5 py-0.5 rounded">
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
            aria-label="Ganti Tema Tampilan"
            class="w-9 h-9 rounded-xl text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-neutral-800 flex items-center justify-center transition-colors"
        >
            <i data-lucide="sun" class="w-4 h-4 hidden dark:block text-amber-400"></i>
            <i data-lucide="moon" class="w-4 h-4 block dark:hidden text-slate-600"></i>
        </button>

        <!-- Notification Bell -->
        <x-notifications.dropdown />

        <!-- Profile Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button 
                type="button" 
                @click="open = !open" 
                @click.outside="open = false" 
                class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors"
            >
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-brand-primary to-indigo-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                    {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                </div>
                <div class="hidden sm:block text-left pr-1">
                    <span class="block text-xs font-semibold text-slate-800 dark:text-slate-100 leading-tight">
                        {{ Str::limit($user->name ?? 'Siswa', 15) }}
                    </span>
                    <span class="block text-[10px] text-brand-primary font-medium">
                        {{ $student?->schoolClass?->nama ?? 'PPLG' }}
                    </span>
                </div>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
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
                class="absolute right-0 mt-2 w-56 bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-xl py-2 z-50 divide-y divide-slate-100 dark:divide-white/5"
                style="display: none;"
            >
                <div class="px-4 py-2.5">
                    <p class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ $user->name }}</p>
                    <p class="text-[11px] text-slate-400 truncate mt-0.5">NISN: {{ $student?->nisn ?? '-' }}</p>
                    <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium mt-1">
                        Siswa Aktif Angkatan {{ $student?->angkatan ?? '2024' }}
                    </p>
                </div>

                <div class="py-1.5">
                    <a href="{{ route('student.profile') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-neutral-800">
                        <i data-lucide="user-check" class="w-4 h-4 text-slate-400"></i>
                        <span>Profil & Keahlian Saya</span>
                    </a>
                    <a href="{{ route('student.portfolios.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-neutral-800">
                        <i data-lucide="folder-kanban" class="w-4 h-4 text-slate-400"></i>
                        <span>Portofolio Karya</span>
                    </a>
                    <a href="{{ route('student.applications.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-neutral-800">
                        <i data-lucide="send" class="w-4 h-4 text-slate-400"></i>
                        <span>Lamaran Terkirim</span>
                    </a>
                </div>

                <div class="py-1.5">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-left font-medium">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span>Keluar Sistem</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
