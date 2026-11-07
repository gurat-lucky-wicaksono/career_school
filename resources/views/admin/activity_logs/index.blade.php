@extends('layouts.admin')

@section('title', 'Audit Trail & Log Aktivitas Sistem')

@section('content')
<div class="space-y-6" x-data="activityLogManager()">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-border-subtle">
        <div>
            <h1 class="text-2xl font-bold font-sans tracking-tight text-text-primary">
                Audit Trail & Log Aktivitas Sistem
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Rekam jejak operasional, peristiwa keamanan, dan riwayat tindakan pengguna di seluruh sistem CareerSchool SMKN 1 Cirebon.
            </p>
        </div>

        <!-- Quick Export Action -->
        <div class="flex items-center gap-2.5">
            <x-ui.button 
                variant="secondary"
                href="{{ route('admin.activity-logs.export', request()->all()) }}" 
            >
                Ekspor log audit (CSV)
            </x-ui.button>
        </div>
    </div>

    <!-- 1. Stats Counter Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Log Hari Ini -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-4 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-text-secondary">Log hari ini</span>
                <div class="w-8 h-8 rounded-full bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-heading font-bold text-text-primary">
                {{ number_format($todayCount) }}
            </div>
            <p class="text-[11px] text-text-muted">Peristiwa tercatat sejak 00:00 WIB</p>
        </div>

        <!-- Autentikasi / Login -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-4 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-text-secondary">Aktivitas autentikasi</span>
                <div class="w-8 h-8 rounded-full bg-surface-bg border border-border-default text-text-secondary flex items-center justify-center">
                    <i data-lucide="key-round" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-heading font-bold text-text-primary">
                {{ number_format($authCount) }}
            </div>
            <p class="text-[11px] text-text-muted">Riwayat sesi login & logout sistem</p>
        </div>

        <!-- Kemitraan & Lamaran -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-4 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-text-secondary">Aktivitas kemitraan & seleksi</span>
                <div class="w-8 h-8 rounded-full bg-brand-primary/10 text-brand-secondary dark:text-brand-primary flex items-center justify-center">
                    <i data-lucide="building-2" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-heading font-bold text-text-primary">
                {{ number_format($companyCount) }}
            </div>
            <p class="text-[11px] text-text-muted">Verifikasi industri & seleksi pelamar</p>
        </div>

        <!-- Validasi Keahlian Guru -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-4 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-text-secondary">Validasi kompetensi siswa</span>
                <div class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="award" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-heading font-bold text-text-primary">
                {{ number_format($validationCount) }}
            </div>
            <p class="text-[11px] text-text-muted">Pengesahan keahlian oleh guru</p>
        </div>
    </div>

    @php
        $isFilterActive = request()->filled('role') || request()->filled('action') || request()->filled('start_date');
    @endphp

    <!-- 2. Standardized Search & Filter Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="relative flex-1 sm:max-w-md">
            @if (request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            @if (request('action'))
                <input type="hidden" name="action" value="{{ request('action') }}">
            @endif
            @if (request('start_date'))
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            @endif

            <i data-lucide="search" class="w-4 h-4 text-text-secondary/70 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            <input 
                type="text" 
                name="q"
                id="q"
                value="{{ request('q') }}"
                placeholder="Cari deskripsi, pengguna, atau IP..."
                class="w-full h-11 pl-10 pr-9 text-xs sm:text-sm rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 transition-all outline-none shadow-xs font-normal"
            >
            @if (request('q'))
                <a 
                    href="{{ route('admin.activity-logs.index', request()->except('q')) }}" 
                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-text-secondary hover:text-text-primary transition-colors"
                    title="Hapus Pencarian"
                >
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2.5">
            <button 
                type="button" 
                onclick="window.openModal('modal-filter-activity-logs')"
                class="relative h-11 px-4 text-xs sm:text-sm font-semibold rounded-full border border-border-default bg-surface-card text-text-primary hover:border-text-secondary/70 hover:bg-surface-bg flex items-center gap-2 transition-all shadow-xs"
            >
                <i data-lucide="filter" class="w-4 h-4 text-text-secondary"></i>
                <span>Filter</span>
                @if ($isFilterActive)
                    <span class="w-2 h-2 rounded-full bg-brand-primary"></span>
                @endif
            </button>

            @if ($isFilterActive || request('q'))
                <a 
                    href="{{ route('admin.activity-logs.index') }}" 
                    class="h-11 px-4 text-xs font-semibold rounded-full border border-border-default bg-surface-card text-text-secondary hover:text-text-primary hover:bg-surface-bg flex items-center gap-1.5 transition-all shadow-xs"
                    title="Reset semua filter"
                >
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span>Reset</span>
                </a>
            @endif
        </div>
    </div>

    <!-- 3. Logs Table Section -->
    <div class="bg-surface-card border border-border-subtle rounded-card shadow-xs overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-surface-bg/50 border-b border-border-subtle text-text-muted font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-5">Waktu (WIB)</th>
                        <th class="py-3.5 px-5">Pengguna (Aktor)</th>
                        <th class="py-3.5 px-5">Aksi / Kategori</th>
                        <th class="py-3.5 px-5">Rincian Peristiwa</th>
                        <th class="py-3.5 px-5">Alamat IP & Perangkat</th>
                        <th class="py-3.5 px-5 text-right">Data Teknis</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-surface-bg/50 transition-colors">
                            <!-- Waktu -->
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="font-heading font-semibold text-text-primary block">
                                    {{ $log->created_at->format('H:i:s') }} WIB
                                </span>
                                <span class="text-[11px] text-text-muted block mt-0.5" title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                    {{ $log->created_at->translatedFormat('d M Y') }} &bull; {{ $log->created_at->diffForHumans() }}
                                </span>
                            </td>

                            <!-- Aktor -->
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-full bg-surface-bg border border-border-default flex items-center justify-center font-bold text-xs text-brand-secondary dark:text-brand-primary flex-shrink-0">
                                        {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-semibold text-text-primary truncate block text-xs sm:text-sm">
                                            {{ $log->user->name ?? 'Sistem otomatis' }}
                                        </span>
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded-full text-[10px] font-semibold uppercase tracking-wider mt-0.5 {{ match($log->role) {
                                            'admin' => 'bg-surface-bg text-text-secondary border border-border-subtle',
                                            'perusahaan' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400',
                                            'guru' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
                                            'siswa' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                                            default => 'bg-surface-bg text-text-secondary border border-border-subtle',
                                        } }}">
                                            {{ $log->role ?? 'Sistem' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                @php
                                    $actionPrefix = explode('.', $log->action)[0] ?? '';
                                    $badgeVariant = match($actionPrefix) {
                                        'auth' => 'secondary',
                                        'company' => 'brand',
                                        'application' => 'success',
                                        'skill' => 'warning',
                                        'report' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <x-ui.badge :variant="$badgeVariant">
                                    <i data-lucide="{{ $log->icon }}" class="w-3.5 h-3.5 mr-1 inline"></i>
                                    {{ $log->action_label }}
                                </x-ui.badge>
                            </td>

                            <!-- Deskripsi -->
                            <td class="py-3.5 px-5 max-w-md">
                                <p class="text-xs text-text-secondary leading-relaxed">
                                    {{ $log->description }}
                                </p>
                            </td>

                            <!-- IP Address & Perangkat -->
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="text-[11px] text-text-primary block font-medium">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </span>
                                <span class="text-[10px] text-text-muted truncate block max-w-xs mt-0.5" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent ?? 'Internal system', 28) }}
                                </span>
                            </td>

                            <!-- Aksi Detail -->
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <x-ui.icon-button 
                                    size="sm" 
                                    icon="info" 
                                    @click="openDetail({{ $log->id }})"
                                    label="Lihat Rincian Teknis" 
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-text-secondary text-sm">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i data-lucide="shield-alert" class="w-8 h-8 text-text-muted"></i>
                                    <p class="font-semibold text-text-primary">Tidak ada log aktivitas yang cocok</p>
                                    <p class="text-xs text-text-muted">Belum ada catatan aktivitas yang sesuai dengan kriteria filter saat ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($logs->hasPages())
            <div class="px-5 py-4 border-t border-border-subtle flex justify-center">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- 4. Detail Modal (Alpine.js) -->
    <div 
        x-show="modalOpen" 
        x-cloak 
        @keydown.escape.window="modalOpen = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
    >
        <!-- Backdrop -->
        <div 
            x-show="modalOpen" 
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm"
            @click="modalOpen = false"
        ></div>

        <!-- Modal Panel -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div 
                x-show="modalOpen" 
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-xl transform overflow-hidden rounded-card bg-surface-card border border-border-subtle p-6 text-left shadow-2xl transition-all space-y-5"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-border-subtle">
                    <div>
                        <h3 class="text-base font-heading font-bold text-text-primary" id="modal-title">
                            Rincian Log Audit
                        </h3>
                        <p class="text-xs text-text-muted mt-0.5" x-text="'ID Peristiwa: #LOG-' + String(detailData?.id || '').padStart(6, '0')"></p>
                    </div>
                    <button 
                        type="button" 
                        @click="modalOpen = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-text-muted hover:text-text-primary hover:bg-surface-bg transition-colors"
                        aria-label="Tutup Modal"
                    >
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Modal Body: Key Data -->
                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3 bg-surface-bg border border-border-subtle rounded-card p-3">
                        <div>
                            <span class="text-text-muted block text-[11px]">Aktor / Pengguna</span>
                            <span class="font-heading font-bold text-text-primary" x-text="detailData?.actor?.name || '-'"></span>
                            <span class="text-text-secondary block text-[10px]" x-text="detailData?.actor?.email || '-'"></span>
                        </div>
                        <div>
                            <span class="text-text-muted block text-[11px]">Peran (Role)</span>
                            <span class="font-heading font-bold text-brand-secondary dark:text-brand-primary capitalize" x-text="detailData?.actor?.role || '-'"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 bg-surface-bg border border-border-subtle rounded-card p-3">
                        <div>
                            <span class="text-text-muted block text-[11px]">Waktu Kejadian</span>
                            <span class="font-semibold text-text-primary" x-text="detailData?.created_at || '-'"></span>
                            <span class="text-text-secondary block text-[10px]" x-text="detailData?.time_ago || '-'"></span>
                        </div>
                        <div>
                            <span class="text-text-muted block text-[11px]">Alamat IP</span>
                            <span class="text-text-primary font-medium" x-text="detailData?.ip_address || '-'"></span>
                        </div>
                    </div>

                    <div>
                        <span class="text-text-muted block text-[11px] mb-1">Deskripsi Lengkap</span>
                        <p class="text-text-primary bg-surface-bg border border-border-subtle rounded-card p-3 leading-relaxed" x-text="detailData?.description || '-'"></p>
                    </div>

                    <div>
                        <span class="text-text-muted block text-[11px] mb-1">Perangkat / User Agent</span>
                        <p class="text-[11px] text-text-muted bg-surface-bg border border-border-subtle rounded-card p-2.5 break-all leading-normal" x-text="detailData?.user_agent || '-'"></p>
                    </div>

                    <!-- JSON Properties / Metadata Diff -->
                    <template x-if="detailData?.properties">
                        <div>
                            <span class="text-text-muted block text-[11px] mb-1">Data Perubahan / Payload Metadata (JSON)</span>
                            <pre class="bg-neutral-900 text-neutral-200 text-[11px] font-mono p-3 rounded-card overflow-x-auto max-h-48 border border-neutral-800" x-text="JSON.stringify(detailData.properties, null, 2)"></pre>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="pt-3 border-t border-border-subtle flex justify-end">
                    <button 
                        type="button" 
                        @click="modalOpen = false"
                        class="px-5 py-2.5 rounded-full text-xs sm:text-sm font-semibold border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg transition-colors"
                    >
                        Tutup Rincian
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Filter Activity Logs -->
<x-ui.modal 
    id="modal-filter-activity-logs" 
    title="Filter Log Audit Aktivitas" 
    subtitle="Saring aktivitas berdasarkan peran pengguna, kategori aksi, dan tanggal"
    size="md"
>
    <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="space-y-4">
        @if (request('q'))
            <input type="hidden" name="q" value="{{ request('q') }}">
        @endif

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Peran Pengguna (Role)
            </label>
            <select 
                name="role" 
                id="role"
                class="choices-select w-full"
            >
                <option value="">Semua Peran</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                <option value="perusahaan" {{ request('role') === 'perusahaan' ? 'selected' : '' }}>Mitra industri</option>
                <option value="guru" {{ request('role') === 'guru' ? 'selected' : '' }}>Guru kejuruan</option>
                <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa vokasi</option>
            </select>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Kategori Aksi
            </label>
            <select 
                name="action" 
                id="action"
                class="choices-select w-full"
            >
                <option value="">Semua Kategori Aksi</option>
                <option value="auth.*" {{ request('action') === 'auth.*' ? 'selected' : '' }}>Autentikasi (Login / logout)</option>
                <option value="company.*" {{ request('action') === 'company.*' ? 'selected' : '' }}>Kemitraan industri</option>
                <option value="application.*" {{ request('action') === 'application.*' ? 'selected' : '' }}>Lamaran siswa & PKL</option>
                <option value="skill.*" {{ request('action') === 'skill.*' ? 'selected' : '' }}>Validasi keahlian guru</option>
                <option value="report.*" {{ request('action') === 'report.*' ? 'selected' : '' }}>Ekspor & cetak laporan BKK</option>
            </select>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Tanggal Mulai
            </label>
            <input 
                type="date" 
                name="start_date" 
                id="start_date" 
                value="{{ request('start_date') }}"
                class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
            >
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-between gap-3">
            <a 
                href="{{ route('admin.activity-logs.index') }}" 
                class="px-4 py-2 text-xs font-semibold text-text-secondary hover:text-text-primary transition-colors"
            >
                Reset Filter
            </a>
            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    onclick="window.closeModal('modal-filter-activity-logs')"
                    class="px-5 py-2.5 text-xs sm:text-sm font-semibold rounded-full border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg transition-colors focus:outline-none"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 text-xs sm:text-sm font-semibold rounded-full bg-brand-primary text-text-on-brand-primary hover:brightness-105 transition-all shadow-md shadow-brand-primary/20 focus:outline-none"
                >
                    Terapkan Filter
                </button>
            </div>
        </div>
    </form>
</x-ui.modal>

@push('scripts')
<script>
    function activityLogManager() {
        return {
            modalOpen: false,
            detailData: null,
            loading: false,

            async openDetail(logId) {
                this.loading = true;
                this.modalOpen = true;
                this.detailData = null;

                try {
                    const response = await fetch(`/admin/activity-logs/${logId}`);
                    if (response.ok) {
                        this.detailData = await response.json();
                        this.$nextTick(() => {
                            if (window.lucide) window.lucide.createIcons();
                        });
                    }
                } catch (error) {
                    console.error('Gagal mengambil rincian log audit:', error);
                } finally {
                    this.loading = false;
                }
            }
        };
    }
</script>
@endpush
@endsection
