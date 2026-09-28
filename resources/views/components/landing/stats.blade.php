@props(['stats' => []])

<section id="statistik" class="py-20 md:py-24 bg-surface-bg scroll-mt-20">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 space-y-16">
        
        <!-- Header -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7 space-y-5">
                <h2 class="font-heading text-3xl sm:text-4xl lg:text-[40px] font-bold text-text-primary tracking-tight leading-[1.25]">
                    Statistik nyata dari
                    <x-highlight-pill>
                        ekosistem vokasi.
                    </x-highlight-pill>
                </h2>
                <p class="text-base sm:text-lg text-text-secondary leading-relaxed">
                    Data terkini yang terhubung langsung dengan basis data Career School SMK Negeri 1 Cirebon untuk memantau kesiapan karier siswa.
                </p>
            </div>

            <!-- Illustration Asset: Stats Growing -->
            <div class="lg:col-span-5 flex items-center justify-center">
                <div class="w-full max-w-sm p-6 rounded-card bg-surface-card border border-border-subtle shadow-sm flex flex-col items-center text-center space-y-3">
                    <div class="h-32 w-auto flex items-center justify-center text-text-primary">
                        <x-illustration name="stats-growing" class="h-full w-auto text-text-primary" />
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-text-secondary">
                        Pertumbuhan Kompetensi Berkelanjutan
                    </p>
                </div>
            </div>
        </div>

        <!-- Realtime Key Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-6 border-t border-border-subtle">
            <div class="rounded-card bg-surface-card border border-border-subtle p-7 space-y-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-secondary">Program Keahlian</span>
                <div class="font-heading text-4xl font-bold text-text-primary">
                    {{ $stats['majors_count'] ?? 3 }}
                </div>
                <p class="text-xs text-text-secondary">
                    Jurusan PPLG, TJKT, dan DKV terdaftar
                </p>
            </div>

            <div class="rounded-card bg-surface-card border border-border-subtle p-7 space-y-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-secondary">Standar Kompetensi</span>
                <div class="font-heading text-4xl font-bold text-text-primary">
                    {{ $stats['skills_count'] ?? 12 }}+
                </div>
                <p class="text-xs text-text-secondary">
                    Keahlian kejuruan terdata dan siap divalidasi guru
                </p>
            </div>

            <div class="rounded-card bg-surface-card border border-border-subtle p-7 space-y-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-secondary">Lowongan Terdata</span>
                <div class="font-heading text-4xl font-bold text-text-primary">
                    {{ $stats['jobs_count'] ?? 0 }}
                </div>
                <p class="text-xs text-text-secondary">
                    Peluang magang & kerja dari mitra terverifikasi
                </p>
            </div>

            <div class="rounded-card bg-surface-card border border-border-subtle p-7 space-y-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-text-secondary">Mitra Perusahaan</span>
                <div class="font-heading text-4xl font-bold text-text-primary">
                    {{ $stats['companies_count'] ?? 0 }}
                </div>
                <p class="text-xs text-text-secondary">
                    Perusahaan DUDI dalam jaringan BKK sekolah
                </p>
            </div>
        </div>

    </div>
</section>
