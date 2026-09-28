<section id="simulator" class="py-20 bg-surface-bg">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        
        <!-- Section Header: Direct Heading, No Eyebrow -->
        <div class="max-w-3xl mx-auto text-center space-y-3 mb-12">
            <h2 class="font-heading text-3xl sm:text-4xl lg:text-[40px] font-bold text-text-primary tracking-tight leading-[1.25]">
                Uji Langsung <x-wavy-underline>Formula Job Matching</x-wavy-underline>
            </h2>
            <p class="text-base text-text-secondary">
                Pilih atribut di bawah ini dan perhatikan bagaimana sistem menghitung skor kecocokan secara transparan sesuai formula KAK Paket 15.
            </p>
        </div>

        <!-- Alpine.js Component -->
        <div
            x-data="{
                hasLaravel: true,
                hasTailwind: true,
                hasMySQL: false,
                majorMatch: 'true',
                avgSkillLevel: '4',
                portfolioCount: '3',
                locationMatch: 'true',

                get skillScore() {
                    let total = 6;
                    let acquired = 0;
                    if (this.hasLaravel) acquired += 3;
                    if (this.hasTailwind) acquired += 2;
                    if (this.hasMySQL) acquired += 1;
                    return (acquired / total) * 100;
                },
                get majorScore() {
                    return (this.majorMatch === true || this.majorMatch === 'true') ? 100 : 0;
                },
                get levelScore() {
                    return (Number(this.avgSkillLevel) / 5) * 100;
                },
                get portfolioScore() {
                    return Math.min(Number(this.portfolioCount) / 3, 1) * 100;
                },
                get locationScore() {
                    return (this.locationMatch === true || this.locationMatch === 'true') ? 100 : 0;
                },
                get totalScore() {
                    let total = (0.60 * this.skillScore) +
                                (0.15 * this.majorScore) +
                                (0.10 * this.levelScore) +
                                (0.10 * this.portfolioScore) +
                                (0.05 * this.locationScore);
                    return Math.round(total * 10) / 10;
                },
                get badgeLabel() {
                    if (this.totalScore >= 75) return 'Sangat Sesuai';
                    if (this.totalScore >= 50) return 'Cukup Sesuai';
                    return 'Perlu Peningkatan';
                },
                initSimulatorChoices() {
                    const setup = () => {
                        if (!window.Choices) {
                            setTimeout(setup, 50);
                            return;
                        }

                        const items = [
                            { id: 'sim_major_match', prop: 'majorMatch' },
                            { id: 'sim_avg_skill', prop: 'avgSkillLevel' },
                            { id: 'sim_portfolio_count', prop: 'portfolioCount' },
                            { id: 'sim_location_match', prop: 'locationMatch' }
                        ];

                        items.forEach(({ id, prop }) => {
                            const el = document.getElementById(id);
                            if (!el || el._choicesInstance) return;

                            const instance = new window.Choices(el, {
                                searchEnabled: false,
                                itemSelectText: '',
                                shouldSort: false,
                                placeholder: true,
                                allowHTML: false,
                                position: 'bottom',
                            });
                            el._choicesInstance = instance;

                            el.addEventListener('change', (e) => {
                                this[prop] = e.target.value;
                            });
                        });
                    };

                    this.$nextTick ? this.$nextTick(setup) : setTimeout(setup, 30);
                }
            }"
            x-init="initSimulatorChoices()"
            class="max-w-4xl mx-auto rounded-card bg-surface-card border border-border-subtle p-6 sm:p-8 grid grid-cols-1 md:grid-cols-12 gap-8 items-center shadow-sm"
        >
            <!-- Controls Left -->
            <div class="md:col-span-7 space-y-5">
                <h3 class="font-heading text-base font-bold text-text-primary">
                    1. Keahlian yang Dimiliki Siswa (Bobot 60%)
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <label class="flex items-center gap-2 p-3 rounded-input bg-surface-bg border border-border-subtle cursor-pointer hover:border-text-primary transition-colors text-xs font-medium">
                        <input type="checkbox" x-model="hasLaravel" class="rounded text-brand-primary focus:ring-brand-primary">
                        <span>Laravel (Bobot 3)</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-input bg-surface-bg border border-border-subtle cursor-pointer hover:border-text-primary transition-colors text-xs font-medium">
                        <input type="checkbox" x-model="hasTailwind" class="rounded text-brand-primary focus:ring-brand-primary">
                        <span>Tailwind (Bobot 2)</span>
                    </label>
                    <label class="flex items-center gap-2 p-3 rounded-input bg-surface-bg border border-border-subtle cursor-pointer hover:border-text-primary transition-colors text-xs font-medium">
                        <input type="checkbox" x-model="hasMySQL" class="rounded text-brand-primary focus:ring-brand-primary">
                        <span>MySQL (Bobot 1)</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <!-- Major Match -->
                    <div class="space-y-1.5">
                        <label for="sim_major_match" class="text-xs font-semibold text-text-primary block">
                            2. Kesesuaian Jurusan (15%)
                        </label>
                        <select id="sim_major_match" class="choices-select w-full">
                            <option value="true" selected>Sesuai (PPLG)</option>
                            <option value="false">Tidak Sesuai (Lainnya)</option>
                        </select>
                    </div>

                    <!-- Skill Level -->
                    <div class="space-y-1.5">
                        <label for="sim_avg_skill" class="text-xs font-semibold text-text-primary block">
                            3. Rata-rata Tingkat (10%)
                        </label>
                        <select id="sim_avg_skill" class="choices-select w-full">
                            <option value="5">Tingkat 5 (Mahir)</option>
                            <option value="4" selected>Tingkat 4 (Kompeten)</option>
                            <option value="3">Tingkat 3 (Menengah)</option>
                            <option value="2">Tingkat 2 (Dasar)</option>
                            <option value="1">Tingkat 1 (Pemula)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Portfolios -->
                    <div class="space-y-1.5">
                        <label for="sim_portfolio_count" class="text-xs font-semibold text-text-primary block">
                            4. Portofolio Terkait (10%)
                        </label>
                        <select id="sim_portfolio_count" class="choices-select w-full">
                            <option value="3" selected>3 Karya atau lebih (Maksimal)</option>
                            <option value="2">2 Karya terlampir</option>
                            <option value="1">1 Karya terlampir</option>
                            <option value="0">Belum ada portofolio</option>
                        </select>
                    </div>

                    <!-- Location -->
                    <div class="space-y-1.5">
                        <label for="sim_location_match" class="text-xs font-semibold text-text-primary block">
                            5. Kesediaan Lokasi (5%)
                        </label>
                        <select id="sim_location_match" class="choices-select w-full">
                            <option value="true" selected>Bersedia di Cirebon</option>
                            <option value="false">Tidak Bersedia</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Result Panel Right -->
            <div class="md:col-span-5 bg-surface-bg rounded-card border border-border-subtle p-6 text-center space-y-4">
                <span class="text-xs font-medium text-text-secondary uppercase tracking-wider block">
                    Kalkulasi Skor Otomatis
                </span>
                
                <div class="space-y-1">
                    <div class="font-heading text-5xl font-bold text-lime-700 dark:text-lime-400 tracking-tight" x-text="totalScore + '%'">
                        83.0%
                    </div>
                    <div class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                        :class="{
                            'bg-state-success-bg text-state-success-text': totalScore >= 75,
                            'bg-state-warning-bg text-state-warning-text': totalScore >= 50 && totalScore < 75,
                            'bg-state-danger-bg text-state-danger-text': totalScore < 50
                        }"
                        x-text="badgeLabel"
                    >
                        Sangat Sesuai
                    </div>
                </div>

                <!-- Mini Breakdown -->
                <div class="space-y-2 pt-3 border-t border-border-subtle text-xs text-left">
                    <div class="flex justify-between text-text-secondary">
                        <span>Komponen Keahlian:</span>
                        <span class="font-semibold text-text-primary" x-text="(0.60 * skillScore).toFixed(1) + ' pts'"></span>
                    </div>
                    <div class="flex justify-between text-text-secondary">
                        <span>Kesesuaian Jurusan:</span>
                        <span class="font-semibold text-text-primary" x-text="(0.15 * majorScore).toFixed(1) + ' pts'"></span>
                    </div>
                    <div class="flex justify-between text-text-secondary">
                        <span>Tingkat & Portofolio:</span>
                        <span class="font-semibold text-text-primary" x-text="((0.10 * levelScore) + (0.10 * portfolioScore)).toFixed(1) + ' pts'"></span>
                    </div>
                    <div class="flex justify-between text-text-secondary">
                        <span>Faktor Lokasi:</span>
                        <span class="font-semibold text-text-primary" x-text="(0.05 * locationScore).toFixed(1) + ' pts'"></span>
                    </div>
                </div>

                <div class="pt-2 text-[11px] text-text-secondary italic">
                    Rumus: 0.60×Skill + 0.15×Major + 0.10×Level + 0.10×Porto + 0.05×Loc
                </div>
            </div>
        </div>

    </div>
</section>
