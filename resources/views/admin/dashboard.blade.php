@extends('layouts.admin')

@section('title', 'Dashboard Utama Administrator')

@section('content')
<div class="space-y-8">
    <!-- 1. Page Header Title & Academic Context -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-heading font-bold text-text-primary tracking-tight">
                Dashboard utama administrator
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Pusat kendali vokasi, analitik penyerapan kerja lulusan, dan monitoring data master SMK Negeri 1 Cirebon.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-border-subtle bg-surface-card text-xs font-medium text-text-secondary shadow-xs">
                <span class="w-2 h-2 rounded-full bg-state-success-text animate-pulse"></span>
                <span>Tahun ajaran 2025/2026 &bull; Ganjil</span>
            </span>
        </div>
    </div>

    <!-- 2. KPI Stat Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <x-ui.stat-card 
            title="Total Siswa Terdaftar" 
            value="{{ number_format($stats['students_count']) }}" 
            icon="graduation-cap" 
            trend="+8.4%" 
            :trendUp="true"
        />

        <x-ui.stat-card 
            title="Guru & tenaga pendidik" 
            value="{{ number_format($stats['teachers_count']) }}" 
            icon="user-check" 
            trend="Aktif mengajar" 
            :trendUp="true"
        />

        <x-ui.stat-card 
            title="Mitra industri (DUDI)" 
            value="{{ number_format($stats['companies_count']) }}" 
            icon="building-2" 
            trend="+1 Rekanan" 
            :trendUp="true"
        />

        <x-ui.stat-card 
            title="Antrean verifikasi mitra" 
            value="{{ number_format($stats['pending_companies_count']) }}" 
            icon="shield-alert" 
            trend="{{ $stats['pending_companies_count'] > 0 ? 'Perlu ditinjau' : 'Semua bersih' }}" 
            :trendUp="$stats['pending_companies_count'] === 0"
        />
    </div>

    <!-- 3. Asymmetric Bento Grid (2 Columns: Left 5 Cols, Right 7 Cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        
        <!-- LEFT COLUMN (5 Cols): Operasional, Distribusi Jurusan & Target BMW -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Card A: Distribusi Siswa per Program Keahlian -->
            <x-ui.card 
                title="Distribusi program keahlian" 
                subtitle="Komposisi siswa dan rombel kelas per jurusan vokasi"
            >
                <div class="space-y-4">
                    @php
                        $totalSiswa = $stats['students_count'] ?: 1;
                    @endphp

                    @forelse ($majorsBreakdown as $major)
                        @php
                            $percentage = round(($major->students_count / $totalSiswa) * 100);
                        @endphp
                        <div class="p-3.5 rounded-btn bg-surface-bg border border-border-subtle space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-btn font-mono font-bold text-[10px] bg-brand-primary text-text-on-brand-primary">
                                        {{ $major->kode }}
                                    </span>
                                    <span class="font-heading font-semibold text-text-primary truncate max-w-[170px] sm:max-w-[210px]" title="{{ $major->nama }}">
                                        {{ $major->nama }}
                                    </span>
                                </div>
                                <span class="font-mono text-text-secondary font-medium">
                                    {{ $major->students_count }} Siswa
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full h-2 rounded-full bg-neutral-200 dark:bg-neutral-700/80 overflow-hidden">
                                <div 
                                    class="h-full bg-brand-primary rounded-full transition-all duration-500" 
                                    style="width: {{ max($percentage, 8) }}%"
                                ></div>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-text-muted">
                                <span>{{ $major->classes_count }} Rombongan belajar</span>
                                <span>{{ $percentage }}% dari total siswa</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-text-muted">
                            Belum ada data program keahlian.
                        </div>
                    @endforelse
                </div>
            </x-ui.card>

            <!-- Card B: Target Capaian BMW -->
            <x-ui.card 
                title="Target penyerapan BMW" 
                subtitle="Sasaran keterserapan tamatan vokasi SMKN 1 Cirebon"
            >
                <div class="space-y-4">
                    <!-- Segmented Multi-Color Bar -->
                    <div class="w-full h-3.5 rounded-btn bg-surface-bg p-0.5 flex gap-1 overflow-hidden border border-border-subtle">
                        <div class="h-full bg-brand-primary rounded-btn transition-all duration-300" style="width: 65%" title="Bekerja: 65%"></div>
                        <div class="h-full bg-brand-secondary rounded-btn transition-all duration-300" style="width: 20%" title="Melanjutkan: 20%"></div>
                        <div class="h-full bg-neutral-400 dark:bg-neutral-500 rounded-btn transition-all duration-300" style="width: 10%" title="Wirausaha: 10%"></div>
                        <div class="h-full bg-neutral-200 dark:bg-neutral-700 rounded-btn transition-all duration-300" style="width: 5%" title="Lainnya: 5%"></div>
                    </div>

                    <!-- Legend Breakdown -->
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-primary flex-shrink-0"></span>
                            <span class="text-text-secondary">Bekerja di DUDI</span>
                            <span class="ml-auto font-heading font-bold text-text-primary">65%</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-secondary flex-shrink-0"></span>
                            <span class="text-text-secondary">Kuliah / studi</span>
                            <span class="ml-auto font-heading font-bold text-text-primary">20%</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-2.5 h-2.5 rounded-full bg-neutral-400 dark:bg-neutral-500 flex-shrink-0"></span>
                            <span class="text-text-secondary">Wirausaha</span>
                            <span class="ml-auto font-heading font-bold text-text-primary">10%</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-2.5 h-2.5 rounded-full bg-neutral-200 dark:bg-neutral-700 flex-shrink-0"></span>
                            <span class="text-text-secondary">Belum terlacak</span>
                            <span class="ml-auto font-heading font-bold text-text-primary">5%</span>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <!-- Card C: Status Portal Karir Publik & Visibilitas -->
            <x-ui.card 
                title="Status portal karir publik" 
                subtitle="Keterbukaan informasi lowongan dan portofolio ke masyarakat"
            >
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3.5 rounded-btn bg-surface-bg border border-border-subtle">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-btn bg-state-success-bg text-state-success-text flex items-center justify-center">
                                <i data-lucide="globe" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <span class="font-heading font-semibold text-xs text-text-primary block">Portal web aktif</span>
                                <span class="text-[11px] text-text-secondary block">Dapat diakses publik & industri</span>
                            </div>
                        </div>
                        <x-ui.badge variant="success">Online</x-ui.badge>
                    </div>

                    <div class="pt-1">
                        <a href="{{ route('landing') }}" target="_blank" rel="noopener noreferrer" class="w-full block">
                            <x-ui.button variant="secondary" class="w-full justify-center">
                                Buka portal publik
                                <i data-lucide="external-link" class="w-3.5 h-3.5 ml-1"></i>
                            </x-ui.button>
                        </a>
                    </div>
                </div>
            </x-ui.card>

        </div>

        <!-- RIGHT COLUMN (7 Cols): Analitik Mendalam, Grafik Utama & Sub-grid -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Card 1: Grafik Utama Tren Penyerapan Lulusan Vokasi -->
            <x-ui.card 
                title="Tren Keterserapan Lulusan Vokasi SMKN 1 Cirebon" 
                subtitle="Data distribusi lulusan bekerja, kuliah, dan wirausaha (BMW)"
            >
                <div id="chart-absorption" class="h-72 w-full"></div>
            </x-ui.card>

            <!-- Sub-Grid: 2 Kartu Berdampingan (Donut Chart & Ringkasan Kemitraan) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Donut Chart: Komposisi Mitra Industri -->
                <x-ui.card 
                    title="Sektor industri rekanan" 
                    subtitle="Komposisi sektor industri DUDI"
                >
                    <div class="flex items-center justify-center">
                        <div id="chart-sectors" class="h-60 w-full flex items-center justify-center"></div>
                    </div>
                </x-ui.card>

                <!-- Mini Operasional Card: Ringkasan Kemitraan & Rombel -->
                <x-ui.card 
                    title="Rekap operasional vokasi" 
                    subtitle="Ringkasan data kemitraan aktif"
                >
                    <div class="space-y-3.5 pt-1">
                        <div class="flex items-center justify-between pb-2.5 border-b border-border-subtle">
                            <span class="text-xs text-text-secondary">Total program keahlian:</span>
                            <span class="font-heading font-bold text-xs text-text-primary font-mono">{{ $stats['majors_count'] }} Jurusan</span>
                        </div>
                        <div class="flex items-center justify-between pb-2.5 border-b border-border-subtle">
                            <span class="text-xs text-text-secondary">Rombongan belajar:</span>
                            <span class="font-heading font-bold text-xs text-text-primary font-mono">{{ $stats['classes_count'] }} Kelas</span>
                        </div>
                        <div class="flex items-center justify-between pb-2.5 border-b border-border-subtle">
                            <span class="text-xs text-text-secondary">Mitra terverifikasi:</span>
                            <span class="font-heading font-bold text-xs text-text-primary font-mono">{{ $stats['companies_count'] }} DUDI</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-text-secondary">Antrean verifikasi:</span>
                            @if ($stats['pending_companies_count'] > 0)
                                <x-ui.badge variant="warning">{{ $stats['pending_companies_count'] }} Menunggu</x-ui.badge>
                            @else
                                <x-ui.badge variant="success">Semua selesai</x-ui.badge>
                            @endif
                        </div>
                    </div>
                </x-ui.card>
            </div>

        </div>

    </div>

    <!-- 4. Bottom Section: Riwayat Aktivitas & Log Sistem Terkini -->
    <div>
        <div class="bg-surface-card border border-border-subtle rounded-card shadow-xs overflow-hidden transition-colors">
            <!-- Header Toolbar -->
            <div class="p-6 sm:p-7 border-b border-border-subtle flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-heading font-bold text-text-primary tracking-tight">
                        Riwayat Aktivitas & Pendaftaran Terkini
                    </h2>
                    <p class="text-xs text-text-secondary mt-0.5">
                        Log rekaman pembaruan mitra industri, peserta didik, dan data master sekolah secara real-time
                    </p>
                </div>

                <div class="flex items-center gap-2.5">
                    <a href="{{ route('admin.companies.index') }}">
                        <x-ui.button variant="secondary" size="sm">
                            Kelola semua mitra
                        </x-ui.button>
                    </a>
                </div>
            </div>

            <!-- Table Body -->
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-border-subtle bg-surface-bg text-[10px] font-semibold text-text-secondary uppercase tracking-wider select-none">
                            <th class="px-5 py-3.5">Kategori aktivitas</th>
                            <th class="px-5 py-3.5">Entitas & identitas</th>
                            <th class="px-5 py-3.5">Keterangan / konteks</th>
                            <th class="px-5 py-3.5">Waktu tercatat</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle text-text-primary">
                        @forelse ($recentActivities as $activity)
                            <tr class="hover:bg-surface-bg transition-colors group">
                                <!-- Column: Kategori -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-btn bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-text-secondary border border-border-subtle">
                                            <i data-lucide="{{ $activity['icon'] }}" class="w-3.5 h-3.5"></i>
                                        </div>
                                        <span class="text-xs font-semibold text-text-primary">
                                            {{ $activity['type'] }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Column: Title -->
                                <td class="px-5 py-3.5">
                                    <span class="font-heading font-semibold text-text-primary block">
                                        {{ $activity['title'] }}
                                    </span>
                                </td>

                                <!-- Column: Subtitle / Context -->
                                <td class="px-5 py-3.5 text-xs text-text-secondary">
                                    {{ $activity['subtitle'] }}
                                </td>

                                <!-- Column: Time -->
                                <td class="px-5 py-3.5 text-xs font-mono text-text-muted whitespace-nowrap">
                                    {{ $activity['time'] }}
                                </td>

                                <!-- Column: Status -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <x-ui.badge :variant="$activity['badge']">
                                        {{ $activity['status'] }}
                                    </x-ui.badge>
                                </td>

                                <!-- Column: Direct Icon Button Action -->
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <x-ui.icon-button 
                                        size="sm" 
                                        icon="eye" 
                                        label="Lihat detail log" 
                                        onclick="demoDetailAction('{{ addslashes($activity['title']) }}')"
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10">
                                    <x-ui.empty-state 
                                        title="Belum ada riwayat aktivitas"
                                        description="Belum ada transaksi atau entri data baru yang tercatat."
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize ApexCharts: Tren Keterserapan Lulusan Vokasi (Area Chart)
        const absorptionEl = document.getElementById('chart-absorption');
        if (absorptionEl && window.createSchoolChart) {
            window.createSchoolChart(absorptionEl, {
                series: [
                    { name: 'Tersalurkan kerja & wirausaha', data: [385, 440, 505, 560, 620] },
                    { name: 'Melanjutkan studi (kuliah)', data: [110, 125, 135, 140, 155] }
                ],
                colors: ['#C2F970', '#166E4F'],
                chart: {
                    type: 'area',
                    height: 280,
                    toolbar: { show: false }
                },
                xaxis: {
                    categories: ['2022', '2023', '2024', '2025', '2026'],
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) { return val + ' Org'; }
                    }
                },
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: [2.5, 2]
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 95]
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    fontSize: '11px',
                    markers: { radius: 12 }
                }
            });
        }

        // Initialize ApexCharts: Sektor Mitra Industri (Donut Chart)
        const sectorsEl = document.getElementById('chart-sectors');
        if (sectorsEl && window.createSchoolChart) {
            window.createSchoolChart(sectorsEl, {
                series: [55, 30, 15],
                labels: ['Teknologi & RPL', 'Mesin & otomotif', 'Jasa & komunikasi'],
                colors: ['#C2F970', '#166E4F', '#94A3B8'],
                chart: {
                    type: 'donut',
                    height: 240
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '72%',
                            labels: {
                                show: true,
                                name: { fontSize: '11px' },
                                value: {
                                    fontSize: '16px',
                                    fontWeight: 700,
                                    formatter: function (val) { return val + '%'; }
                                },
                                total: {
                                    show: true,
                                    label: 'Mitra DUDI',
                                    fontSize: '10px',
                                    formatter: function () { return '{{ $stats['companies_count'] }} Mitra'; }
                                }
                            }
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '11px',
                    markers: { radius: 12 }
                },
                dataLabels: { enabled: false }
            });
        }
    });

    function demoDetailAction(title) {
        if (window.notifyAlert) {
            window.notifyAlert({
                title: 'Detail aktivitas',
                text: 'Melihat rincian log sistem untuk: ' + title,
                icon: 'info',
                confirmButtonText: 'Tutup'
            });
        }
    }
</script>
@endpush
@endsection
