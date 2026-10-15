@extends('layouts.admin')

@section('title', 'Kemitraan Industri (DUDI)')

@section('content')
<div class="space-y-6">
    @php
        $headers = [
            ['key' => 'nama', 'label' => 'Nama Perusahaan / Instansi', 'sortable' => true],
            ['key' => 'bidang', 'label' => 'Bidang Industri', 'sortable' => true],
            ['key' => 'kota', 'label' => 'Kota / Wilayah', 'sortable' => true],
            ['key' => 'jobs_count', 'label' => 'Lowongan', 'sortable' => true],
            ['key' => 'status_verifikasi', 'label' => 'Status Verifikasi', 'sortable' => true],
        ];
        $isFilterActive = request()->filled('status') || request()->filled('filter_bidang');
    @endphp

    <x-admin.data-table 
        title="Mitra Dunia Usaha & Industri (DUDI)"
        subtitle="Manajemen jejaring kemitraan magang, PKL, dan keterserapan kerja SMK Negeri 1 Cirebon"
        searchPlaceholder="Cari nama perusahaan, bidang, kota..."
        :search="request('search')"
        :items="$companies"
        :headers="$headers"
        :currentSort="$sortField"
        :currentDirection="$sortDirection"
    >
        <x-slot:checkboxAll>
            <input 
                type="checkbox" 
                id="select-all-companies"
                onclick="toggleSelectAll(this)"
                aria-label="Pilih Semua Mitra"
            >
        </x-slot:checkboxAll>

        <x-slot:filter>
            <button 
                type="button" 
                onclick="window.openModal('modal-filter-companies')"
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
            <x-ui.button variant="primary" onclick="window.openModal('modal-create-company')">
                Tambah Mitra
            </x-ui.button>
        </x-slot:action>

        @forelse ($companies as $company)
            <tr class="hover:bg-slate-50/80 dark:hover:bg-neutral-800/40 transition-colors group">
                <td class="w-12 px-4 py-3.5 text-center">
                    <input 
                        type="checkbox" 
                        aria-label="Pilih perusahaan {{ $company->nama }}"
                    >
                </td>

                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-200 font-semibold text-xs flex items-center justify-center flex-shrink-0 border border-slate-200/80 dark:border-white/10">
                            {{ strtoupper(substr($company->nama, 0, 2)) }}
                        </div>
                        <div>
                            <span class="font-semibold text-slate-900 dark:text-white block">
                                {{ $company->nama }}
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 block font-mono">
                                {{ $company->user->email ?? '-' }}
                            </span>
                        </div>
                    </div>
                </td>

                <td class="px-5 py-3.5 text-xs text-slate-700 dark:text-slate-200">
                    {{ $company->bidang ?? 'Umum' }}
                </td>

                <td class="px-5 py-3.5 text-xs text-slate-600 dark:text-slate-300">
                    {{ $company->kota ?? '-' }}
                </td>

                <td class="px-5 py-3.5 font-mono text-xs font-semibold text-slate-700 dark:text-slate-200">
                    {{ $company->jobs_count }} Loker
                </td>

                <td class="px-5 py-3.5">
                    @php
                        $badgeVariant = match($company->status_verifikasi) {
                            'terverifikasi' => 'success',
                            'menunggu' => 'warning',
                            'ditolak' => 'danger',
                            default => 'secondary'
                        };
                    @endphp
                    <x-ui.badge :variant="$badgeVariant">
                        {{ ucfirst($company->status_verifikasi) }}
                    </x-ui.badge>
                </td>

                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                        <x-ui.icon-button 
                            size="sm" 
                            icon="eye" 
                            label="Lihat Detail Mitra" 
                            onclick="showCompanyDetail({{ json_encode([
                                'nama' => $company->nama,
                                'email' => $company->user->email ?? '-',
                                'bidang' => $company->bidang ?? 'Umum',
                                'kota' => $company->kota ?? '-',
                                'alamat' => $company->alamat ?? '-',
                                'situs' => $company->situs ?? '-',
                                'status' => ucfirst($company->status_verifikasi),
                                'jobs_count' => $company->jobs_count . ' Lowongan Terdaftar',
                                'deskripsi' => $company->deskripsi ?? 'Tidak ada profil ringkas.',
                            ]) }})"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            icon="edit-3" 
                            label="Ubah Data Mitra" 
                            onclick="editCompany({{ json_encode([
                                'id' => $company->id,
                                'update_url' => route('admin.companies.update', $company->id),
                                'nama' => $company->nama,
                                'email' => $company->user->email ?? '',
                                'bidang' => $company->bidang ?? '',
                                'kota' => $company->kota ?? '',
                                'alamat' => $company->alamat ?? '',
                                'situs' => $company->situs ?? '',
                                'deskripsi' => $company->deskripsi ?? '',
                                'status_verifikasi' => $company->status_verifikasi,
                            ]) }})"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            variant="danger" 
                            icon="trash-2" 
                            label="Hapus Mitra" 
                            onclick="window.submitDeleteForm('{{ route('admin.companies.destroy', $company->id) }}', 'Mitra {{ addslashes($company->nama) }}')"
                        />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-12">
                    <x-ui.empty-state 
                        title="Belum Ada Mitra Industri"
                        description="Data perusahaan mitra belum tercatat atau hasil pencarian tidak ditemukan."
                        actionLabel="Tambah Mitra Baru"
                        actionUrl="javascript:window.openModal('modal-create-company')"
                    />
                </td>
            </tr>
        @endforelse
    </x-admin.data-table>
