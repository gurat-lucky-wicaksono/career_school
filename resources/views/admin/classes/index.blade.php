@extends('layouts.admin')

@section('title', 'Data Kelas & Rombongan Belajar')

@section('content')
<div class="space-y-6">
    @php
        $headers = [
            ['key' => 'nama', 'label' => 'Nama Rombel / Kelas', 'sortable' => true],
            ['key' => 'major', 'label' => 'Konsentrasi Keahlian', 'sortable' => false],
            ['key' => 'tingkat', 'label' => 'Tingkat', 'sortable' => true],
            ['key' => 'wali_kelas', 'label' => 'Wali Kelas', 'sortable' => false],
            ['key' => 'tahun_ajaran', 'label' => 'Tahun Ajaran', 'sortable' => true],
            ['key' => 'students_count', 'label' => 'Jumlah Siswa', 'sortable' => true],
        ];
        $isFilterActive = request()->filled('filter_major') || request()->filled('filter_tingkat') || request()->filled('filter_tahun');
    @endphp

    <x-admin.data-table 
        title="Rombongan Belajar & Kelas"
        subtitle="Manajemen data kelas vokasi dan wali kelas SMK Negeri 1 Cirebon"
        searchPlaceholder="Cari nama kelas atau tahun ajaran..."
        :search="request('search')"
        :items="$classes"
        :headers="$headers"
        :currentSort="$sortField"
        :currentDirection="$sortDirection"
    >
        <x-slot:checkboxAll>
            <input 
                type="checkbox" 
                id="select-all-classes"
                onclick="toggleSelectAll(this)"
                aria-label="Pilih Semua Kelas"
            >
        </x-slot:checkboxAll>

        <x-slot:filter>
            <button 
                type="button" 
                onclick="window.openModal('modal-filter-classes')"
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
            <x-ui.button variant="primary" onclick="window.openModal('modal-create-class')">
                Tambah Kelas
            </x-ui.button>
        </x-slot:action>

        @forelse ($classes as $class)
            <tr class="hover:bg-slate-50/80 dark:hover:bg-neutral-800/40 transition-colors group">
                <td class="w-12 px-4 py-3.5 text-center">
                    <input 
                        type="checkbox" 
                        aria-label="Pilih kelas {{ $class->nama }}"
                    >
                </td>

                <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                    {{ $class->nama }}
                </td>

                <td class="px-5 py-3.5 text-xs text-slate-600 dark:text-slate-300">
                    <span class="font-medium text-slate-800 dark:text-slate-200">{{ $class->major->nama ?? '-' }}</span>
                    <span class="block text-[11px] text-slate-400">({{ $class->major->kode ?? '-' }})</span>
                </td>

                <td class="px-5 py-3.5 font-mono text-xs font-semibold text-slate-700 dark:text-slate-200">
                    Kelas {{ $class->tingkat }}
                </td>

                <td class="px-5 py-3.5 text-xs text-slate-700 dark:text-slate-300">
                    {{ $class->waliKelas->user->name ?? 'Belum Ditentukan' }}
                </td>

                <td class="px-5 py-3.5 font-mono text-xs text-slate-600 dark:text-slate-400">
                    {{ $class->tahun_ajaran }}
                </td>

                <td class="px-5 py-3.5 font-mono text-xs font-semibold text-slate-800 dark:text-slate-200">
                    {{ $class->students_count }} Siswa
                </td>

                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                        <x-ui.icon-button 
                            size="sm" 
                            icon="eye" 
                            label="Lihat Detail Kelas" 
                            onclick="showClassDetail({{ json_encode([
                                'nama' => $class->nama,
                                'major' => ($class->major->kode ?? '-') . ' - ' . ($class->major->nama ?? '-'),
                                'tingkat' => 'Tingkat ' . $class->tingkat,
                                'wali_kelas' => $class->waliKelas->user->name ?? 'Belum Ditugaskan',
                                'tahun_ajaran' => $class->tahun_ajaran,
                                'students_count' => $class->students_count . ' Siswa Terdaftar',
                            ]) }})"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            icon="edit-3" 
                            label="Ubah Data Kelas" 
                            onclick="editClass({{ json_encode([
                                'id' => $class->id,
                                'update_url' => route('admin.classes.update', $class->id),
                                'nama' => $class->nama,
                                'major_id' => $class->major_id,
                                'wali_kelas_id' => $class->wali_kelas_id,
                                'tingkat' => $class->tingkat,
                                'tahun_ajaran' => $class->tahun_ajaran,
                            ]) }})"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            variant="danger" 
                            icon="trash-2" 
                            label="Hapus Data Kelas" 
                            onclick="window.submitDeleteForm('{{ route('admin.classes.destroy', $class->id) }}', 'Kelas {{ addslashes($class->nama) }}')"
                        />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="py-12">
                    <x-ui.empty-state 
                        title="Belum Ada Data Kelas"
                        description="Data rombongan belajar belum tercatat atau hasil pencarian tidak ditemukan."
                        actionLabel="Tambah Kelas Baru"
                        actionUrl="javascript:window.openModal('modal-create-class')"
                    />
                </td>
            </tr>
        @endforelse
    </x-admin.data-table>
