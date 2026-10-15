<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Anti-FOUC Inline Theme Script (Executes synchronously before paint) -->
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

    <title>@yield('title', 'Admin Dashboard') &mdash; Career School SMKN 1 Cirebon</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logos/logo-career-school.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sora:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Vendor Choices (local) -->
    <link rel="stylesheet" href="{{ asset('vendor/choices/choices.min.css') }}">
    <script src="{{ asset('vendor/choices/choices.min.js') }}"></script>

    <!-- Vite Styles & Scripts (Tailwind, Alpine, ApexCharts, Flatpickr, SweetAlert2, Lucide) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body 
    x-data="{
        sidebarOpen: false,
        sidebarCollapsed: localStorage.getItem('career_sidebar_collapsed') === 'true',
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('career_sidebar_collapsed', this.sidebarCollapsed);
            // Re-render Lucide icons and charts on layout transition
            setTimeout(() => {
                if (window.lucide) window.lucide.createIcons();
                window.dispatchEvent(new Event('resize'));
            }, 305);
        }
    }"
    @toggle-sidebar.window="toggleSidebar()"
    class="h-full bg-surface-bg text-text-primary antialiased font-sans"
>
    <div class="min-h-full flex flex-col">
        <!-- Dual-Mode Sidebar -->
        <x-layouts.admin.sidebar />

        <!-- Main Content Area with Adaptive Desktop Padding -->
        <div 
            :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'" 
            class="flex-1 flex flex-col transition-all duration-300 ease-in-out min-w-0"
        >
            <!-- Topbar / Header -->
            <x-layouts.admin.header :breadcrumbs="$breadcrumbs ?? []" />

            <!-- Page Content Body -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                <!-- Session Flash Alerts (Clean, Non-AI-Slop) -->
                @if (session('success'))
                    <div class="mb-6 px-4 py-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm flex items-center justify-between gap-3 shadow-sm">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0"></i>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 px-4 py-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm flex items-center justify-between gap-3 shadow-sm">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400 flex-shrink-0"></i>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                        <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-300">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 px-4 py-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-2.5">
                                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 dark:text-rose-400 flex-shrink-0 mt-0.5"></i>
                                <div>
                                    <span class="font-semibold block mb-1">Terdapat kesalahan pada isian formulir:</span>
                                    <ul class="list-disc list-inside text-xs space-y-0.5 opacity-90">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" @click="$el.closest('div.rounded-2xl').remove()" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-300">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </main>

            <!-- Minimalist Footer -->
            <x-layouts.admin.footer />
        </div>
    </div>

    @stack('scripts')
</body>
</html>
