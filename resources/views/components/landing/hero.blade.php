@props(['stats' => []])

<section id="beranda" class="relative min-h-[calc(100vh-5rem)] flex items-center py-12 md:py-16 overflow-hidden bg-surface-bg scroll-mt-20">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
            
            <!-- Hero Text Content (Left) -->
            <div class="lg:col-span-6 space-y-6">
                <!-- Main Heading (Persuasive, Clear, with Brand Green Highlight Block) -->
                <h1 class="font-heading text-3xl sm:text-4xl lg:text-[46px] font-bold text-text-primary tracking-tight leading-[1.28] sm:leading-[1.3] lg:leading-[1.32]">
                    Cari kerja dan magang dari
                    <x-highlight-pill>
                        karya nyata
                    </x-highlight-pill>
                    yang divalidasi sekolah.
                </h1>

                <!-- Clear, Human, and Natural Copywriting -->
                <p class="text-base sm:text-lg text-text-secondary leading-relaxed font-normal">
                    Career School memadukan media sosial portofolio siswa SMKN 1 Cirebon dengan bursa kerja industri. Bagikan proyek praktikummu, dapatkan validasi resmi dari guru kejuruan, dan lamar lowongan mitra DUDI yang cocok dengan keahlianmu.
                </p>

                <!-- CTA Actions (Proportional, Compact, Strictly Text-Only) -->
                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <a
                        href="{{ route('register') }}"
                        class="px-5 py-2.5 rounded-btn bg-brand-primary text-text-on-brand-primary font-semibold text-sm hover:brightness-105 shadow-sm transition-all duration-150 active:scale-[0.96]"
                    >
                        Mulai Aktivasi NISN
                    </a>
                    <a
                        href="{{ route('login') }}"
                        class="px-5 py-2.5 rounded-btn bg-surface-card border border-border-default hover:border-text-primary text-text-primary font-semibold text-sm transition-all duration-150 active:scale-[0.96] shadow-xs"
                    >
                        Portal Perusahaan Mitra
                    </a>
                </div>

                <!-- Proof Metrics Bar (Live Realtime Data from Database) -->
                <div class="pt-8 border-t border-border-subtle grid grid-cols-3 gap-6 text-left">
                    <div>
                        <span class="block font-heading text-2xl font-bold text-text-primary">
                            {{ $stats['majors_count'] ?? 3 }} Jurusan
                        </span>
                        <span class="text-xs text-text-secondary">PPLG, TJKT, dan DKV</span>
                    </div>
                    <div>
                        <span class="block font-heading text-2xl font-bold text-text-primary">
                            {{ $stats['skills_count'] ?? 12 }}+ Keahlian
                        </span>
                        <span class="text-xs text-text-secondary">Standar kompetensi terdata</span>
                    </div>
                    <div>
                        <span class="block font-heading text-2xl font-bold text-text-primary">
                            {{ $stats['jobs_count'] ?? 0 }} Lowongan
                        </span>
                        <span class="text-xs text-text-secondary">Peluang magang & kerja DUDI</span>
                    </div>
                </div>
            </div>

            <!-- Hero Visual Stage (Right): Dynamic Composition of Multiple Figma Assets with Uniform Neutral Typography -->
            <div class="lg:col-span-6 relative">
                <div class="relative w-full max-w-lg mx-auto">
                    
                    <!-- Background Soft Halo -->
                    <div class="absolute inset-0 bg-lime-200/25 dark:bg-lime-900/10 rounded-3xl blur-2xl -z-10"></div>

                    <!-- Main Composition Container -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 sm:p-7 shadow-lg relative overflow-hidden">
                        
                        <!-- Top Item: Teacher Validation Asset (Uniform Neutral Styling) -->
                        <div class="flex items-center justify-between gap-4 pb-5 border-b border-border-subtle">
                            <div class="space-y-1">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-text-secondary">
                                    Validasi Kompetensi
                                </span>
                                <h3 class="font-heading text-base font-bold text-text-primary">
                                    Rekomendasi Guru Kejuruan
                                </h3>
                                <p class="text-xs text-text-secondary">
                                    Tingkat keahlian 1–5 dinilai langsung di kelas praktikum
                                </p>
                            </div>
                            <div class="w-24 sm:w-28 h-20 shrink-0 flex items-center justify-center text-text-primary">
                                <x-illustration name="teacher-reviewing" class="h-full w-auto text-text-primary" />
                            </div>
                        </div>

                        <!-- Center Item: Student at Laptop Asset (Uniform Neutral Styling) -->
                        <div class="py-5 flex flex-col sm:flex-row items-center justify-between gap-6 border-b border-border-subtle">
                            <div class="w-36 sm:w-44 h-32 shrink-0 flex items-center justify-center text-text-primary">
                                <x-illustration name="student-laptop" class="h-full w-auto text-text-primary" />
                            </div>
                            <div class="space-y-1.5 text-left sm:text-right">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-text-secondary">
                                    Portofolio Siswa
                                </span>
                                <h4 class="font-heading text-base font-bold text-text-primary">
                                    Karya Asli Melekat di Akun
                                </h4>
                                <p class="text-xs text-text-secondary max-w-xs">
                                    Repositori GitHub, desain antarmuka, konfigurasi server, dan berkas PDF terpadu.
                                </p>
                            </div>
                        </div>

                        <!-- Bottom Item: Company Handshake Asset (Uniform Neutral Styling) -->
                        <div class="pt-5 flex items-center justify-between gap-4">
                            <div class="space-y-1">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-text-secondary">
                                    Koneksi Industri
                                </span>
                                <h4 class="font-heading text-base font-bold text-text-primary">
                                    Peluang Magang & Kerja DUDI
                                </h4>
                                <p class="text-xs text-text-secondary">
                                    Perekrutan transparan berbasis skor kecocokan tertinggi
                                </p>
                            </div>
                            <div class="w-24 sm:w-28 h-20 shrink-0 flex items-center justify-center text-text-primary">
                                <x-illustration name="company-handshake" class="h-full w-auto text-text-primary" />
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
