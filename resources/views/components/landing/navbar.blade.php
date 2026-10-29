<header class="sticky top-0 z-50 bg-surface-bg/95 backdrop-blur-md border-b border-border-subtle">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 md:h-20 flex items-center justify-between gap-4" aria-label="Navigasi utama">
        <!-- Brand Logo & Career School Text -->
        <a href="/beranda" data-nav="beranda" class="flex items-center gap-2.5 shrink-0 group focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-primary rounded-input">
            <img
                src="{{ asset('images/logos/logo-career-school.png') }}"
                alt="Logo CareerSchool"
                class="h-8 md:h-9 w-auto rounded-lg object-contain shadow-xs border border-border-subtle/70 transition-transform group-hover:scale-105 duration-150"
            >
            <span class="text-sm md:text-base font-bold font-heading text-text-primary tracking-tight whitespace-nowrap">
                Career School
            </span>
        </a>

        <!-- Navigation Links: Clean Section Routes -->
        <div class="hidden lg:flex items-center gap-4 xl:gap-5 text-xs md:text-sm font-medium text-text-secondary whitespace-nowrap">
            <a href="/beranda" data-nav="beranda" class="hover:text-text-primary transition-colors py-1">Beranda</a>
            <a href="/platform" data-nav="platform" class="hover:text-text-primary transition-colors py-1">Platform</a>
            <a href="/sekolah" data-nav="sekolah" class="hover:text-text-primary transition-colors py-1">Sekolah</a>
            <a href="/fitur-unggulan" data-nav="fitur-unggulan" class="hover:text-text-primary transition-colors py-1">Fitur</a>
            <a href="{{ route('public.announcements') }}" class="hover:text-text-primary transition-colors py-1">Pengumuman</a>
            <a href="{{ route('public.agendas') }}" class="hover:text-text-primary transition-colors py-1">Agenda</a>
            <a href="{{ route('alumni.index') }}" class="hover:text-text-primary transition-colors py-1">Alumni</a>
            @auth
                <a href="{{ route('feed.index') }}" class="text-brand-secondary dark:text-brand-primary font-bold hover:underline py-1">Komunitas</a>
            @endauth
            <a href="/faq" data-nav="faq" class="hover:text-text-primary transition-colors py-1">FAQ</a>
        </div>

        <!-- Action Buttons: Proportional, Compact, Strict Button Rules -->
        <div class="flex items-center gap-2 sm:gap-2.5 shrink-0 whitespace-nowrap">
            <!-- Theme Toggle Button (Icon-Only, Prominent Icon) -->
            <button
                type="button"
                onclick="window.toggleTheme()"
                aria-label="Ubah tema gelap atau terang"
                class="w-10 h-10 rounded-btn border border-border-subtle bg-surface-card text-text-primary hover:border-text-primary transition-all duration-150 active:scale-[0.96] flex items-center justify-center shadow-xs"
            >
                <span class="dark:hidden flex items-center justify-center">
                    <x-icon name="moon" class="w-5 h-5" />
                </span>
                <span class="hidden dark:flex items-center justify-center">
                    <x-icon name="sun" class="w-5 h-5" />
                </span>
            </button>

            <!-- Auth Actions (Masuk & Daftar) -->
            @guest
                <a
                    href="{{ route('login') }}"
                    class="px-4 sm:px-5 py-2 text-xs md:text-sm font-semibold rounded-btn bg-surface-card border border-border-default/80 hover:border-text-primary text-text-primary shadow-xs transition-all duration-150 active:scale-[0.96]"
                >
                    Masuk
                </a>
                <a
                    href="{{ route('register') }}"
                    class="px-5 py-2 text-xs md:text-sm font-semibold rounded-btn bg-brand-primary text-text-on-brand-primary hover:brightness-105 shadow-xs transition-all duration-150 active:scale-[0.96]"
                >
                    Daftar
                </a>
            @else
                <div class="flex items-center gap-2 sm:gap-3">
                    <span class="text-xs md:text-sm font-semibold text-text-primary max-w-[120px] sm:max-w-none truncate">
                        {{ auth()->user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="px-3.5 sm:px-4 py-2 text-xs md:text-sm font-semibold rounded-btn bg-surface-card border border-border-default hover:border-red-500 hover:text-red-500 text-text-secondary transition-all duration-150 active:scale-[0.96]"
                        >
                            Keluar
                        </button>
                    </form>
                </div>
            @endguest
        </div>
    </nav>
</header>