</div>

<!-- Modal 1: Filter Kelas -->
<x-ui.modal 
    id="modal-filter-classes" 
    title="Filter Data Kelas" 
    subtitle="Saring rombel berdasarkan jurusan, tingkat, atau tahun ajaran"
    size="md"
    overflow="visible"
>
    <form method="GET" action="{{ route('admin.classes.index') }}" class="space-y-4">
        @if (request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        @if (request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
        @endif

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Konsentrasi Keahlian (Jurusan)
            </label>
            <select 
                name="filter_major" 
                class="choices-select w-full"
            >
                <option value="">Semua Jurusan</option>
                @foreach ($majors as $m)
                    <option value="{{ $m->id }}" {{ request('filter_major') == $m->id ? 'selected' : '' }}>
                        {{ $m->nama }} ({{ $m->kode }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Tingkat Kelas
                </label>
                <select 
                    name="filter_tingkat" 
                    class="choices-select w-full"
                >
                    <option value="">Semua</option>
                    <option value="10" {{ request('filter_tingkat') == '10' ? 'selected' : '' }}>Kelas 10 (X)</option>
                    <option value="11" {{ request('filter_tingkat') == '11' ? 'selected' : '' }}>Kelas 11 (XI)</option>
                    <option value="12" {{ request('filter_tingkat') == '12' ? 'selected' : '' }}>Kelas 12 (XII)</option>
                    <option value="13" {{ request('filter_tingkat') == '13' ? 'selected' : '' }}>Kelas 13 (XIII)</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Tahun Ajaran
                </label>
                <select 
                    name="filter_tahun" 
                    class="choices-select w-full"
                >
                    <option value="">Semua</option>
                    @foreach ($tahunAjarans as $tahun)
                        <option value="{{ $tahun }}" {{ request('filter_tahun') == $tahun ? 'selected' : '' }}>
                            {{ $tahun }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-between gap-3">
            <a 
                href="{{ route('admin.classes.index') }}" 
                class="px-4 py-2 text-xs font-semibold text-text-secondary hover:text-text-primary transition-colors"
            >
                Reset Filter
            </a>
            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    onclick="window.closeModal('modal-filter-classes')"
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

<!-- Modal 2: Tambah Kelas -->
<x-ui.modal 
    id="modal-create-class" 
    title="Tambah Rombongan Belajar Baru" 
    subtitle="Registrasi kelas vokasi dan penetapan wali kelas"
    size="md"
>
    <form method="POST" action="{{ route('admin.classes.store') }}" class="space-y-4">
        @csrf

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Nama Kelas / Rombel
            </label>
            <input 
                type="text" 
                name="nama" 
                required 
                placeholder="Contoh: XII PPLG 1"
                class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
            >
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Konsentrasi Keahlian (Jurusan)
            </label>
            <select 
                name="major_id" 
                required
                class="choices-select w-full"
            >
                <option value="">Pilih Jurusan</option>
                @foreach ($majors as $m)
                    <option value="{{ $m->id }}">{{ $m->kode }} - {{ $m->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Tingkat
                </label>
                <select 
                    name="tingkat" 
                    required
                    class="choices-select w-full"
                >
                    <option value="10">Kelas 10 (X)</option>
                    <option value="11">Kelas 11 (XI)</option>
                    <option value="12" selected>Kelas 12 (XII)</option>
                    <option value="13">Kelas 13 (XIII)</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Tahun Ajaran
                </label>
                <input 
                    type="text" 
                    name="tahun_ajaran" 
                    required 
                    value="2025/2026"
                    placeholder="2025/2026"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary font-mono text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Wali Kelas
            </label>
            <select 
                name="wali_kelas_id" 
                class="choices-select w-full"
            >
                <option value="">Pilih Wali Kelas (Opsional)</option>
                @foreach ($teachers as $t)
                    <option value="{{ $t->id }}">{{ $t->user->name ?? 'Guru' }} ({{ $t->nip }})</option>
                @endforeach
            </select>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-end gap-3">
            <button 
                type="button" 
                onclick="window.closeModal('modal-create-class')"
                class="px-5 py-2.5 text-xs sm:text-sm font-semibold rounded-full border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg transition-colors focus:outline-none"
            >
                Batal
            </button>
            <button 
                type="submit" 
                class="px-6 py-2.5 text-xs sm:text-sm font-semibold rounded-full bg-brand-primary text-text-on-brand-primary hover:brightness-105 transition-all shadow-md shadow-brand-primary/20 focus:outline-none"
            >
                Simpan Kelas
            </button>
        </div>
    </form>
</x-ui.modal>

<!-- Modal 3: Edit Kelas -->
<x-ui.modal 
    id="modal-edit-class" 
    title="Ubah Data Kelas" 
    subtitle="Perbarui identitas kelas dan penugasan wali kelas"
    size="md"
>
    <form id="form-edit-class" method="POST" action="" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Nama Kelas / Rombel
            </label>
            <input 
                type="text" 
                id="edit-class-nama" 
                name="nama" 
                required 
                class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
            >
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Konsentrasi Keahlian (Jurusan)
            </label>
            <select 
                id="edit-class-major" 
                name="major_id" 
                required
                class="choices-select w-full"
            >
                @foreach ($majors as $m)
                    <option value="{{ $m->id }}">{{ $m->kode }} - {{ $m->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Tingkat
                </label>
                <select 
                    id="edit-class-tingkat" 
                    name="tingkat" 
                    required
                    class="choices-select w-full"
                >
                    <option value="10">Kelas 10 (X)</option>
                    <option value="11">Kelas 11 (XI)</option>
                    <option value="12">Kelas 12 (XII)</option>
                    <option value="13">Kelas 13 (XIII)</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Tahun Ajaran
                </label>
                <input 
                    type="text" 
                    id="edit-class-tahun" 
                    name="tahun_ajaran" 
                    required 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary font-mono text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Wali Kelas
            </label>
            <select 
                id="edit-class-wali" 
                name="wali_kelas_id" 
                class="choices-select w-full"
            >
                <option value="">Pilih Wali Kelas (Opsional)</option>
                @foreach ($teachers as $t)
                    <option value="{{ $t->id }}">{{ $t->user->name ?? 'Guru' }} ({{ $t->nip }})</option>
                @endforeach
            </select>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-end gap-3">
            <button 
                type="button" 
                onclick="window.closeModal('modal-edit-class')"
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

<!-- Modal 4: Detail Kelas -->
<x-ui.modal 
    id="modal-detail-class" 
    title="Detail Rombongan Belajar" 
    subtitle="Rincian informasi data kelas dan wali kelas"
    size="md"
>
    <div class="space-y-4">
        <div class="p-4 rounded-2xl bg-surface-bg border border-border-default">
            <h4 id="detail-class-nama" class="font-bold text-text-primary text-lg">
                Nama Kelas
            </h4>
            <p id="detail-class-major" class="text-xs text-text-secondary mt-1">
                Jurusan
            </p>
        </div>

        <div class="divide-y divide-border-subtle text-xs sm:text-sm">
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Tingkat:</span>
                <span id="detail-class-tingkat" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Wali Kelas:</span>
                <span id="detail-class-wali" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Tahun Ajaran:</span>
                <span id="detail-class-tahun" class="font-mono font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Jumlah Siswa:</span>
                <span id="detail-class-siswa" class="font-semibold text-text-primary">-</span>
            </div>
        </div>

        <div class="pt-4 border-t border-border-subtle flex justify-end">
            <button 
                type="button" 
                onclick="window.closeModal('modal-detail-class')"
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

    function showClassDetail(data) {
        document.getElementById('detail-class-nama').innerText = data.nama;
        document.getElementById('detail-class-major').innerText = data.major;
        document.getElementById('detail-class-tingkat').innerText = data.tingkat;
        document.getElementById('detail-class-wali').innerText = data.wali_kelas;
        document.getElementById('detail-class-tahun').innerText = data.tahun_ajaran;
        document.getElementById('detail-class-siswa').innerText = data.students_count;
        window.openModal('modal-detail-class');
    }

    function editClass(data) {
        document.getElementById('form-edit-class').action = data.update_url;
        document.getElementById('edit-class-nama').value = data.nama;
        document.getElementById('edit-class-tahun').value = data.tahun_ajaran;
        
        window.setSelectValue('edit-class-major', data.major_id);
        window.setSelectValue('edit-class-tingkat', data.tingkat);
        window.setSelectValue('edit-class-wali', data.wali_kelas_id || '');

        window.openModal('modal-edit-class');
    }
</script>
@endpush
@endsection