</div>

<!-- Modal 1: Filter Mitra -->
<x-ui.modal 
    id="modal-filter-companies" 
    title="Filter Mitra Industri" 
    subtitle="Saring data kemitraan DUDI berdasarkan status verifikasi atau bidang"
    size="md"
    overflow="visible"
>
    <form method="GET" action="{{ route('admin.companies.index') }}" class="space-y-4">
        @if (request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        @if (request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
        @endif

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Status Verifikasi
            </label>
            <select 
                name="status" 
                class="choices-select w-full"
            >
                <option value="">Semua Status</option>
                <option value="terverifikasi" {{ request('status') === 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Bidang Industri
            </label>
            <select 
                name="filter_bidang" 
                class="choices-select w-full"
            >
                <option value="">Semua Bidang</option>
                @foreach ($bidangs as $bid)
                    <option value="{{ $bid }}" {{ request('filter_bidang') === $bid ? 'selected' : '' }}>
                        {{ $bid }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-between gap-3">
            <a 
                href="{{ route('admin.companies.index') }}" 
                class="px-4 py-2 text-xs font-semibold text-text-secondary hover:text-text-primary transition-colors"
            >
                Reset Filter
            </a>
            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    onclick="window.closeModal('modal-filter-companies')"
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

<!-- Modal 2: Tambah Mitra Baru -->
<x-ui.modal 
    id="modal-create-company" 
    title="Tambah Mitra Industri Baru" 
    subtitle="Entri data kemitraan dunia usaha dan dunia industri (DUDI)"
    size="lg"
>
    <form method="POST" action="{{ route('admin.companies.store') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Nama Perusahaan / Instansi
                </label>
                <input 
                    type="text" 
                    name="nama" 
                    required 
                    placeholder="Contoh: PT Telkom Indonesia"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Email Kontak / HRD
                </label>
                <input 
                    type="email" 
                    name="email" 
                    required 
                    placeholder="hrd@perusahaan.com"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Bidang Industri
                </label>
                <input 
                    type="text" 
                    name="bidang" 
                    placeholder="Contoh: IT & Software"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Kota / Wilayah
                </label>
                <input 
                    type="text" 
                    name="kota" 
                    placeholder="Contoh: Kota Cirebon"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Status Verifikasi
                </label>
                <select 
                    name="status_verifikasi" 
                    required
                    class="choices-select w-full"
                >
                    <option value="terverifikasi" selected>Terverifikasi</option>
                    <option value="menunggu">Menunggu</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Situs Web Resmi
            </label>
            <input 
                type="url" 
                name="situs" 
                placeholder="https://perusahaan.co.id"
                class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
            >
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Alamat Kantor
            </label>
            <textarea 
                name="alamat" 
                rows="2"
                placeholder="Alamat kantor atau lokasi operasional..."
                class="w-full p-4 rounded-2xl bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
            ></textarea>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Deskripsi Singkat Profil Perusahaan
            </label>
            <textarea 
                name="deskripsi" 
                rows="2"
                placeholder="Gambaran umum profil dan lini bisnis perusahaan..."
                class="w-full p-4 rounded-2xl bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
            ></textarea>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-end gap-3">
            <button 
                type="button" 
                onclick="window.closeModal('modal-create-company')"
                class="px-5 py-2.5 text-xs sm:text-sm font-semibold rounded-full border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg transition-colors focus:outline-none"
            >
                Batal
            </button>
            <button 
                type="submit" 
                class="px-6 py-2.5 text-xs sm:text-sm font-semibold rounded-full bg-brand-primary text-text-on-brand-primary hover:brightness-105 transition-all shadow-md shadow-brand-primary/20 focus:outline-none"
            >
                Simpan Mitra Baru
            </button>
        </div>
    </form>
</x-ui.modal>

<!-- Modal 3: Edit Mitra -->
<x-ui.modal 
    id="modal-edit-company" 
    title="Ubah Data Mitra Industri" 
    subtitle="Perbarui data kontak dan status verifikasi perusahaan"
    size="lg"
>
    <form id="form-edit-company" method="POST" action="" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Nama Perusahaan / Instansi
                </label>
                <input 
                    type="text" 
                    id="edit-company-nama" 
                    name="nama" 
                    required 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Email Kontak / HRD
                </label>
                <input 
                    type="email" 
                    id="edit-company-email" 
                    name="email" 
                    required 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Bidang Industri
                </label>
                <input 
                    type="text" 
                    id="edit-company-bidang" 
                    name="bidang" 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Kota / Wilayah
                </label>
                <input 
                    type="text" 
                    id="edit-company-kota" 
                    name="kota" 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Status Verifikasi
                </label>
                <select 
                    id="edit-company-status" 
                    name="status_verifikasi" 
                    required
                    class="choices-select w-full"
                >
                    <option value="terverifikasi">Terverifikasi</option>
                    <option value="menunggu">Menunggu</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Situs Web Resmi
            </label>
            <input 
                type="url" 
                id="edit-company-situs" 
                name="situs" 
                class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
            >
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Alamat Kantor
            </label>
            <textarea 
                id="edit-company-alamat" 
                name="alamat" 
                rows="2"
                class="w-full p-4 rounded-2xl bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
            ></textarea>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Deskripsi Singkat Profil Perusahaan
            </label>
            <textarea 
                id="edit-company-deskripsi" 
                name="deskripsi" 
                rows="2"
                class="w-full p-4 rounded-2xl bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
            ></textarea>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-end gap-3">
            <button 
                type="button" 
                onclick="window.closeModal('modal-edit-company')"
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

<!-- Modal 4: Detail Mitra -->
<x-ui.modal 
    id="modal-detail-company" 
    title="Detail Mitra Industri" 
    subtitle="Profil lengkap mitra dan keterserapan kerja siswa"
    size="md"
>
    <div class="space-y-4">
        <div class="flex items-center gap-4 p-4 rounded-2xl bg-surface-bg border border-border-default">
            <div id="detail-company-avatar" class="w-12 h-12 rounded-full bg-brand-primary/20 text-brand-secondary dark:text-brand-primary font-bold text-base flex items-center justify-center border border-brand-primary/30">
                PT
            </div>
            <div>
                <h4 id="detail-company-nama" class="font-bold text-text-primary text-base">
                    Nama Mitra
                </h4>
                <p id="detail-company-email" class="text-xs text-text-secondary font-mono">
                    email@perusahaan.com
                </p>
            </div>
        </div>

        <div class="divide-y divide-border-subtle text-xs sm:text-sm">
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Bidang Usaha:</span>
                <span id="detail-company-bidang" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Kota Domisili:</span>
                <span id="detail-company-kota" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Status Kemitraan:</span>
                <span id="detail-company-status" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Jumlah Lowongan:</span>
                <span id="detail-company-jobs" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Situs Resmi:</span>
                <span id="detail-company-situs" class="font-mono text-xs text-text-primary">-</span>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-text-secondary mb-1">Alamat Kantor:</label>
            <p id="detail-company-alamat" class="text-xs text-text-primary leading-relaxed bg-surface-bg p-4 rounded-2xl border border-border-default">
                -
            </p>
        </div>

        <div class="pt-4 border-t border-border-subtle flex justify-end">
            <button 
                type="button" 
                onclick="window.closeModal('modal-detail-company')"
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

    function showCompanyDetail(data) {
        document.getElementById('detail-company-nama').innerText = data.nama;
        document.getElementById('detail-company-email').innerText = data.email;
        document.getElementById('detail-company-bidang').innerText = data.bidang;
        document.getElementById('detail-company-kota').innerText = data.kota;
        document.getElementById('detail-company-status').innerText = data.status;
        document.getElementById('detail-company-jobs').innerText = data.jobs_count;
        document.getElementById('detail-company-situs').innerText = data.situs;
        document.getElementById('detail-company-alamat').innerText = data.alamat;
        document.getElementById('detail-company-avatar').innerText = (data.nama || 'PT').substring(0, 2).toUpperCase();
        window.openModal('modal-detail-company');
    }

    function editCompany(data) {
        document.getElementById('form-edit-company').action = data.update_url;
        document.getElementById('edit-company-nama').value = data.nama;
        document.getElementById('edit-company-email').value = data.email;
        document.getElementById('edit-company-bidang').value = data.bidang;
        document.getElementById('edit-company-kota').value = data.kota;
        document.getElementById('edit-company-alamat').value = data.alamat;
        document.getElementById('edit-company-situs').value = data.situs;
        document.getElementById('edit-company-deskripsi').value = data.deskripsi;
        
        window.setSelectValue('edit-company-status', data.status_verifikasi);

        window.openModal('modal-edit-company');
    }
</script>
@endpush
@endsection
