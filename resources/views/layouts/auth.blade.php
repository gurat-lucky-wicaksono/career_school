<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', 'Portal Autentikasi Career School — SMK Negeri 1 Cirebon')">
    <title>@yield('title', 'Autentikasi') — Career School SMKN 1 Cirebon</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logos/logo-career-school.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sora:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Vendor Libraries -->
    <link rel="stylesheet" href="{{ asset('vendor/choices/choices.min.css') }}">
    <script src="{{ asset('vendor/choices/choices.min.js') }}"></script>

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-surface-bg text-text-primary antialiased font-sans selection:bg-brand-primary selection:text-neutral-900 transition-colors duration-150 min-h-screen flex flex-col">

    <!-- Minimal Auth Header -->
    <header class="w-full border-b border-border-subtle bg-surface-bg/90 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                <img
                    src="{{ asset('images/logos/logo-career-school.png') }}"
                    alt="Career School Logo"
                    class="h-9 w-9 object-contain rounded-lg group-hover:scale-105 transition-transform"
                >
                <div class="flex flex-col">
                    <span class="font-heading font-bold text-base sm:text-lg text-text-primary tracking-tight leading-tight">
                        Career School
                    </span>
                    <span class="text-[10px] text-text-secondary font-medium tracking-wide uppercase">
                        SMKN 1 Cirebon
                    </span>
                </div>
            </a>

            <!-- Right Controls -->
            <div class="flex items-center gap-3">
                <!-- Theme Toggle Button -->
                <button
                    type="button"
                    onclick="window.toggleTheme()"
                    class="p-2 rounded-btn bg-surface-card border border-border-default/80 hover:border-text-primary text-text-secondary hover:text-text-primary transition-all duration-150 shadow-xs"
                    aria-label="Alihkan tema gelap/terang"
                    title="Ganti Tema"
                >
                    <span class="dark:hidden flex items-center justify-center">
                        <x-icon name="moon" class="w-4 h-4" />
                    </span>
                    <span class="hidden dark:flex items-center justify-center">
                        <x-icon name="sun" class="w-4 h-4" />
                    </span>
                </button>

                <!-- Back to Landing Link -->
                <a
                    href="{{ route('landing') }}"
                    class="px-3.5 py-1.5 rounded-btn bg-surface-card border border-border-default/80 hover:border-text-primary text-text-primary text-xs font-semibold transition-all duration-150 active:scale-[0.96] shadow-xs flex items-center gap-1.5"
                >
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </header>

    @hasSection('fullscreen')
        @yield('fullscreen')
    @else
        <!-- Main Form Container -->
        <main class="flex-1 flex items-center justify-center py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
            <div class="w-full @yield('container_width', 'max-w-md') my-auto">
                @yield('content')
            </div>
        </main>
    @endif

    <!-- Minimal Auth Footer -->
    <footer class="border-t border-border-subtle py-4 text-center text-xs text-text-secondary">
        <p>© 2026 Career School · SMK Negeri 1 Cirebon · PKWU Paket 15</p>
    </footer>

</body>
</html>
