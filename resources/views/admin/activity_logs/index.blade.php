@extends('layouts.admin')

@section('title', 'Audit Trail & Log Aktivitas Sistem')

@section('content')
<div class="space-y-6" x-data="activityLogManager()">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-white/10">
        <div>
            <h1 class="text-2xl font-bold font-sans tracking-tight text-slate-900 dark:text-white">
                Audit Trail & Log Aktivitas Sistem
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Rekam jejak operasional, peristiwa keamanan, dan riwayat tindakan pengguna di seluruh sistem CareerSchool SMKN 1 Cirebon.
            </p>
        </div>

        <!-- Quick Export Action -->
        <div class="flex items-center gap-2.5">
            <a 
                href="{{ route('admin.activity-logs.export', request()->all()) }}" 
                class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-white dark:bg-neutral-800 text-slate-700 dark:text-slate-200 border border-slate-200/80 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-neutral-700 transition-colors shadow-sm"
            >
                <i data-lucide="download" class="w-4 h-4 mr-2 text-slate-400"></i>
                <span>Ekspor Log Audit (CSV)</span>
            </a>
        </div>
    </div>

    <!-- 1. Stats Counter Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Log Hari Ini -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-4 shadow-sm space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Log Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-sans text-slate-900 dark:text-white">
                {{ number_format($todayCount) }}
            </div>
            <p class="text-[11px] text-slate-400">Peristiwa tercatat sejak 00:00 WIB</p>
        </div>

        <!-- Autentikasi / Login -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-4 shadow-sm space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Aktivitas Autentikasi</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-300 flex items-center justify-center">
                    <i data-lucide="key-round" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-sans text-slate-900 dark:text-white">
                {{ number_format($authCount) }}
            </div>
            <p class="text-[11px] text-slate-400">Riwayat sesi login & logout sistem</p>
        </div>

        <!-- Kemitraan & Lamaran -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-4 shadow-sm space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Aktivitas Kemitraan & Seleksi</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <i data-lucide="building-2" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-sans text-slate-900 dark:text-white">
                {{ number_format($companyCount) }}
            </div>
            <p class="text-[11px] text-slate-400">Verifikasi industri & seleksi pelamar</p>
        </div>

        <!-- Validasi Keahlian Guru -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-4 shadow-sm space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Validasi Kompetensi Siswa</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i data-lucide="award" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-bold font-sans text-slate-900 dark:text-white">
                {{ number_format($validationCount) }}
            </div>
            <p class="text-[11px] text-slate-400">Pengesahan keahlian oleh guru</p>
        </div>
    </div>

    <!-- 2. Multi-Parameter Filter Bar -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <!-- Search Keyword -->
            <div class="sm:col-span-3">
                <label for="q" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">
                    Cari Deskripsi / Pengguna / IP
                </label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input 
                        type="text" 
                        name="q" 
                        id="q" 
                        value="{{ request('q') }}" 
                        placeholder="Cari aktivitas..."
                        class="w-full pl-9 text-xs rounded-xl border-slate-200 dark:border-white/10 dark:bg-neutral-800 dark:text-slate-200 focus:border-brand-primary focus:ring-brand-primary/20"
                    >
                </div>
            </div>

            <!-- Role Filter -->
            <div class="sm:col-span-2">
                <label for="role" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">
                    Peran (Role)
                </label>
                <select 
                    name="role" 
                    id="role"
                    class="w-full text-xs rounded-xl border-slate-200 dark:border-white/10 dark:bg-neutral-800 dark:text-slate-200 focus:border-brand-primary focus:ring-brand-primary/20"
                >
                    <option value="">Semua Peran</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="perusahaan" {{ request('role') === 'perusahaan' ? 'selected' : '' }}>Mitra Industri</option>
                    <option value="guru" {{ request('role') === 'guru' ? 'selected' : '' }}>Guru Kejuruan</option>
                    <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa Vokasi</option>
                </select>
            </div>

            <!-- Action Category Filter -->
            <div class="sm:col-span-3">
                <label for="action" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">
                    Kategori Aksi
                </label>
                <select 
                    name="action" 
                    id="action"
                    class="w-full text-xs rounded-xl border-slate-200 dark:border-white/10 dark:bg-neutral-800 dark:text-slate-200 focus:border-brand-primary focus:ring-brand-primary/20"
                >
                    <option value="">Semua Kategori Aksi</option>
                    <option value="auth.*" {{ request('action') === 'auth.*' ? 'selected' : '' }}>Autentikasi (Login / Logout)</option>
                    <option value="company.*" {{ request('action') === 'company.*' ? 'selected' : '' }}>Kemitraan Industri</option>
                    <option value="application.*" {{ request('action') === 'application.*' ? 'selected' : '' }}>Lamaran Siswa & PKL</option>
                    <option value="skill.*" {{ request('action') === 'skill.*' ? 'selected' : '' }}>Validasi Keahlian Guru</option>
                    <option value="report.*" {{ request('action') === 'report.*' ? 'selected' : '' }}>Ekspor & Cetak Laporan BKK</option>
                </select>
            </div>

            <!-- Date Range: Start -->
            <div class="sm:col-span-2">
                <label for="start_date" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">
                    Tanggal Mulai
                </label>
                <input 
                    type="date" 
                    name="start_date" 
                    id="start_date" 
                    value="{{ request('start_date') }}"
                    class="w-full text-xs rounded-xl border-slate-200 dark:border-white/10 dark:bg-neutral-800 dark:text-slate-200 focus:border-brand-primary focus:ring-brand-primary/20"
                >
            </div>

            <!-- Buttons -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button 
                    type="submit" 
                    class="flex-1 inline-flex items-center justify-center px-3 py-2 rounded-xl text-xs font-semibold bg-neutral-900 text-white dark:bg-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-slate-100 transition-colors"
                >
                    <i data-lucide="filter" class="w-3.5 h-3.5 mr-1.5"></i>
                    <span>Terapkan</span>
                </button>

                @if (request()->hasAny(['q', 'role', 'action', 'start_date', 'end_date']))
                    <a 
                        href="{{ route('admin.activity-logs.index') }}" 
                        class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white border border-slate-200/80 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-neutral-800 transition-colors"
                        title="Reset Filter"
                    >
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 3. Logs Table Section -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 dark:bg-neutral-800/50 border-b border-slate-200/80 dark:border-white/10 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Waktu (WIB)</th>
                        <th class="py-3 px-4">Pengguna (Aktor)</th>
                        <th class="py-3 px-4">Aksi / Kategori</th>
                        <th class="py-3 px-4">Rincian Peristiwa</th>
                        <th class="py-3 px-4">Alamat IP & Perangkat</th>
                        <th class="py-3 px-4 text-right">Data Teknis</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-neutral-800/30 transition-colors">
                            <!-- Waktu -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-semibold text-slate-900 dark:text-white block">
                                    {{ $log->created_at->format('H:i:s') }} WIB
                                </span>
                                <span class="text-[11px] text-slate-400 block" title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                    {{ $log->created_at->translatedFormat('d M Y') }} &bull; {{ $log->created_at->diffForHumans() }}
                                </span>
                            </td>

                            <!-- Aktor -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-neutral-800 border border-slate-200/60 dark:border-white/10 flex items-center justify-center font-bold text-xs text-slate-700 dark:text-slate-300 flex-shrink-0">
                                        {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-semibold text-slate-900 dark:text-white truncate block">
                                            {{ $log->user->name ?? 'Sistem Otomatis' }}
                                        </span>
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold uppercase tracking-wider {{ match($log->role) {
                                            'admin' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300',
                                            'perusahaan' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300',
                                            'guru' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
                                            'siswa' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
                                            default => 'bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-slate-400',
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
                                <p class="text-xs text-slate-700 dark:text-slate-200 leading-relaxed">
                                    {{ $log->description }}
                                </p>
                            </td>

                            <!-- IP Address & Perangkat -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-mono text-[11px] text-slate-600 dark:text-slate-300 block">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </span>
                                <span class="text-[10px] text-slate-400 truncate block max-w-xs" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent ?? 'Internal System', 28) }}
                                </span>
                            </td>

                            <!-- Aksi Detail -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <button 
                                    type="button" 
                                    @click="openDetail({{ $log->id }})"
                                    class="inline-flex items-center px-2.5 py-1.5 rounded-lg border border-slate-200/80 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors text-xs font-medium"
                                >
                                    <i data-lucide="info" class="w-3.5 h-3.5 mr-1 text-slate-400"></i>
                                    <span>Detail</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-neutral-800 flex items-center justify-center mx-auto text-slate-400 mb-3">
                                    <i data-lucide="inbox" class="w-6 h-6"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Tidak Ada Log Aktivitas yang Cocok
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
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
            <div class="p-4 border-t border-slate-100 dark:border-white/5 flex justify-center">
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
                class="w-full max-w-xl transform overflow-hidden rounded-2xl bg-white dark:bg-neutral-900 border border-slate-200/80 dark:border-white/10 p-6 text-left shadow-2xl transition-all space-y-5"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-white/5">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white" id="modal-title">
                            Rincian Log Audit
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="'ID Peristiwa: #LOG-' + String(detailData?.id || '').padStart(6, '0')"></p>
                    </div>
                    <button 
                        type="button" 
                        @click="modalOpen = false"
                        class="p-1 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors"
                    >
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Modal Body: Key Data -->
                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3 bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/60 dark:border-white/5 rounded-xl p-3">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Aktor / Pengguna</span>
                            <span class="font-bold text-slate-900 dark:text-white" x-text="detailData?.actor?.name || '-'"></span>
                            <span class="text-slate-500 block text-[10px]" x-text="detailData?.actor?.email || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Peran (Role)</span>
                            <span class="font-bold text-brand-secondary dark:text-brand-primary" x-text="detailData?.actor?.role || '-'"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/60 dark:border-white/5 rounded-xl p-3">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Waktu Kejadian</span>
                            <span class="font-semibold text-slate-900 dark:text-white" x-text="detailData?.created_at || '-'"></span>
                            <span class="text-slate-500 block text-[10px]" x-text="detailData?.time_ago || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Alamat IP</span>
                            <span class="font-mono text-slate-900 dark:text-white" x-text="detailData?.ip_address || '-'"></span>
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[11px] mb-1">Deskripsi Lengkap</span>
                        <p class="text-slate-800 dark:text-slate-200 bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/60 dark:border-white/5 rounded-xl p-3" x-text="detailData?.description || '-'"></p>
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[11px] mb-1">Perangkat / User Agent</span>
                        <p class="font-mono text-[10px] text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/60 dark:border-white/5 rounded-xl p-2.5 break-all" x-text="detailData?.user_agent || '-'"></p>
                    </div>

                    <!-- JSON Properties / Metadata Diff -->
                    <template x-if="detailData?.properties">
                        <div>
                            <span class="text-slate-400 block text-[11px] mb-1">Data Perubahan / Payload Metadata (JSON)</span>
                            <pre class="bg-neutral-900 text-slate-200 text-[11px] font-mono p-3 rounded-xl overflow-x-auto max-h-48 border border-neutral-800" x-text="JSON.stringify(detailData.properties, null, 2)"></pre>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="pt-3 border-t border-slate-100 dark:border-white/5 flex justify-end">
                    <button 
                        type="button" 
                        @click="modalOpen = false"
                        class="px-4 py-2 rounded-xl text-xs font-semibold bg-neutral-900 text-white dark:bg-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-slate-100 transition-colors"
                    >
                        Tutup Rincian
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
