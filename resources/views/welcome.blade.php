<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Career School — Platform karier, portofolio karya, dan jejaring resmi SMK Negeri 1 Cirebon. Dilengkapi pencocokan lowongan industri terukur dan validasi guru kejuruan.">
    <title>Career School Smkn 1 Cirebon</title>
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

    <!-- 1. Sticky Navigation Header -->
    <x-landing.navbar />

    <!-- 2. Main Landmark (All 7 Sections Merged in One Continuous Scrollable Flow) -->
    <main class="flex-1 relative">
        @if (session('success'))
            <div id="flash-banner" class="fixed top-20 right-4 sm:right-6 z-50 max-w-sm sm:max-w-md bg-emerald-600 text-white px-4 py-3 rounded-card shadow-xl flex items-center justify-between gap-3 text-xs sm:text-sm animate-fade-in">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('flash-banner').remove()" class="text-white/80 hover:text-white font-bold text-base">&times;</button>
            </div>
        @endif
        @if (session('info'))
            <div id="flash-banner-info" class="fixed top-20 right-4 sm:right-6 z-50 max-w-sm sm:max-w-md bg-neutral-900 text-white px-4 py-3 rounded-card shadow-xl flex items-center justify-between gap-3 text-xs sm:text-sm border border-neutral-700 animate-fade-in">
                <span>{{ session('info') }}</span>
                <button type="button" onclick="document.getElementById('flash-banner-info').remove()" class="text-white/80 hover:text-white font-bold text-base">&times;</button>
            </div>
        @endif

        <!-- Section 1: Beranda (/beranda) -->
        <x-landing.hero :stats="$stats" />

        <!-- Section 2: Platform (/platform) -->
        <x-landing.platform />

        <!-- Section 3: Sekolah (/sekolah) -->
        <x-landing.school-profile />

        <!-- Section 4: Fitur Unggulan (/fitur-unggulan) -->
        <x-landing.features />

        <!-- Section 5: Cara Kerja (/cara-kerja) -->
        <x-landing.roles />
        <x-landing.simulator />

        <!-- Section 6: Statistik (/statistik) -->
        <x-landing.stats :stats="$stats" />

        <!-- Section 7: FAQ (/faq) -->
        <x-landing.faq />

        <!-- Call to Action -->
        <x-landing.cta />
    </main>

    <!-- 3. Footer Landmark -->
    <x-landing.footer />

    <!-- Clean Section Routing & Dynamic History API Script -->
    <script>
        (() => {
            const sectionRoutes = {
                '/beranda': 'beranda',
                '/': 'beranda',
                '/platform': 'platform',
                '/sekolah': 'sekolah',
                '/fitur-unggulan': 'fitur-unggulan',
                '/fitur': 'fitur-unggulan',
                '/cara-kerja': 'cara-kerja',
                '/statistik': 'statistik',
                '/faq': 'faq'
            };

            const idToPath = {
                'beranda': '/beranda',
                'platform': '/platform',
                'sekolah': '/sekolah',
                'fitur-unggulan': '/fitur-unggulan',
                'fitur': '/fitur-unggulan',
                'cara-kerja': '/cara-kerja',
                'statistik': '/statistik',
                'faq': '/faq'
            };

            const updateNavHighlight = (activeId) => {
                document.querySelectorAll('[data-nav]').forEach(link => {
                    const navKey = link.getAttribute('data-nav');
                    const isActive = navKey === activeId || (navKey === 'fitur-unggulan' && activeId === 'fitur');
                    if (isActive) {
                        link.classList.add('text-text-primary', 'font-bold');
                        link.classList.remove('text-text-secondary');
                    } else {
                        link.classList.remove('text-text-primary', 'font-bold');
                        link.classList.add('text-text-secondary');
                    }
                });
            };

            const scrollToSection = (sectionId, updateUrl = true, pushHistory = true) => {
                const el = document.getElementById(sectionId) || document.querySelector(`[data-section="${sectionId}"]`);
                if (!el) return;

                el.scrollIntoView({ behavior: 'smooth' });

                if (updateUrl) {
                    const path = idToPath[sectionId] || '/beranda';
                    if (window.location.pathname !== path) {
                        if (pushHistory) {
                            window.history.pushState({ section: sectionId }, '', path);
                        } else {
                            window.history.replaceState({ section: sectionId }, '', path);
                        }
                    }
                }
                updateNavHighlight(sectionId);
            };

            // Intercept in-page navigation clicks to avoid hash symbols and full reloads
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href^="/"]');
                if (!link) return;

                const url = new URL(link.href, window.location.origin);
                const path = url.pathname;
                const sectionId = sectionRoutes[path];

                if (sectionId) {
                    e.preventDefault();
                    scrollToSection(sectionId, true, true);
                }
            });

            // Handle browser back & forward navigation
            window.addEventListener('popstate', (e) => {
                const path = window.location.pathname;
                const sectionId = sectionRoutes[path] || (e.state && e.state.section) || 'beranda';
                scrollToSection(sectionId, false, false);
            });

            // Initial auto-scroll on direct page visit (e.g. /sekolah)
            document.addEventListener('DOMContentLoaded', () => {
                const initial = @json($initialSection ?? 'beranda');
                const currentPath = window.location.pathname;
                const targetSection = sectionRoutes[currentPath] || initial;

                if (targetSection && targetSection !== 'beranda') {
                    setTimeout(() => {
                        scrollToSection(targetSection, false, false);
                    }, 150);
                } else {
                    updateNavHighlight('beranda');
                }

                // Intersection Observer for scroll spy address bar updates
                const sectionObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const id = entry.target.getAttribute('data-section') || entry.target.id;
                            if (id && idToPath[id]) {
                                const newPath = idToPath[id];
                                if (window.location.pathname !== newPath) {
                                    window.history.replaceState({ section: id }, '', newPath);
                                }
                                updateNavHighlight(id);
                            }
                        }
                    });
                }, {
                    rootMargin: '-20% 0px -60% 0px',
                    threshold: 0
                });

                ['beranda', 'platform', 'sekolah', 'fitur-unggulan', 'cara-kerja', 'statistik', 'faq'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) sectionObserver.observe(el);
                });
            });
        })();
    </script>

</body>
</html>
