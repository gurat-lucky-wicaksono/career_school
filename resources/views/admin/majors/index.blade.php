@extends('layouts.admin')

@section('title', 'Data Jurusan & Program Keahlian')

@section('content')
<div class="space-y-6">
    @php
        $headers = [
            ['key' => 'kode', 'label' => 'Kode', 'sortable' => true],
            ['key' => 'nama', 'label' => 'Program Keahlian', 'sortable' => true],
            ['key' => 'kepala_jurusan', 'label' => 'Kepala Program Keahlian', 'sortable' => true],
            ['key' => 'classes_count', 'label' => 'Jumlah Rombel', 'sortable' => false],
            ['key' => 'students_count', 'label' => 'Total Siswa', 'sortable' => false],
        ];
    @endphp

    <x-admin.data-table 
        title="Program Keahlian & Jurusan"
        subtitle="Manajemen kompetensi kejuruan vokasi SMK Negeri 1 Cirebon"
        searchPlaceholder="Cari kode atau nama jurusan..."
        :search="request('search')"
        :items="$majors"
        :headers="$headers"
        :currentSort="$sortField"
        :currentDirection="$sortDirection"
    >
        <x-slot:checkboxAll>
            <input 
                type="checkbox" 
                id="select-all-majors"
                onclick="toggleSelectAll(this)"
                aria-label="Pilih Semua Jurusan"
            >
        </x-slot:checkboxAll>

        <x-slot:action>
            <x-ui.button variant="primary" onclick="window.openModal('modal-create-major')">
                Tambah Jurusan
            </x-ui.button>
        </x-slot:action>

        @forelse ($majors as $major)
            <tr class="hover:bg-slate-50/80 dark:hover:bg-neutral-800/40 transition-colors group">
                <td class="w-12 px-4 py-3.5 text-center">
                    <input 
                        type="checkbox" 
                        aria-label="Pilih jurusan {{ $major->nama }}"
                    >
                </td>

                <td class="px-5 py-3.5 font-mono text-xs font-bold text-slate-900 dark:text-white">
                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-neutral-800 border border-slate-200/80 dark:border-white/10">
                        {{ $major->kode }}
                    </span>
                </td>

                <td class="px-5 py-3.5">
                    <div>
                        <span class="font-semibold text-slate-900 dark:text-white block">
                            {{ $major->nama }}
                        </span>
                        @if ($major->keterangan)
                            <span class="text-xs text-slate-500 dark:text-slate-400 block line-clamp-1">
                                {{ $major->keterangan }}
                            </span>
                        @endif
                    </div>
                </td>

                <td class="px-5 py-3.5 text-slate-700 dark:text-slate-200 text-xs">
                    {{ $major->kepala_jurusan ?? '-' }}
                </td>

                <td class="px-5 py-3.5 font-medium text-slate-700 dark:text-slate-200 text-xs font-mono">
                    {{ $major->classes_count }} Rombel
                </td>

                <td class="px-5 py-3.5 font-medium text-slate-700 dark:text-slate-200 text-xs font-mono">
                    {{ $major->students_count }} Siswa
                </td>

                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                        <x-ui.icon-button 
                            size="sm" 
                            icon="eye" 
                            label="Lihat Detail Jurusan" 
                            onclick="showMajorDetail({{ json_encode([
                                'kode' => $major->kode,
                                'nama' => $major->nama,
                                'kepala_jurusan' => $major->kepala_jurusan ?? '-',
                                'keterangan' => $major->keterangan ?? 'Tidak ada keterangan khusus.',
                                'classes_count' => $major->classes_count . ' Rombongan Belajar',
                                'students_count' => $major->students_count . ' Siswa Aktif',
                            ]) }})"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            icon="edit-3" 
                            label="Ubah Data Jurusan" 
                            onclick="editMajor({{ json_encode([
                                'id' => $major->id,
                                'update_url' => route('admin.majors.update', $major->id),
                                'kode' => $major->kode,
                                'nama' => $major->nama,
                                'kepala_jurusan' => $major->kepala_jurusan ?? '',
                                'keterangan' => $major->keterangan ?? '',
                            ]) }})"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            variant="danger" 
                            icon="trash-2" 
                            label="Hapus Data Jurusan" 
                            onclick="window.submitDeleteForm('{{ route('admin.majors.destroy', $major->id) }}', 'Jurusan {{ addslashes($major->nama) }}')"
                        />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-12">
                    <x-ui.empty-state 
                        title="Belum Ada Data Jurusan"
                        description="Data program keahlian belum tercatat atau hasil pencarian tidak ditemukan."
                        actionLabel="Tambah Jurusan Baru"
                        actionUrl="javascript:window.openModal('modal-create-major')"
                    />
                </td>
            </tr>
        @endforelse
    </x-admin.data-table>
</div>

<!-- Modal 1: Tambah Jurusan -->
<x-ui.modal 
    id="modal-create-major" 
    title="Tambah Program Keahlian Baru" 
    subtitle="Entri kompetensi keahlian dan kepengurusan jurusan SMK"
    size="md"
