<footer class="border-t border-slate-200/80 dark:border-white/10 bg-surface-card py-4 px-6 text-xs text-slate-500 dark:text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2">
    <div>
        &copy; {{ date('Y') }} Career School &mdash; Portal Kemitraan DUDI SMKN 1 Cirebon.
    </div>
    <div class="flex items-center gap-4 text-[11px]">
        <a href="{{ route('landing') }}" class="hover:text-brand-secondary dark:hover:text-brand-primary transition-colors">Beranda</a>
        <span>&bull;</span>
        <a href="{{ route('landing.section', 'faq') }}" class="hover:text-brand-secondary dark:hover:text-brand-primary transition-colors">Bantuan & FAQ</a>
        <span>&bull;</span>
        <span class="text-slate-400 dark:text-neutral-500">v1.0.0</span>
    </div>
</footer>
