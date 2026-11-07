@extends('layouts.admin')

@section('title', 'Tracer Study & Analitik Keterserapan Alumni')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold bg-brand-primary/10 text-brand-secondary dark:text-brand-primary border border-brand-primary/20 mb-2">
                <i data-lucide="line-chart" class="w-3.5 h-3.5"></i>
                <span>Standar Penelusuran Tamatan Vokasi Kemdikbud</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Tracer Study & Keterserapan Alumni
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Monitoring keterserapan lulusan (Bekerja, Melanjutkan Kuliah, Wirausaha - BMW) dan evaluasi keselarasan kurikulum PPLG SMKN 1 Cirebon.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <x-ui.button
                variant="secondary"
                href="{{ route('alumni.index') }}"
                target="_blank"
            >
                Buka Direktori Publik
            </x-ui.button>
            <x-ui.button
                variant="primary"
                href="{{ route('tracer.create') }}"
                target="_blank"
            >
                Input Data Mandiri
            </x-ui.button>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs flex items-center gap-3">
            <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- KPI Summary Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Terdata -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-4 sm:p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-text-secondary">Total Alumni Terdata</span>
                <div class="w-8 h-8 rounded-btn bg-surface-bg border border-border-default flex items-center justify-center text-text-secondary">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl sm:text-3xl font-bold font-heading text-text-primary">{{ $totalAlumni }}</span>
                <span class="text-[11px] text-text-muted block mt-0.5">Siswa Lulusan Terdaftar</span>
            </div>
        </div>

        <!-- Tingkat Keterserapan BMW -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-4 sm:p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Keterserapan BMW</span>
                <div class="w-8 h-8 rounded-btn bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-bold font-heading text-emerald-600 dark:text-emerald-400">{{ $persenTerserap }}%</span>
                    <span class="text-xs text-text-muted font-medium">({{ $terserapCount }}/{{ $totalAlumni }})</span>
                </div>
                <span class="text-[11px] text-text-muted block mt-0.5">Bekerja, Kuliah, atau Wirausaha</span>
            </div>
        </div>

        <!-- Keselarasan Kompetensi Vokasi -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-4 sm:p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-brand-secondary dark:text-brand-primary">Keselarasan Kurikulum</span>
                <div class="w-8 h-8 rounded-btn bg-brand-primary/10 flex items-center justify-center text-brand-secondary dark:text-brand-primary">
                    <i data-lucide="check-check" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-bold font-heading text-text-primary">{{ $persenSelaras }}%</span>
                    <span class="text-xs text-text-muted font-medium">Taraf Selaras</span>
                </div>
                <span class="text-[11px] text-text-muted block mt-0.5">Sesuai / Sangat Sesuai Jurusan</span>
            </div>
        </div>

        <!-- Masih Mencari Kerja -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-4 sm:p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">Mencari Kerja</span>
                <div class="w-8 h-8 rounded-btn bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-bold font-heading text-amber-600 dark:text-amber-400">{{ $mencariKerjaCount }}</span>
                    <span class="text-xs text-text-muted font-medium">
                        ({{ $totalAlumni > 0 ? round(($mencariKerjaCount / $totalAlumni) * 100, 1) : 0 }}%)
                    </span>
                </div>
                <span class="text-[11px] text-text-muted block mt-0.5">Prioritas Fasilitasi BKK</span>
            </div>
        </div>
    </div>

    <!-- Visual Distribution Bars -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Breakdown Status BMW -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-text-primary flex items-center gap-2">
                    <i data-lucide="pie-chart" class="w-4 h-4 text-brand-secondary dark:text-brand-primary"></i>
                    <span>Distribusi Status Keterserapan (BMW)</span>
                </h3>
                <span class="text-[11px] text-text-muted">Basis Target IKU SMK</span>
            </div>

            <div class="space-y-3 text-xs">
                @php
                    $baseTotal = $totalAlumni > 0 ? $totalAlumni : 1;
                @endphp
                <!-- Bekerja -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-text-primary">Bekerja di Industri / DUDI</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">
                            {{ $bekerjaCount }} ({{ round(($bekerjaCount / $baseTotal) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-surface-bg border border-border-subtle overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ ($bekerjaCount / $baseTotal) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Kuliah -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-text-primary">Melanjutkan Pendidikan Tinggi (Kuliah)</span>
                        <span class="text-sky-600 dark:text-sky-400 font-bold">
                            {{ $kuliahCount }} ({{ round(($kuliahCount / $baseTotal) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-surface-bg border border-border-subtle overflow-hidden">
                        <div class="h-full bg-sky-500 rounded-full" style="width: {{ ($kuliahCount / $baseTotal) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Wirausaha -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-text-primary">Wirausaha Mandiri</span>
                        <span class="text-brand-secondary dark:text-brand-primary font-bold">
                            {{ $wirausahaCount }} ({{ round(($wirausahaCount / $baseTotal) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-surface-bg border border-border-subtle overflow-hidden">
                        <div class="h-full bg-brand-primary rounded-full" style="width: {{ ($wirausahaCount / $baseTotal) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Mencari Kerja -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-text-primary">Belum Bekerja / Mencari Peluang</span>
                        <span class="text-amber-600 dark:text-amber-400 font-bold">
                            {{ $mencariKerjaCount }} ({{ round(($mencariKerjaCount / $baseTotal) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-surface-bg border border-border-subtle overflow-hidden">
                        <div class="h-full bg-amber-500 rounded-full" style="width: {{ ($mencariKerjaCount / $baseTotal) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown Keselarasan Kompetensi Vokasi -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-text-primary flex items-center gap-2">
                    <i data-lucide="bar-chart-2" class="w-4 h-4 text-brand-secondary dark:text-brand-primary"></i>
                    <span>Taraf Keselarasan dengan Bidang PPLG</span>
                </h3>
                <span class="text-[11px] text-text-muted">Relevansi Kurikulum</span>
            </div>

            <div class="space-y-3 text-xs">
                @php
                    $evalTotal = ($sangatSesuai + $sesuai + $kurangSesuai + $tidakSesuai) > 0 ? ($sangatSesuai + $sesuai + $kurangSesuai + $tidakSesuai) : 1;
                @endphp
                <!-- Sangat Sesuai -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-text-primary">Sangat Sesuai (Software, IT, Web, Game)</span>
                        <span class="text-brand-secondary dark:text-brand-primary font-bold">
                            {{ $sangatSesuai }} ({{ round(($sangatSesuai / $evalTotal) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-surface-bg border border-border-subtle overflow-hidden">
                        <div class="h-full bg-brand-primary rounded-full" style="width: {{ ($sangatSesuai / $evalTotal) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Sesuai -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-text-primary">Sesuai (Teknologi & Digital Terkait)</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">
                            {{ $sesuai }} ({{ round(($sesuai / $evalTotal) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-surface-bg border border-border-subtle overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ ($sesuai / $evalTotal) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Kurang Sesuai -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-text-primary">Kurang Sesuai (Administrasi / Terkait Sebagian)</span>
                        <span class="text-amber-600 dark:text-amber-400 font-bold">
                            {{ $kurangSesuai }} ({{ round(($kurangSesuai / $evalTotal) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-surface-bg border border-border-subtle overflow-hidden">
                        <div class="h-full bg-amber-500 rounded-full" style="width: {{ ($kurangSesuai / $evalTotal) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Tidak Sesuai -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-text-primary">Tidak Sesuai (Lintas Sektor)</span>
                        <span class="text-rose-600 dark:text-rose-400 font-bold">
                            {{ $tidakSesuai }} ({{ round(($tidakSesuai / $evalTotal) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-surface-bg border border-border-subtle overflow-hidden">
                        <div class="h-full bg-rose-500 rounded-full" style="width: {{ ($tidakSesuai / $evalTotal) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table & Filter -->
    @php
        $headers = [
            ['key' => 'alumni', 'label' => 'Alumni & Kontak', 'sortable' => false],
            ['key' => 'tahun', 'label' => 'Tahun / Jurusan', 'sortable' => false],
            ['key' => 'status', 'label' => 'Status BMW', 'sortable' => false],
            ['key' => 'instansi', 'label' => 'Instansi & Posisi', 'sortable' => false],
            ['key' => 'keselarasan', 'label' => 'Keselarasan', 'sortable' => false],
            ['key' => 'visibilitas', 'label' => 'Visibilitas', 'sortable' => false],
        ];
        $isFilterActive = (request()->filled('status') && request('status') !== 'all') || (request()->filled('tahun') && request('tahun') !== 'all');
    @endphp

    <x-admin.data-table 
        title="Data Tracer Study & Pelacakan Alumni"
        subtitle="Daftar penelusuran tamatan dan rekam jejak keterserapan alumni vokasi"
        searchPlaceholder="Cari nama alumni, NISN, instansi, atau posisi..."
        :search="request('search')"
        :items="$alumni"
        :headers="$headers"
    >
        <x-slot:filter>
            <button 
                type="button" 
                onclick="window.openModal('modal-filter-tracer')"
                class="relative h-11 px-4 text-xs sm:text-sm font-semibold rounded-full border border-border-default bg-surface-card text-text-primary hover:border-text-secondary/70 hover:bg-surface-bg flex items-center gap-2 transition-all shadow-xs"
            >
                <i data-lucide="filter" class="w-4 h-4 text-text-secondary"></i>
                <span>Filter</span>
                @if ($isFilterActive)
                    <span class="w-2 h-2 rounded-full bg-brand-primary"></span>
                @endif
            </button>
        </x-slot:filter>

        @forelse($alumni as $item)
            <tr class="hover:bg-surface-bg/50 transition-colors group">
                <!-- Column: Alumni & Kontak -->
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-surface-bg border border-border-default flex items-center justify-center font-bold text-xs text-brand-secondary dark:text-brand-primary shrink-0">
                            {{ strtoupper(substr($item->user->name ?? 'A', 0, 2)) }}
                        </div>
                        <div>
                            <span class="font-semibold text-text-primary block text-xs sm:text-sm">
                                {{ $item->user->name ?? 'Alumni' }}
                            </span>
                            <span class="text-xs text-text-secondary block">
                                {{ $item->user->email ?? '-' }}
                            </span>
                        </div>
                    </div>
                </td>

                <!-- Column: Tahun / Jurusan -->
                <td class="px-5 py-3.5 text-xs">
                    <span class="font-semibold text-text-primary block">
                        Lulus {{ $item->tahun_lulus }}
                    </span>
                    <span class="text-text-secondary block mt-0.5">
                        {{ $item->major->kode ?? 'PPLG' }}
                    </span>
                </td>

                <!-- Column: Status BMW -->
                <td class="px-5 py-3.5 whitespace-nowrap">
                    @if($item->status_bmw === 'bekerja')
                        <x-ui.badge variant="success">Bekerja</x-ui.badge>
                    @elseif($item->status_bmw === 'kuliah')
                        <x-ui.badge variant="info">Kuliah</x-ui.badge>
                    @elseif($item->status_bmw === 'wirausaha')
                        <x-ui.badge variant="brand">Wirausaha</x-ui.badge>
                    @else
                        <x-ui.badge variant="warning">Mencari Kerja</x-ui.badge>
                    @endif
                </td>

                <!-- Column: Instansi & Posisi -->
                <td class="px-5 py-3.5 text-xs max-w-[200px]">
                    <div class="font-medium text-text-primary truncate">
                        {{ $item->posisi_studi ?: ($item->pekerjaan ?: '-') }}
                    </div>
                    <div class="text-text-secondary truncate mt-0.5">
                        {{ $item->nama_instansi ?: ($item->perusahaan ?: '-') }}
                    </div>
                </td>

                <!-- Column: Keselarasan -->
                <td class="px-5 py-3.5 text-xs text-text-secondary whitespace-nowrap">
                    {{ $item->keselarasan_label }}
                </td>

                <!-- Column: Visibilitas -->
                <td class="px-5 py-3.5 whitespace-nowrap">
                    <form method="POST" action="{{ route('admin.tracer.toggle-visibility', $item->id) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button
                            type="submit"
                            class="px-2.5 py-1 rounded-full text-[11px] font-bold border transition-colors {{ $item->is_public ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-surface-bg text-text-muted border-border-default hover:bg-surface-card' }}"
                            title="Klik untuk mengubah visibilitas"
                        >
                            {{ $item->is_public ? 'Publik' : 'Privat' }}
                        </button>
                    </form>
                </td>

                <!-- Column: Aksi -->
                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                        <x-ui.icon-button 
                            size="sm" 
                            icon="eye" 
                            href="{{ route('alumni.show', $item->id) }}"
                            target="_blank"
                            label="Lihat Profil Publik" 
                        />
                        <form method="POST" action="{{ route('admin.tracer.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data alumni ini?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-ui.icon-button 
                                size="sm" 
                                icon="trash-2" 
                                variant="danger" 
                                type="submit" 
                                label="Hapus Data Alumni" 
                            />
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="p-8 text-center text-text-secondary text-sm">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <i data-lucide="graduation-cap" class="w-8 h-8 text-text-muted"></i>
                        <p class="font-semibold text-text-primary">Belum ada data kuesioner tracer study alumni</p>
                        <p class="text-xs text-text-muted">Alumni dapat mengisi tracer study secara mandiri atau melalui form pelacakan alumni.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.data-table>
</div>

<!-- Modal Filter Tracer Study -->
<x-ui.modal 
    id="modal-filter-tracer" 
    title="Filter Data Tracer Study" 
    subtitle="Saring alumni berdasarkan status penyerapan BMW dan tahun kelulusan"
    size="md"
>
    <form method="GET" action="{{ route('admin.tracer.index') }}" class="space-y-4">
        @if (request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Status BMW (Bekerja, Kuliah, Wirausaha)
            </label>
            <select 
                name="status" 
                class="choices-select w-full"
            >
                <option value="all">Semua Status BMW</option>
                <option value="bekerja" {{ request('status') === 'bekerja' ? 'selected' : '' }}>Bekerja</option>
                <option value="kuliah" {{ request('status') === 'kuliah' ? 'selected' : '' }}>Melanjutkan Kuliah</option>
                <option value="wirausaha" {{ request('status') === 'wirausaha' ? 'selected' : '' }}>Wirausaha</option>
                <option value="mencari_kerja" {{ request('status') === 'mencari_kerja' ? 'selected' : '' }}>Mencari Kerja</option>
            </select>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Tahun Angkatan Lulusan
            </label>
            <select 
                name="tahun" 
                class="choices-select w-full"
            >
                <option value="all">Semua Tahun Lulus</option>
                @foreach($years as $yr)
                    <option value="{{ $yr }}" {{ request('tahun') == $yr ? 'selected' : '' }}>Angkatan {{ $yr }}</option>
                @endforeach
            </select>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-between gap-3">
            <a 
                href="{{ route('admin.tracer.index') }}" 
                class="px-4 py-2 text-xs font-semibold text-text-secondary hover:text-text-primary transition-colors"
            >
                Reset Filter
            </a>
            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    onclick="window.closeModal('modal-filter-tracer')"
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
@endsection
