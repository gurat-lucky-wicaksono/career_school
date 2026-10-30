<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Komunitas & Feed Vokasi') &mdash; CareerSchool SMKN 1 Cirebon</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logos/logo-career-school.png') }}">

    <!-- Anti-FOUC Theme Script -->
    <script>
        (function () {
            try {
                const savedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (_) {}
        })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sora:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-bg text-text-primary antialiased font-sans selection:bg-brand-primary selection:text-neutral-900 min-h-screen flex flex-col">

    <!-- Community Top Navigation Header -->
    <header class="sticky top-0 z-40 bg-surface-card/95 backdrop-blur-md border-b border-slate-200/80 dark:border-white/10 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            
            <!-- Left: Brand Logo & Title -->
            <div class="flex items-center gap-6">
                <a href="{{ route('feed.index') }}" class="flex items-center gap-2.5 shrink-0 group focus:outline-none">
                    <div class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-neutral-800 p-1 flex items-center justify-center border border-slate-200/60 dark:border-white/10 group-hover:scale-105 transition-transform">
                        <img src="{{ asset('images/logos/logo-career-school.png') }}" alt="CareerSchool" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="block text-sm font-bold text-slate-900 dark:text-white font-sans tracking-tight leading-none">
                            Career School
                        </span>
                        <span class="block text-[10px] font-semibold text-brand-secondary dark:text-brand-primary mt-0.5">
                            Komunitas Vokasi
                        </span>
                    </div>
                </a>

                <!-- Nav Links Desktop -->
                <nav class="hidden md:flex items-center gap-1 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <a href="{{ route('feed.index') }}" class="px-3 py-2 rounded-xl {{ request()->routeIs('feed.index') ? 'bg-brand-primary/10 text-brand-secondary dark:text-brand-primary font-bold' : 'hover:bg-slate-100 dark:hover:bg-neutral-800' }} transition-colors">
                        Linimasa Feed
                    </a>
                    <a href="{{ route('bookmarks.index') }}" class="px-3 py-2 rounded-xl {{ request()->routeIs('bookmarks.index') ? 'bg-brand-primary/10 text-brand-secondary dark:text-brand-primary font-bold' : 'hover:bg-slate-100 dark:hover:bg-neutral-800' }} transition-colors">
                        Tersimpan
                    </a>
                    <a href="{{ route('public.announcements') }}" class="px-3 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors">
                        Pengumuman
                    </a>
                    <a href="{{ route('public.agendas') }}" class="px-3 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors">
                        Agenda
                    </a>
                </nav>
            </div>

            <!-- Middle: Search in Feed -->
            <div class="hidden sm:block max-w-xs w-full">
                <form method="GET" action="{{ route('feed.index') }}" class="relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari kiriman atau tagar..."
                        class="w-full pl-9 pr-4 py-1.5 text-xs rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/70 dark:bg-neutral-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary"
                    >
                    <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5"></i>
                </form>
            </div>

            <!-- Right: Actions & User Dropdown -->
            <div class="flex items-center gap-2.5">
                <!-- Theme Toggle -->
                <button
                    type="button"
                    onclick="window.toggleTheme()"
                    aria-label="Ubah tema"
                    class="w-9 h-9 rounded-xl border border-slate-200/80 dark:border-white/10 bg-surface-card text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-neutral-800 transition-all flex items-center justify-center shadow-xs"
                >
                    <span class="dark:hidden flex items-center justify-center">
                        <i data-lucide="moon" class="w-4 h-4"></i>
                    </span>
                    <span class="hidden dark:flex items-center justify-center">
                        <i data-lucide="sun" class="w-4 h-4"></i>
                    </span>
                </button>

                <!-- Portal Shortcut based on Role -->
                @php
                    $user = auth()->user();
                    $portalRoute = route('landing');
                    $portalLabel = 'Portal';
                    if ($user->isAdmin()) {
                        $portalRoute = route('admin.dashboard');
                        $portalLabel = 'Portal Admin';
                    } elseif ($user->isGuru()) {
                        $portalRoute = route('teacher.dashboard');
                        $portalLabel = 'Portal Guru';
                    } elseif ($user->isSiswa()) {
                        $portalRoute = route('student.dashboard');
                        $portalLabel = 'Portal Siswa';
                    } elseif ($user->isPerusahaan()) {
                        $portalRoute = route('company.dashboard');
                        $portalLabel = 'Portal Industri';
                    }
                @endphp

                <a 
                    href="{{ $portalRoute }}" 
                    class="hidden lg:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-surface-card hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors shadow-xs"
                >
                    <i data-lucide="layout-dashboard" class="w-3.5 h-3.5 text-brand-secondary dark:text-brand-primary"></i>
                    <span>{{ $portalLabel }}</span>
                </a>

                <!-- Notification Bell -->
                <x-notifications.dropdown />

                <!-- User Dropdown (Alpine.js) -->
                <div x-data="{ open: false }" class="relative">
                    <button 
                        @click="open = !open" 
                        class="flex items-center gap-2 p-1 rounded-xl hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors focus:outline-none"
                    >
                        <div class="w-8 h-8 rounded-full bg-brand-primary/20 text-brand-secondary dark:text-brand-primary font-bold text-xs flex items-center justify-center border border-brand-primary/30">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                    </button>

                    <div 
                        x-show="open" 
                        x-cloak 
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-56 rounded-2xl bg-surface-card border border-slate-200/80 dark:border-white/10 shadow-lg py-2 z-50 text-xs divide-y divide-slate-100 dark:divide-white/5"
                    >
                        <div class="px-4 py-2.5">
                            <p class="font-bold text-slate-900 dark:text-white truncate">
                                {{ $user->name }}
                            </p>
                            <p class="text-[11px] text-slate-400 truncate">
                                @ {{ $user->username }} &bull; {{ ucfirst($user->role->nama ?? 'Pengguna') }}
                            </p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('community.profile', $user->username) }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors">
                                <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                                Profil Komunitas Saya
                            </a>
                            <a href="{{ route('bookmarks.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors">
                                <i data-lucide="bookmark" class="w-4 h-4 text-slate-400"></i>
                                Kiriman Tersimpan
                            </a>
                            <a href="{{ $portalRoute }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 text-slate-400"></i>
                                Kembali ke {{ $portalLabel }}
                            </a>
                        </div>

                        <div class="py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-colors">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    Keluar (Logout)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Notifications -->
    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-800 dark:text-emerald-300 flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">&times;</button>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs font-semibold text-rose-800 dark:text-rose-300 flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 font-bold">&times;</button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-1 py-8">
        @yield('content')
    </main>

    <!-- Minimalist Footer -->
    <footer class="py-6 border-t border-slate-200/80 dark:border-white/10 text-center text-xs text-slate-400 dark:text-neutral-500 font-sans">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; 2026 Career School &mdash; Komunitas Vokasi SMKN 1 Cirebon. Hak Cipta Dilindungi.</p>
            <div class="flex items-center gap-4 text-[11px]">
                <a href="{{ route('public.announcements') }}" class="hover:underline">Pengumuman BKK</a>
                <a href="{{ route('public.agendas') }}" class="hover:underline">Agenda Kegiatan</a>
                <a href="/faq" class="hover:underline">Bantuan & FAQ</a>
            </div>
        </div>
    </footer>

    <!-- Lucide icons activator -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