>
    <form method="POST" action="{{ route('admin.majors.store') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Kode Singkat
                </label>
                <input 
                    type="text" 
                    name="kode" 
                    required 
                    placeholder="PPLG"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary uppercase font-mono text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="sm:col-span-2 space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Nama Program Keahlian
                </label>
                <input 
                    type="text" 
                    name="nama" 
                    required 
                    placeholder="Pengembangan Perangkat Lunak & Gim"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Kepala Program Keahlian
            </label>
            <input 
                type="text" 
                name="kepala_jurusan" 
                placeholder="Nama Kaprog beserta Gelar"
                class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
            >
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Keterangan / Deskripsi Singkat
            </label>
            <textarea 
                name="keterangan" 
                rows="3"
                placeholder="Deskripsi ruang lingkup kurikulum kejuruan..."
                class="w-full p-4 rounded-2xl bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
            ></textarea>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-end gap-3">
            <button 
                type="button" 
                onclick="window.closeModal('modal-create-major')"
                class="px-5 py-2.5 text-xs sm:text-sm font-semibold rounded-full border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg transition-colors focus:outline-none"
            >
                Batal
            </button>
            <button 
                type="submit" 
                class="px-6 py-2.5 text-xs sm:text-sm font-semibold rounded-full bg-brand-primary text-text-on-brand-primary hover:brightness-105 transition-all shadow-md shadow-brand-primary/20 focus:outline-none"
            >
                Simpan Jurusan
            </button>
        </div>
    </form>
</x-ui.modal>

<!-- Modal 2: Edit Jurusan -->
<x-ui.modal 
    id="modal-edit-major" 
    title="Ubah Data Program Keahlian" 
    subtitle="Perbarui identitas dan informasi kompetensi keahlian"
    size="md"
>
    <form id="form-edit-major" method="POST" action="" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Kode Singkat
                </label>
                <input 
                    type="text" 
                    id="edit-major-kode" 
                    name="kode" 
                    required 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary uppercase font-mono text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="sm:col-span-2 space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Nama Program Keahlian
                </label>
                <input 
                    type="text" 
                    id="edit-major-nama" 
                    name="nama" 
                    required 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Kepala Program Keahlian
            </label>
            <input 
                type="text" 
                id="edit-major-kaprog" 
                name="kepala_jurusan" 
                class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
            >
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Keterangan
            </label>
            <textarea 
                id="edit-major-keterangan" 
                name="keterangan" 
                rows="3"
                class="w-full p-4 rounded-2xl bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
            ></textarea>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-end gap-3">
            <button 
                type="button" 
                onclick="window.closeModal('modal-edit-major')"
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

<!-- Modal 3: Detail Jurusan -->
<x-ui.modal 
    id="modal-detail-major" 
    title="Detail Program Keahlian" 
    subtitle="Rincian informasi konsentrasi kejuruan"
    size="md"
>
    <div class="space-y-4">
        <div class="p-4 rounded-2xl bg-surface-bg border border-border-default">
            <span id="detail-major-kode" class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg bg-brand-primary/20 text-brand-secondary dark:text-brand-primary">
                KODE
            </span>
            <h4 id="detail-major-nama" class="font-bold text-text-primary text-lg mt-2">
                Nama Jurusan
            </h4>
        </div>

        <div class="divide-y divide-border-subtle text-xs sm:text-sm">
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Kepala Program:</span>
                <span id="detail-major-kaprog" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Jumlah Rombel:</span>
                <span id="detail-major-rombel" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Kapasitas Siswa:</span>
                <span id="detail-major-siswa" class="font-semibold text-text-primary">-</span>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-text-secondary mb-1">Keterangan:</label>
            <p id="detail-major-keterangan" class="text-xs text-text-primary leading-relaxed bg-surface-bg p-4 rounded-2xl border border-border-default">
                -
            </p>
        </div>

        <div class="pt-4 border-t border-border-subtle flex justify-end">
            <button 
                type="button" 
                onclick="window.closeModal('modal-detail-major')"
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

    function showMajorDetail(data) {
        document.getElementById('detail-major-kode').innerText = data.kode;
        document.getElementById('detail-major-nama').innerText = data.nama;
        document.getElementById('detail-major-kaprog').innerText = data.kepala_jurusan;
        document.getElementById('detail-major-rombel').innerText = data.classes_count;
        document.getElementById('detail-major-siswa').innerText = data.students_count;
        document.getElementById('detail-major-keterangan').innerText = data.keterangan;
        window.openModal('modal-detail-major');
    }

    function editMajor(data) {
        document.getElementById('form-edit-major').action = data.update_url;
        document.getElementById('edit-major-kode').value = data.kode;
        document.getElementById('edit-major-nama').value = data.nama;
        document.getElementById('edit-major-kaprog').value = data.kepala_jurusan;
        document.getElementById('edit-major-keterangan').value = data.keterangan;
        window.openModal('modal-edit-major');
    }
</script>
@endpush
@endsection
