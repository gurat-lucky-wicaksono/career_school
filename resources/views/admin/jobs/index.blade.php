@extends('layouts.admin')

@section('title', 'Lowongan Kerja & Magang Vokasi')

@section('content')
<div class="space-y-6">
    @php
        $headers = [
            ['key' => 'judul', 'label' => 'Posisi / Lowongan', 'sortable' => true],
            ['key' => 'company', 'label' => 'Mitra Perusahaan', 'sortable' => false],
            ['key' => 'jenis', 'label' => 'Tipe Posisi', 'sortable' => true],
            ['key' => 'lokasi', 'label' => 'Lokasi', 'sortable' => false],
            ['key' => 'batas_lamaran', 'label' => 'Tenggat', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
        ];
        $isFilterActive = request()->filled('filter_jenis') || request()->filled('filter_status') || request()->filled('filter_company');
    @endphp

    <x-admin.data-table 
        title="Bursa Kerja & Magang DUDI"
        subtitle="Manajemen lowongan kerja industri dan program PKL siswa SMK Negeri 1 Cirebon"
        searchPlaceholder="Cari judul loker, mitra, atau lokasi..."
        :search="request('search')"
        :items="$jobs"
        :headers="$headers"
        :currentSort="$sortField"
        :currentDirection="$sortDirection"
    >
        <x-slot:checkboxAll>
            <input 
                type="checkbox" 
                id="select-all-jobs"
                onclick="toggleSelectAll(this)"
                aria-label="Pilih Semua Lowongan"
            >
        </x-slot:checkboxAll>

        <x-slot:filter>
            <button 
                type="button" 
                onclick="window.openModal('modal-filter-jobs')"
                class="relative h-11 px-4 text-xs sm:text-sm font-semibold rounded-full border border-border-default bg-surface-card text-text-primary hover:border-text-secondary/70 hover:bg-surface-bg flex items-center gap-2 transition-all shadow-xs"
            >
                <i data-lucide="filter" class="w-4 h-4 text-text-secondary"></i>
                <span>Filter</span>
                @if ($isFilterActive)
                    <span class="w-2 h-2 rounded-full bg-brand-primary"></span>
                @endif
            </button>
        </x-slot:filter>

        <x-slot:action>
            <x-ui.button variant="primary" onclick="window.openModal('modal-create-job')">
                Tambah Lowongan
            </x-ui.button>
        </x-slot:action>

        @forelse ($jobs as $job)
            <tr class="hover:bg-slate-50/80 dark:hover:bg-neutral-800/40 transition-colors group">
                <td class="w-12 px-4 py-3.5 text-center">
                    <input 
                        type="checkbox" 
                        aria-label="Pilih lowongan {{ $job->judul }}"
                    >
                </td>

                <td class="px-5 py-3.5">
                    <div>
                        <span class="font-bold text-slate-900 dark:text-white block">
                            {{ $job->judul }}
                        </span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 block font-mono">
                            Kuota: {{ $job->kuota }} posisi &bull; {{ $job->applications_count }} pelamar
                        </span>
                    </div>
                </td>

                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200 text-xs">
                    {{ $job->company->nama ?? 'Mitra Umum' }}
                </td>

                <td class="px-5 py-3.5">
                    @php
                        $jenisLabels = [
                            'magang' => 'Magang / PKL',
                            'penuh_waktu' => 'Full Time',
                            'paruh_waktu' => 'Part Time',
                            'proyek' => 'Proyek / Kontrak',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-white/10">
                        {{ $jenisLabels[$job->jenis] ?? ucfirst($job->jenis) }}
                    </span>
                </td>

                <td class="px-5 py-3.5 text-xs text-slate-600 dark:text-slate-300">
                    {{ $job->lokasi ?? '-' }}
                </td>

                <td class="px-5 py-3.5 font-mono text-xs text-slate-600 dark:text-slate-400">
                    {{ $job->batas_lamaran ? date('d M Y', strtotime($job->batas_lamaran)) : 'Terbuka' }}
                </td>

                <td class="px-5 py-3.5">
                    @php
                        $statusVariant = match($job->status) {
                            'dibuka' => 'success',
                            'ditutup' => 'danger',
                            default => 'secondary'
                        };
                    @endphp
                    <x-ui.badge :variant="$statusVariant">
                        {{ ucfirst($job->status) }}
                    </x-ui.badge>
                </td>

                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                        <x-ui.icon-button 
                            size="sm" 
                            icon="eye" 
                            label="Lihat Detail Lowongan" 
                            onclick="showJobDetail({{ json_encode([
                                'judul' => $job->judul,
                                'company' => $job->company->nama ?? 'Mitra Industri',
                                'jenis' => $jenisLabels[$job->jenis] ?? ucfirst($job->jenis),
                                'lokasi' => $job->lokasi ?? 'Lokasi Sekolah / Mitra',
                                'kuota' => $job->kuota . ' Posisi Dibutuhkan',
                                'pelamar' => $job->applications_count . ' Siswa Melamar',
                                'batas' => $job->batas_lamaran ? date('d F Y', strtotime($job->batas_lamaran)) : 'Terbuka Selamanya',
                                'status' => ucfirst($job->status),
                                'deskripsi' => $job->deskripsi ?? 'Tidak ada deskripsi rinci.',
                            ]) }})"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            icon="edit-3" 
                            label="Ubah Data Lowongan" 
                            onclick="editJob({{ json_encode([
                                'id' => $job->id,
                                'update_url' => route('admin.jobs.update', $job->id),
                                'judul' => $job->judul,
                                'company_id' => $job->company_id,
                                'jenis' => $job->jenis,
                                'lokasi' => $job->lokasi ?? '',
                                'kuota' => $job->kuota,
                                'batas_lamaran' => $job->batas_lamaran,
                                'status' => $job->status,
                                'deskripsi' => $job->deskripsi,
                            ]) }})"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            variant="danger" 
                            icon="trash-2" 
                            label="Hapus Lowongan" 
                            onclick="window.submitDeleteForm('{{ route('admin.jobs.destroy', $job->id) }}', 'Lowongan {{ addslashes($job->judul) }}')"
                        />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="py-12">
                    <x-ui.empty-state 
                        title="Belum Ada Lowongan Kerja"
                        description="Daftar lowongan kerja atau magang DUDI belum tercatat atau hasil pencarian tidak ditemukan."
                        actionLabel="Tambah Lowongan Baru"
                        actionUrl="javascript:window.openModal('modal-create-job')"
                    />
                </td>
            </tr>
        @endforelse
    </x-admin.data-table>
