@extends('layouts.admin')

@section('title', 'Audit Trail & Log Aktivitas Sistem')

@section('content')
<div class="space-y-6" x-data="activityLogManager()">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-border-subtle">
        <div>
            <h1 class="text-2xl font-heading font-bold tracking-tight text-text-primary">
                Audit trail & log aktivitas sistem
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Rekam jejak operasional, peristiwa keamanan, dan riwayat tindakan pengguna di seluruh sistem CareerSchool SMKN 1 Cirebon.
            </p>
        </div>

        <!-- Quick Export Action -->
        <div class="flex items-center gap-2.5">
            <a 
                href="{{ route('admin.activity-logs.export', request()->all()) }}" 
                class="inline-flex items-center px-4 py-2 rounded-btn text-xs font-semibold bg-surface-card text-text-primary border border-border-subtle hover:bg-surface-bg active:scale-[0.96] transition-transform shadow-xs"
            >
                <i data-lucide="download" class="w-4 h-4 mr-2 text-text-muted"></i>
                <span>Ekspor log audit (CSV)</span>
            </a>
        </div>
    </div>

    <!-- 1. Stats Counter Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Log Hari Ini -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-4 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-text-secondary">Log hari ini</span>
                <div class="w-8 h-8 rounded-btn bg-state-info-bg text-state-info-text flex items-center justify-center">
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
                <div class="w-8 h-8 rounded-btn bg-neutral-100 dark:bg-neutral-800 text-text-secondary flex items-center justify-center">
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
                <div class="w-8 h-8 rounded-btn bg-brand-primary/10 text-brand-secondary dark:text-brand-primary flex items-center justify-center">
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
                <div class="w-8 h-8 rounded-btn bg-state-success-bg text-state-success-text flex items-center justify-center">
                    <i data-lucide="award" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-heading font-bold text-text-primary">
                {{ number_format($validationCount) }}
            </div>
            <p class="text-[11px] text-text-muted">Pengesahan keahlian oleh guru</p>
        </div>
    </div>

    <!-- 2. Multi-Parameter Filter Bar -->
    <div class="bg-surface-card border border-border-subtle rounded-card p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <!-- Search Keyword -->
            <div class="sm:col-span-3">
                <label for="q" class="block text-xs font-semibold text-text-secondary mb-1">
                    Cari deskripsi / pengguna / IP
                </label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-text-muted"></i>
                    <input 
                        type="text" 
                        name="q" 
                        id="q" 
                        value="{{ request('q') }}" 
                        placeholder="Cari aktivitas..."
                        class="w-full pl-9 text-xs rounded-btn border border-border-subtle bg-surface-bg text-text-primary placeholder:text-text-muted focus:ring-1 focus:ring-brand-primary"
                    >
                </div>
            </div>

            <!-- Role Filter -->
            <div class="sm:col-span-2">
                <label for="role" class="block text-xs font-semibold text-text-secondary mb-1">
                    Peran (Role)
                </label>
                <select 
                    name="role" 
                    id="role"
                    class="w-full text-xs rounded-btn border border-border-subtle bg-surface-bg text-text-primary focus:ring-1 focus:ring-brand-primary"
                >
                    <option value="">Semua peran</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="perusahaan" {{ request('role') === 'perusahaan' ? 'selected' : '' }}>Mitra industri</option>
                    <option value="guru" {{ request('role') === 'guru' ? 'selected' : '' }}>Guru kejuruan</option>
                    <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa vokasi</option>
                </select>
            </div>

            <!-- Action Category Filter -->
            <div class="sm:col-span-3">
                <label for="action" class="block text-xs font-semibold text-text-secondary mb-1">
                    Kategori aksi
                </label>
                <select 
                    name="action" 
                    id="action"
                    class="w-full text-xs rounded-btn border border-border-subtle bg-surface-bg text-text-primary focus:ring-1 focus:ring-brand-primary"
                >
                    <option value="">Semua kategori aksi</option>
                    <option value="auth.*" {{ request('action') === 'auth.*' ? 'selected' : '' }}>Autentikasi (Login / logout)</option>
                    <option value="company.*" {{ request('action') === 'company.*' ? 'selected' : '' }}>Kemitraan industri</option>
                    <option value="application.*" {{ request('action') === 'application.*' ? 'selected' : '' }}>Lamaran siswa & PKL</option>
                    <option value="skill.*" {{ request('action') === 'skill.*' ? 'selected' : '' }}>Validasi keahlian guru</option>
                    <option value="report.*" {{ request('action') === 'report.*' ? 'selected' : '' }}>Ekspor & cetak laporan BKK</option>
                </select>
            </div>

            <!-- Date Range: Start -->
            <div class="sm:col-span-2">
                <label for="start_date" class="block text-xs font-semibold text-text-secondary mb-1">
                    Tanggal mulai
                </label>
                <input 
                    type="date" 
                    name="start_date" 
                    id="start_date" 
                    value="{{ request('start_date') }}"
                    class="w-full text-xs rounded-btn border border-border-subtle bg-surface-bg text-text-primary focus:ring-1 focus:ring-brand-primary"
                >
            </div>

            <!-- Buttons -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button 
                    type="submit" 
                    class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-btn text-xs font-semibold bg-brand-primary text-text-on-brand-primary hover:brightness-105 active:scale-[0.96] transition-transform shadow-xs"
                >
                    <i data-lucide="filter" class="w-3.5 h-3.5 mr-1.5"></i>
                    <span>Terapkan</span>
                </button>

                @if (request()->hasAny(['q', 'role', 'action', 'start_date', 'end_date']))
                    <a 
                        href="{{ route('admin.activity-logs.index') }}" 
                        class="p-2 rounded-btn text-text-muted hover:text-text-primary border border-border-subtle hover:bg-surface-bg active:scale-[0.96] transition-transform"
                        title="Reset filter"
                    >
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 3. Logs Table Section -->
    <div class="bg-surface-card border border-border-subtle rounded-card shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-surface-bg border-b border-border-subtle text-text-secondary font-semibold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Waktu (WIB)</th>
                        <th class="py-3 px-4">Pengguna (Aktor)</th>
                        <th class="py-3 px-4">Aksi / kategori</th>
                        <th class="py-3 px-4">Rincian peristiwa</th>
                        <th class="py-3 px-4">Alamat IP & perangkat</th>
                        <th class="py-3 px-4 text-right">Data teknis</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-subtle">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-surface-bg transition-colors">
                            <!-- Waktu -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-heading font-semibold text-text-primary block">
                                    {{ $log->created_at->format('H:i:s') }} WIB
                                </span>
                                <span class="text-[11px] text-text-muted block" title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                    {{ $log->created_at->translatedFormat('d M Y') }} &bull; {{ $log->created_at->diffForHumans() }}
                                </span>
                            </td>

                            <!-- Aktor -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-full bg-neutral-100 dark:bg-neutral-800 border border-border-subtle flex items-center justify-center font-bold text-xs text-text-primary flex-shrink-0">
                                        {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-heading font-semibold text-text-primary truncate block">
                                            {{ $log->user->name ?? 'Sistem otomatis' }}
                                        </span>
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded-full text-[10px] font-semibold uppercase tracking-wider {{ match($log->role) {
                                            'admin' => 'bg-neutral-100 text-text-secondary dark:bg-neutral-800',
                                            'perusahaan' => 'bg-state-info-bg text-state-info-text',
                                            'guru' => 'bg-state-warning-bg text-state-warning-text',
                                            'siswa' => 'bg-state-success-bg text-state-success-text',
                                            default => 'bg-neutral-100 text-text-secondary dark:bg-neutral-800',
                                        } }}">
                                            {{ $log->role ?? 'Sistem' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $log->badge_class }}">
                                    <i data-lucide="{{ $log->icon }}" class="w-3.5 h-3.5"></i>
                                    <span>{{ $log->action_label }}</span>
                                </span>
                            </td>

                            <!-- Deskripsi -->
                            <td class="py-3 px-4 max-w-md">
                                <p class="text-xs text-text-secondary leading-relaxed">
                                    {{ $log->description }}
                                </p>
                            </td>

                            <!-- IP Address & Perangkat -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-mono text-[11px] text-text-primary block">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </span>
                                <span class="text-[10px] text-text-muted truncate block max-w-xs" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent ?? 'Internal system', 28) }}
                                </span>
                            </td>

                            <!-- Aksi Detail -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <button 
                                    type="button" 
                                    @click="openDetail({{ $log->id }})"
                                    class="inline-flex items-center px-2.5 py-1.5 rounded-btn border border-border-subtle text-text-secondary hover:text-text-primary hover:bg-surface-bg active:scale-[0.96] transition-transform text-xs font-medium"
                                >
                                    <i data-lucide="info" class="w-3.5 h-3.5 mr-1 text-text-muted"></i>
                                    <span>Detail</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-text-muted">
                                <div class="w-12 h-12 rounded-card bg-surface-bg border border-border-subtle flex items-center justify-center mx-auto text-text-muted mb-3">
                                    <i data-lucide="inbox" class="w-6 h-6"></i>
                                </div>
                                <p class="text-sm font-heading font-semibold text-text-primary">
                                    Tidak ada log aktivitas yang cocok
                                </p>
                                <p class="text-xs text-text-secondary mt-1">
                                    Belum ada catatan aktivitas yang sesuai dengan kriteria filter saat ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($logs->hasPages())
            <div class="p-4 border-t border-border-subtle flex justify-center">
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
                class="w-full max-w-xl transform overflow-hidden rounded-modal bg-surface-card border border-border-subtle p-6 text-left shadow-2xl transition-all space-y-5"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-border-subtle">
                    <div>
                        <h3 class="text-base font-heading font-bold text-text-primary" id="modal-title">
                            Rincian log audit
                        </h3>
                        <p class="text-xs text-text-muted mt-0.5" x-text="'ID Peristiwa: #LOG-' + String(detailData?.id || '').padStart(6, '0')"></p>
                    </div>
                    <button 
                        type="button" 
                        @click="modalOpen = false"
                        class="p-1 rounded-btn text-text-muted hover:text-text-primary hover:bg-surface-bg transition-colors"
                    >
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Modal Body: Key Data -->
                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3 bg-surface-bg border border-border-subtle rounded-card p-3">
                        <div>
                            <span class="text-text-muted block text-[11px]">Aktor / pengguna</span>
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
                            <span class="text-text-muted block text-[11px]">Waktu kejadian</span>
                            <span class="font-semibold text-text-primary" x-text="detailData?.created_at || '-'"></span>
                            <span class="text-text-secondary block text-[10px]" x-text="detailData?.time_ago || '-'"></span>
                        </div>
                        <div>
                            <span class="text-text-muted block text-[11px]">Alamat IP</span>
                            <span class="font-mono text-text-primary" x-text="detailData?.ip_address || '-'"></span>
                        </div>
                    </div>

                    <div>
                        <span class="text-text-muted block text-[11px] mb-1">Deskripsi lengkap</span>
                        <p class="text-text-primary bg-surface-bg border border-border-subtle rounded-card p-3" x-text="detailData?.description || '-'"></p>
                    </div>

                    <div>
                        <span class="text-text-muted block text-[11px] mb-1">Perangkat / user agent</span>
                        <p class="font-mono text-[10px] text-text-muted bg-surface-bg border border-border-subtle rounded-card p-2.5 break-all" x-text="detailData?.user_agent || '-'"></p>
                    </div>

                    <!-- JSON Properties / Metadata Diff -->
                    <template x-if="detailData?.properties">
                        <div>
                            <span class="text-text-muted block text-[11px] mb-1">Data perubahan / payload metadata (JSON)</span>
                            <pre class="bg-neutral-900 text-neutral-200 text-[11px] font-mono p-3 rounded-card overflow-x-auto max-h-48 border border-neutral-800" x-text="JSON.stringify(detailData.properties, null, 2)"></pre>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="pt-3 border-t border-border-subtle flex justify-end">
                    <button 
                        type="button" 
                        @click="modalOpen = false"
                        class="px-4 py-2 rounded-btn text-xs font-semibold bg-neutral-900 text-white dark:bg-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-100 active:scale-[0.96] transition-transform shadow-xs"
                    >
                        Tutup rincian
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

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
