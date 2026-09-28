<section id="sekolah" class="py-16 md:py-20 bg-surface-bg scroll-mt-20">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 space-y-16">
        
        <!-- 1. Header Profil & Google Maps Embed -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            <!-- Left Info -->
            <div class="lg:col-span-6 space-y-5">
                <div class="flex items-center gap-4">
                    <img
                        src="{{ asset('images/logos/logo-smk-neper.png') }}"
                        alt="Logo SMKN 1 Cirebon"
                        class="h-14 w-auto object-contain rounded-xl shadow-xs"
                    >
                    <div>
                        <h2 class="font-heading text-xl font-bold text-text-primary">SMK Negeri 1 Cirebon</h2>
                        <p class="text-xs sm:text-sm text-text-secondary">Pusat Keunggulan & Rekayasa Teknologi (NEPER)</p>
                    </div>
                </div>

                <h3 class="font-heading text-3xl sm:text-4xl lg:text-[40px] font-bold text-text-primary tracking-tight leading-[1.25]">
                    Pencetak talenta vokasi unggul dan
                    <x-highlight-pill>
                        siap kerja.
                    </x-highlight-pill>
                </h3>

                <p class="text-sm sm:text-base text-text-secondary leading-relaxed">
                    SMK Negeri 1 Cirebon berkomitmen menghasilkan lulusan berkarakter, terampil, dan berdaya saing global melalui kurikulum berbasis industri, sarana laboratorium modern, dan kemitraan strategis dengan puluhan perusahaan terkemuka.
                </p>

                <div class="space-y-2 text-xs sm:text-sm text-text-secondary pt-1">
                    <p><strong class="text-text-primary">Alamat:</strong> Jl. Perjuangan, Sunyaragi, Kec. Kesambi, Kota Cirebon, Jawa Barat 45132</p>
                    <p><strong class="text-text-primary">Telepon:</strong> (0231) 480202</p>
                    <p><strong class="text-text-primary">Email:</strong> info@smkn1-cirebon.sch.id</p>
                </div>

                <!-- Text-Only Action Buttons (Comfortable Size & Readable Text) -->
                <div class="pt-2 flex flex-wrap gap-4">
                    <a
                        href="https://www.smkn1-cirebon.sch.id/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center px-6 py-3.5 text-sm font-semibold rounded-btn bg-surface-card border-2 border-border-default hover:border-text-primary text-text-primary shadow-xs transition-all duration-150 active:scale-[0.96]"
                    >
                        Kunjungi Website Resmi Sekolah
                    </a>
                    <a
                        href="https://maps.app.goo.gl/JL2x1fbGjpY7x53e6"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center px-6 py-3.5 text-sm font-semibold rounded-btn bg-brand-primary text-neutral-900 hover:brightness-105 shadow-xs transition-all duration-150 active:scale-[0.96]"
                    >
                        Buka di Google Maps
                    </a>
                </div>
            </div>

            <!-- Right: Interactive Google Maps Embed -->
            <div class="lg:col-span-6">
                <div class="rounded-card bg-surface-card border border-border-subtle p-3.5 shadow-md">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3962.296522858189!2d108.53724647573683!3d-6.733621465839217!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6ee2635a96e67b%3A0x6b306b940989f5ce!2sSMK%20Negeri%201%20Cirebon!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"
                        class="w-full h-72 sm:h-80 rounded-xl border border-border-subtle/70"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Lokasi SMK Negeri 1 Cirebon di Google Maps"
                    ></iframe>
                    <div class="pt-3 pb-1 text-center">
                        <span class="text-xs text-text-secondary">
                            Jl. Perjuangan, Sunyaragi, Kec. Kesambi, Kota Cirebon, Jawa Barat 45132
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Interactive Navigation Filter & Cards Showcase -->
        <div x-data="{ activeTab: 'semua' }" class="space-y-10 pt-4">
            
            <!-- Filter Bar & Section Heading -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="space-y-2">
                    <h3 class="font-heading text-2xl sm:text-3xl font-bold text-text-primary tracking-tight">
                        <x-wavy-underline>Program Keahlian</x-wavy-underline> & Ekstrakurikuler
                    </h3>
                    <p class="text-sm text-text-secondary max-w-xl">
                        Membina kompetensi kejuruan aplikatif berstandar industri dan mengasah karakter kepemimpinan siswa.
                    </p>
                </div>

                <!-- Tab Filter Buttons -->
                <div class="flex items-center gap-2 p-1.5 rounded-2xl bg-surface-card border border-border-subtle shadow-xs self-start md:self-auto">
                    <button
                        type="button"
                        @click="activeTab = 'semua'"
                        :class="activeTab === 'semua' ? 'bg-brand-primary text-neutral-900 font-bold shadow-xs' : 'text-text-secondary hover:text-text-primary font-medium'"
                        class="px-4 py-2 text-xs sm:text-sm rounded-xl transition-all duration-150"
                    >
                        Semua (16)
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'jurusan'"
                        :class="activeTab === 'jurusan' ? 'bg-brand-primary text-neutral-900 font-bold shadow-xs' : 'text-text-secondary hover:text-text-primary font-medium'"
                        class="px-4 py-2 text-xs sm:text-sm rounded-xl transition-all duration-150"
                    >
                        Jurusan (10)
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'eskul'"
                        :class="activeTab === 'eskul' ? 'bg-brand-primary text-neutral-900 font-bold shadow-xs' : 'text-text-secondary hover:text-text-primary font-medium'"
                        class="px-4 py-2 text-xs sm:text-sm rounded-xl transition-all duration-150"
                    >
                        Ekstrakurikuler (6)
                    </button>
                </div>
            </div>

            <!-- Bagian A: 10 Program Keahlian -->
            <div x-show="activeTab === 'semua' || activeTab === 'jurusan'" class="space-y-6">
                <div class="flex items-center justify-between">
                    <h4 class="font-heading text-lg sm:text-xl font-bold text-text-primary flex items-center gap-3">
                        <span>10 Program Keahlian</span>
                        <span class="text-xs px-2.5 py-1 rounded-full bg-surface-card border border-border-default/40 text-text-secondary font-medium">
                            Kurikulum Industri & BNSP
                        </span>
                    </h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <!-- 1. RPL -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-neutral-900 text-white flex items-center justify-center font-mono font-bold text-base shadow-xs shrink-0 border border-neutral-700">
                                    &lt;/&gt;
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary leading-snug">
                                    Rekayasa Perangkat Lunak
                                </h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Jurusan Rekayasa Perangkat Lunak (RPL) di SMK mengajarkan pembuatan software, aplikasi, dan website. Siswa dibekali coding, desain UI/UX, dan basis data. Lulusan siap bekerja di bidang IT sebagai programmer, developer, atau teknisi software.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>Full-Stack & Mobile</span>
                            <span>Lab Komputer Modern</span>
                        </div>
                    </div>

                    <!-- 2. TKJ -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shadow-xs shrink-0 border border-blue-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                                        <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                                        <line x1="6" y1="6" x2="6.01" y2="6"></line>
                                        <line x1="6" y1="18" x2="6.01" y2="18"></line>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary leading-snug">
                                    Teknik Komputer & Jaringan
                                </h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Jurusan Teknik Komputer dan Jaringan (TKJ) di SMK membekali siswa dengan keahlian merakit komputer, mengelola jaringan, dan sistem operasi. Lulusan siap kerja sebagai teknisi jaringan, admin server, atau IT support di berbagai bidang industri.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>MikroTik & Cisco</span>
                            <span>Server & Cloud Lab</span>
                        </div>
                    </div>

                    <!-- 3. Teknik Elektro -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shadow-xs shrink-0 border border-teal-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                                        <rect x="9" y="9" width="6" height="6"></rect>
                                        <line x1="9" y1="1" x2="9" y2="4"></line>
                                        <line x1="15" y1="1" x2="15" y2="4"></line>
                                        <line x1="9" y1="20" x2="9" y2="23"></line>
                                        <line x1="15" y1="20" x2="15" y2="23"></line>
                                        <line x1="20" y1="9" x2="23" y2="9"></line>
                                        <line x1="20" y1="14" x2="23" y2="14"></line>
                                        <line x1="1" y1="9" x2="4" y2="9"></line>
                                        <line x1="1" y1="14" x2="4" y2="14"></line>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary leading-snug">
                                    Teknik Elektro
                                </h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Jurusan Teknik Elektro di SMK membekali siswa dengan keterampilan instalasi listrik, perawatan peralatan elektronik, dan sistem kontrol. Lulusan siap bekerja di bidang kelistrikan, teknisi elektronik, atau melanjutkan ke jenjang pendidikan lebih tinggi.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>Sistem Kontrol</span>
                            <span>Teknisi Elektronika</span>
                        </div>
                    </div>

                    <!-- 4. Teknik Mesin -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shadow-xs shrink-0 border border-amber-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="3"></circle>
                                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary leading-snug">
                                    Teknik Mesin
                                </h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Jurusan Teknik Pemesinan SMK membekali siswa dengan keterampilan mengoperasikan mesin bubut, frais, gerinda, hingga CNC. Cocok untuk yang tertarik di bidang manufaktur dan mekanik. Lulusannya siap kerja di industri teknik dan permesinan.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>Operator & CNC</span>
                            <span>Bengkel Bubut & Frais</span>
                        </div>
                    </div>

                    <!-- 5. Teknik Otomotif -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center shadow-xs shrink-0 border border-red-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m12 14 4-4"></path>
                                        <path d="M3.34 19a10 10 0 1 1 17.32 0"></path>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary leading-snug">
                                    Teknik Otomotif
                                </h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Jurusan Teknik Otomotif di SMK mengajarkan perawatan, perbaikan, dan modifikasi kendaraan bermotor. Siswa dibekali ilmu mesin, kelistrikan, dan sistem kendaraan. Lulusan siap bekerja di bengkel, industri otomotif, atau membuka usaha sendiri.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>Perawatan Mesin</span>
                            <span>Mekanik Kendaraan</span>
                        </div>
                    </div>

                    <!-- 6. Teknik Ketenagalistrikan -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 flex items-center justify-center shadow-xs shrink-0 border border-yellow-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary leading-snug">
                                    Teknik Ketenagalistrikan
                                </h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Jurusan Ketenagalistrikan di SMK membekali siswa dengan keterampilan instalasi listrik, perawatan sistem tenaga, dan penggunaan alat listrik. Lulusan siap bekerja sebagai teknisi listrik, instalator, atau teknisi gardu distribusi industri.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>Instalasi Daya & Panel</span>
                            <span>Instalator Bersertifikat</span>
                        </div>
                    </div>

                    <!-- 7. DPIB -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shadow-xs shrink-0 border border-indigo-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m18 16 4-4-4-4"></path>
                                        <path d="m6 8-4 4 4 4"></path>
                                        <path d="m14.5 4-5 16"></path>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary leading-snug">
                                    Desain Pemodelan & Informasi Bangunan
                                </h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Jurusan Desain Pemodelan dan Informasi Bangunan (DPIB) di SMK mengajarkan gambar teknik, desain bangunan, serta perencanaan konstruksi. Siswa dilatih menggunakan software desain seperti AutoCAD, BIM, dan SketchUp untuk proyek arsitektur modern.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>AutoCAD & BIM Modeling</span>
                            <span>Drafter Konstruksi</span>
                        </div>
                    </div>

                    <!-- 8. TOI -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-xs shrink-0 border border-emerald-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                                        <circle cx="12" cy="5" r="2"></circle>
                                        <path d="M12 7v4"></path>
                                        <line x1="8" y1="16" x2="8.01" y2="16"></line>
                                        <line x1="16" y1="16" x2="16.01" y2="16"></line>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary leading-snug">
                                    Teknik Otomasi Industri
                                </h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Jurusan TOI mempelajari sistem otomasi dunia industri: pengendalian mesin dan proses produksi berbasis teknologi. Siswa dibekali keterampilan mengoperasikan, merancang, dan memelihara sistem otomatis menggunakan PLC, sensor, motor listrik, dan sistem kontrol.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>PLC & Smart Sensors</span>
                            <span>Teknisi Robotik & Otomasi</span>
                        </div>
                    </div>

                    <!-- 9. TBKR -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shadow-xs shrink-0 border border-purple-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m14.622 17.897-1.069-1.069a1.5 1.5 0 0 1 0-2.121l5.524-5.524a1.5 1.5 0 0 1 2.121 0l1.069 1.069a1.5 1.5 0 0 1 0 2.121l-5.524 5.524a1.5 1.5 0 0 1-2.121 0Z"></path>
                                        <path d="M4 20h3l10-10-3-3L4 17v3Z"></path>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary leading-snug">
                                    Teknik Bodi Kendaraan Ringan
                                </h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Jurusan Teknik Bodi Kendaraan Ringan (TBKR) di SMK Negeri membekali siswa dengan keahlian perbaikan bodi mobil, pengecatan presisi, dan pengelasan modern. Lulusan siap kerja di bengkel karoseri, authorized body repair, atau membuka usaha mandiri.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>Oven Painting & Welding</span>
                            <span>Spesialis Bodi Karoseri</span>
                        </div>
                    </div>

                    <!-- 10. TPTU -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between md:col-span-2 lg:col-span-1">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shadow-xs shrink-0 border border-sky-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="2" y1="12" x2="22" y2="12"></line>
                                        <line x1="12" y1="2" x2="12" y2="22"></line>
                                        <path d="m20 16-4-4 4-4"></path>
                                        <path d="m4 8 4 4-4 4"></path>
                                        <path d="m16 4-4 4-4-4"></path>
                                        <path d="m8 20 4-4 4 4"></path>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary leading-snug">
                                    Teknik Pendingin & Tata Udara
                                </h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Jurusan Teknik Pendinginan dan Tata Udara (TPTU) di SMK Negeri membekali siswa dengan keahlian instalasi, perawatan, dan perbaikan sistem pendingin seperti AC split, AC sentral gedung, dan lemari es industri. Lulusan siap kerja di industri HVAC atau membuka jasa servis mandiri.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>Sistem HVAC Komersial</span>
                            <span>Teknisi Pendingin Industri</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Bagian B: 6 Ekstrakurikuler -->
            <div x-show="activeTab === 'semua' || activeTab === 'eskul'" class="space-y-6 pt-4">
                <div class="flex items-center justify-between">
                    <h4 class="font-heading text-lg sm:text-xl font-bold text-text-primary flex items-center gap-3">
                        <span>6 Ekstrakurikuler Unggulan</span>
                        <span class="text-xs px-2.5 py-1 rounded-full bg-surface-card border border-border-default/40 text-text-secondary font-medium">
                            Pengembangan Minat, Bakat & Prestasi
                        </span>
                    </h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <!-- 1. Paskibra -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shadow-xs shrink-0 border border-rose-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                                        <line x1="4" y1="22" x2="4" y2="15"></line>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary">Paskibra</h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Ekstrakurikuler Paskibra melatih kedisiplinan, kepemimpinan, dan cinta tanah air melalui latihan baris-berbaris dan pengibaran bendera. Anggotanya berperan penting dalam upacara dan aktif mengikuti lomba baris-berbaris tingkat sekolah hingga nasional.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>Capaska Kota Cirebon</span>
                            <span>Juara Baris Berbaris</span>
                        </div>
                    </div>

                    <!-- 2. Marching Band -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center shadow-xs shrink-0 border border-orange-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 18V5l12-2v13"></path>
                                        <circle cx="6" cy="18" r="3"></circle>
                                        <circle cx="18" cy="16" r="3"></circle>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary">Marching Band</h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Marching Band mengajarkan kekompakan, disiplin, dan seni musik melalui permainan alat musik tiup, perkusi, dan gerakan formasi. Siswa dilatih tampil harmonis dalam tim dan sering tampil di acara sekolah, lomba, serta pawai peringatan hari besar.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>Gita Swara Neper</span>
                            <span>Parade & Konser Musik</span>
                        </div>
                    </div>

                    <!-- 3. Pramuka -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center shadow-xs shrink-0 border border-violet-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary">Pramuka</h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Ekstrakurikuler Pramuka melatih kemandirian, kerja sama, disiplin, dan kepemimpinan siswa melalui kegiatan berkemah, tali-temali, baris-berbaris, dan berbagai permainan edukatif. Pramuka juga menanamkan nilai kebangsaan dan cinta lingkungan.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>Gugus Depan Pangkalan</span>
                            <span>Pramuka Penegak Garuda</span>
                        </div>
                    </div>

                    <!-- 4. Pencak Silat -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-stone-500/10 text-stone-700 dark:text-stone-300 flex items-center justify-center shadow-xs shrink-0 border border-stone-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary">Pencak Silat</h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Ekstrakurikuler Pencak Silat di SMK melatih siswa dalam seni bela diri tradisional Indonesia. Kegiatan ini mengembangkan kedisiplinan, ketangkasan, dan sportivitas, serta membentuk karakter tangguh dan percaya diri melalui latihan rutin dan kompetisi.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>Seni Bela Diri Nusantara</span>
                            <span>Kejurda & O2SN</span>
                        </div>
                    </div>

                    <!-- 5. PMR -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center shadow-xs shrink-0 border border-red-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary">PMR (Palang Merah Remaja)</h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Ekstrakurikuler PMR di SMK melatih siswa dalam keterampilan pertolongan pertama, kepedulian sosial, dan kesiapsiagaan bencana. Siswa dilatih untuk sigap membantu sesama, aktif dalam kegiatan kemanusiaan, dan berperan sebagai relawan muda yang tangguh.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>PMR Wira Unit Neper</span>
                            <span>Relawan Siaga Bencana</span>
                        </div>
                    </div>

                    <!-- 6. Panahan -->
                    <div class="rounded-card bg-surface-card border border-border-subtle p-6 space-y-4 hover:border-border-default hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shadow-xs shrink-0 border border-teal-500/20">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <circle cx="12" cy="12" r="6"></circle>
                                        <circle cx="12" cy="12" r="2"></circle>
                                    </svg>
                                </div>
                                <h5 class="font-heading text-base font-bold text-text-primary">Panahan (Archery)</h5>
                            </div>
                            <p class="text-xs sm:text-sm text-text-secondary leading-relaxed">
                                Ekstrakurikuler Panahan di SMK melatih fokus, ketenangan, dan ketepatan. Siswa diajarkan teknik dasar hingga mahir dalam memanah, sekaligus menanamkan sportivitas, disiplin, dan ketangguhan mental melalui latihan rutin dan kompetisi.
                            </p>
                        </div>
                        <div class="pt-3 flex items-center justify-between text-[11px] text-text-secondary">
                            <span>Archery Club Neper</span>
                            <span>Turnamen Panahan Pelajar</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</section>