</div>

<!-- Modal 1: Filter Lowongan -->
<x-ui.modal 
    id="modal-filter-jobs" 
    title="Filter Lowongan Kerja" 
    subtitle="Saring bursa kerja berdasarkan jenis penempatan, status, atau perusahaan mitra"
    size="md"
    overflow="visible"
>
    <form method="GET" action="{{ route('admin.jobs.index') }}" class="space-y-4">
        @if (request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        @if (request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
        @endif

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Perusahaan Mitra (DUDI)
            </label>
            <select 
                name="filter_company" 
                class="choices-select w-full"
            >
                <option value="">Semua Perusahaan</option>
                @foreach ($companies as $comp)
                    <option value="{{ $comp->id }}" {{ request('filter_company') == $comp->id ? 'selected' : '' }}>
                        {{ $comp->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Jenis Posisi
                </label>
                <select 
                    name="filter_jenis" 
                    class="choices-select w-full"
                >
                    <option value="">Semua Jenis</option>
                    <option value="magang" {{ request('filter_jenis') === 'magang' ? 'selected' : '' }}>Magang / PKL</option>
                    <option value="penuh_waktu" {{ request('filter_jenis') === 'penuh_waktu' ? 'selected' : '' }}>Full Time</option>
                    <option value="paruh_waktu" {{ request('filter_jenis') === 'paruh_waktu' ? 'selected' : '' }}>Part Time</option>
                    <option value="proyek" {{ request('filter_jenis') === 'proyek' ? 'selected' : '' }}>Proyek / Kontrak</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Status Lowongan
                </label>
                <select 
                    name="filter_status" 
                    class="choices-select w-full"
                >
                    <option value="">Semua Status</option>
                    <option value="dibuka" {{ request('filter_status') === 'dibuka' ? 'selected' : '' }}>Dibuka</option>
                    <option value="ditutup" {{ request('filter_status') === 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                    <option value="selesai" {{ request('filter_status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-between gap-3">
            <a 
                href="{{ route('admin.jobs.index') }}" 
                class="px-4 py-2 text-xs font-semibold text-text-secondary hover:text-text-primary transition-colors"
            >
                Reset Filter
            </a>
            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    onclick="window.closeModal('modal-filter-jobs')"
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

<!-- Modal 2: Tambah Lowongan -->
<x-ui.modal 
    id="modal-create-job" 
    title="Publikasikan Lowongan Baru" 
    subtitle="Buka kesempatan magang atau karier bagi peserta didik dan alumni"
    size="lg"
>
    <form method="POST" action="{{ route('admin.jobs.store') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Judul Posisi / Pekerjaan
                </label>
                <input 
                    type="text" 
                    name="judul" 
                    required 
                    placeholder="Contoh: Junior Web Developer, Teknisi Jaringan"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Mitra Perusahaan (DUDI)
                </label>
                <select 
                    name="company_id" 
                    required
                    class="choices-select w-full"
                >
                    <option value="">Pilih Perusahaan Mitra</option>
                    @foreach ($companies as $comp)
                        <option value="{{ $comp->id }}">{{ $comp->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Jenis Posisi
                </label>
                <select 
                    name="jenis" 
                    required
                    class="choices-select w-full"
                >
                    <option value="magang">Magang / PKL</option>
                    <option value="penuh_waktu" selected>Penuh Waktu (Full Time)</option>
                    <option value="paruh_waktu">Paruh Waktu (Part Time)</option>
                    <option value="proyek">Proyek / Kontrak</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Kuota Posisi
                </label>
                <input 
                    type="number" 
                    name="kuota" 
                    required 
                    value="1" 
                    min="1"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary font-mono text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Status Lowongan
                </label>
                <select 
                    name="status" 
                    required
                    class="choices-select w-full"
                >
                    <option value="dibuka" selected>Dibuka</option>
                    <option value="ditutup">Ditutup</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Lokasi Penempatan
                </label>
                <input 
                    type="text" 
                    name="lokasi" 
                    placeholder="Contoh: Kota Cirebon / WFH"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Tenggat Pendaftaran (Batas Lamaran)
                </label>
                <input 
                    type="date" 
                    name="batas_lamaran" 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Deskripsi & Kualifikasi Lowongan
            </label>
            <textarea 
                name="deskripsi" 
                rows="4" 
                required
                placeholder="Rincian deskripsi tanggung jawab pekerjaan dan kualifikasi yang dicari..."
                class="w-full p-4 rounded-2xl bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
            ></textarea>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-end gap-3">
            <button 
                type="button" 
                onclick="window.closeModal('modal-create-job')"
                class="px-5 py-2.5 text-xs sm:text-sm font-semibold rounded-full border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg transition-colors focus:outline-none"
            >
                Batal
            </button>
            <button 
                type="submit" 
                class="px-6 py-2.5 text-xs sm:text-sm font-semibold rounded-full bg-brand-primary text-text-on-brand-primary hover:brightness-105 transition-all shadow-md shadow-brand-primary/20 focus:outline-none"
            >
                Publikasikan Lowongan
            </button>
        </div>
    </form>
</x-ui.modal>

<!-- Modal 3: Edit Lowongan -->
<x-ui.modal 
    id="modal-edit-job" 
    title="Ubah Data Lowongan" 
    subtitle="Perbarui kualifikasi, kuota, atau status posisi kerja"
    size="lg"
>
    <form id="form-edit-job" method="POST" action="" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Judul Posisi / Pekerjaan
                </label>
                <input 
                    type="text" 
                    id="edit-job-judul" 
                    name="judul" 
                    required 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Mitra Perusahaan (DUDI)
                </label>
                <select 
                    id="edit-job-company" 
                    name="company_id" 
                    required
                    class="choices-select w-full"
                >
                    @foreach ($companies as $comp)
                        <option value="{{ $comp->id }}">{{ $comp->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Jenis Posisi
                </label>
                <select 
                    id="edit-job-jenis" 
                    name="jenis" 
                    required
                    class="choices-select w-full"
                >
                    <option value="magang">Magang / PKL</option>
                    <option value="penuh_waktu">Penuh Waktu (Full Time)</option>
                    <option value="paruh_waktu">Paruh Waktu (Part Time)</option>
                    <option value="proyek">Proyek / Kontrak</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Kuota Posisi
                </label>
                <input 
                    type="number" 
                    id="edit-job-kuota" 
                    name="kuota" 
                    required 
                    min="1"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary font-mono text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Status Lowongan
                </label>
                <select 
                    id="edit-job-status" 
                    name="status" 
                    required
                    class="choices-select w-full"
                >
                    <option value="dibuka">Dibuka</option>
                    <option value="ditutup">Ditutup</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Lokasi Penempatan
                </label>
                <input 
                    type="text" 
                    id="edit-job-lokasi" 
                    name="lokasi" 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Tenggat Pendaftaran
                </label>
                <input 
                    type="date" 
                    id="edit-job-batas" 
                    name="batas_lamaran" 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Deskripsi & Kualifikasi Lowongan
            </label>
            <textarea 
                id="edit-job-deskripsi" 
                name="deskripsi" 
                rows="4" 
                required
                class="w-full p-4 rounded-2xl bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
            ></textarea>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-end gap-3">
            <button 
                type="button" 
                onclick="window.closeModal('modal-edit-job')"
                class="px-5 py-2.5 text-xs sm:text-sm font-semibold rounded-full border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg transition-colors focus:outline-none"
            >
                Batal
            </button>
            <button 
                type="submit" 
                class="px-6 py-2.5 text-xs sm:text-sm font-semibold rounded-full bg-brand-primary text-text-on-brand-primary hover:brightness-105 transition-all shadow-md shadow-brand-primary/20 focus:outline-none"
            >
                Simpan Perubahan
            </button>
        </div>
    </form>
</x-ui.modal>

<!-- Modal 4: Detail Lowongan -->
<x-ui.modal 
    id="modal-detail-job" 
    title="Detail Lowongan Pekerjaan" 
    subtitle="Rincian informasi kesempatan karier dan kualifikasi"
    size="md"
>
    <div class="space-y-4">
        <div class="p-4 rounded-2xl bg-surface-bg border border-border-default">
            <h4 id="detail-job-judul" class="font-bold text-text-primary text-lg">
                Judul Posisi
            </h4>
            <p id="detail-job-company" class="text-xs text-brand-secondary dark:text-brand-primary font-semibold mt-1">
                Mitra Perusahaan
            </p>
        </div>

        <div class="divide-y divide-border-subtle text-xs sm:text-sm">
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Tipe Posisi:</span>
                <span id="detail-job-jenis" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Lokasi:</span>
                <span id="detail-job-lokasi" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Kuota Diterima:</span>
                <span id="detail-job-kuota" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Jumlah Pelamar:</span>
                <span id="detail-job-pelamar" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Tenggat Lamaran:</span>
                <span id="detail-job-batas" class="font-mono text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Status:</span>
                <span id="detail-job-status" class="font-semibold text-text-primary">-</span>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-text-secondary mb-1">Deskripsi & Kualifikasi:</label>
            <p id="detail-job-deskripsi" class="text-xs text-text-primary leading-relaxed bg-surface-bg p-4 rounded-2xl border border-border-default whitespace-pre-line">
                -
            </p>
        </div>

        <div class="pt-4 border-t border-border-subtle flex justify-end">
            <button 
                type="button" 
                onclick="window.closeModal('modal-detail-job')"
                class="px-5 py-2.5 text-xs sm:text-sm font-semibold rounded-full border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg transition-colors focus:outline-none"
            >
                Tutup
            </button>
        </div>
    </div>
</x-ui.modal>

@push('scripts')
<script>
    function toggleSelectAll(master) {
        document.querySelectorAll('tbody input[type="checkbox"]').forEach(cb => cb.checked = master.checked);
    }

    function showJobDetail(data) {
        document.getElementById('detail-job-judul').innerText = data.judul;
        document.getElementById('detail-job-company').innerText = data.company;
        document.getElementById('detail-job-jenis').innerText = data.jenis;
        document.getElementById('detail-job-lokasi').innerText = data.lokasi;
        document.getElementById('detail-job-kuota').innerText = data.kuota;
        document.getElementById('detail-job-pelamar').innerText = data.pelamar;
        document.getElementById('detail-job-batas').innerText = data.batas;
        document.getElementById('detail-job-status').innerText = data.status;
        document.getElementById('detail-job-deskripsi').innerText = data.deskripsi;
        window.openModal('modal-detail-job');
    }

    function editJob(data) {
        document.getElementById('form-edit-job').action = data.update_url;
        document.getElementById('edit-job-judul').value = data.judul;
        document.getElementById('edit-job-lokasi').value = data.lokasi;
        document.getElementById('edit-job-kuota').value = data.kuota;
        document.getElementById('edit-job-batas').value = data.batas_lamaran ? data.batas_lamaran.substring(0, 10) : '';
        document.getElementById('edit-job-deskripsi').value = data.deskripsi;

        window.setSelectValue('edit-job-company', data.company_id);
        window.setSelectValue('edit-job-jenis', data.jenis);
        window.setSelectValue('edit-job-status', data.status);

        window.openModal('modal-edit-job');
    }
</script>
@endpush
@endsection
