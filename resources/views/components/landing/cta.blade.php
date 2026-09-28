<section class="py-16 md:py-20 bg-surface-bg">
    <div class="max-w-4xl mx-auto px-6 text-center space-y-6">
        <h2 class="font-heading text-3xl sm:text-4xl lg:text-[40px] font-bold text-text-primary tracking-tight leading-[1.25]">
            Siap membuktikan keahlianmu ke <x-highlight-pill>dunia kerja?</x-highlight-pill>
        </h2>
        <p class="text-base text-text-secondary max-w-xl mx-auto leading-relaxed">
            Masukkan NISN kamu yang sudah terdaftar di basis data sekolah, atur kata sandi baru, dan mulailah membangun portofolio karier vokasimu hari ini.
        </p>
        <div class="flex flex-wrap justify-center gap-4 pt-2">
            <!-- Text-Only Button -->
            <a
                href="{{ route('register') }}"
                class="px-7 py-3.5 rounded-btn bg-brand-primary text-text-on-brand-primary font-semibold text-base hover:brightness-105 shadow-md shadow-brand-primary/20 transition-all duration-150 active:scale-[0.96]"
            >
                Mulai Aktivasi NISN Sekarang
            </a>
            <!-- Text-Only Button -->
            <a
                href="{{ route('login') }}"
                class="px-7 py-3.5 rounded-btn bg-surface-card border border-border-default hover:border-text-primary text-text-primary font-semibold text-base transition-all duration-150 active:scale-[0.96] shadow-xs"
            >
                Sudah Punya Akun? Masuk
            </a>
        </div>
    </div>
</section>
