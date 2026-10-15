@extends('layouts.admin')

@section('title', 'Master Keahlian & Keterampilan')

@section('content')
<div class="space-y-6">
    @php
        $headers = [
            ['key' => 'nama', 'label' => 'Nama Keahlian / Skill', 'sortable' => true],
            ['key' => 'kategori', 'label' => 'Kategori Bidang', 'sortable' => true],
            ['key' => 'students_count', 'label' => 'Siswa Memiliki', 'sortable' => true],
            ['key' => 'jobs_count', 'label' => 'Kebutuhan DUDI', 'sortable' => true],
        ];
        $isFilterActive = request()->filled('filter_kategori');
    @endphp

    <x-admin.data-table 
        title="Kompetensi & Keahlian Siswa"
        subtitle="Daftar keahlian teknis dan standar industri SMK Negeri 1 Cirebon"
        searchPlaceholder="Cari keahlian atau kategori..."
        :search="request('search')"
        :items="$skills"
        :headers="$headers"
        :currentSort="$sortField"
        :currentDirection="$sortDirection"
    >
        <x-slot:checkboxAll>
            <input 
                type="checkbox" 
                id="select-all-skills"
                onclick="toggleSelectAll(this)"
                aria-label="Pilih Semua Keahlian"
            >
        </x-slot:checkboxAll>

        <x-slot:filter>
            <button 
                type="button" 
                onclick="window.openModal('modal-filter-skills')"
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
            <x-ui.button variant="primary" onclick="window.openModal('modal-create-skill')">
                Tambah Keahlian
            </x-ui.button>
        </x-slot:action>

        @forelse ($skills as $skill)
            <tr class="hover:bg-slate-50/80 dark:hover:bg-neutral-800/40 transition-colors group">
                <td class="w-12 px-4 py-3.5 text-center">
                    <input 
                        type="checkbox" 
                        aria-label="Pilih keahlian {{ $skill->nama }}"
                    >
                </td>

                <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-brand-primary"></span>
                        <span>{{ $skill->nama }}</span>
                    </div>
                </td>

                <td class="px-5 py-3.5">
                    @if ($skill->kategori)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-white/10">
                            {{ $skill->kategori }}
                        </span>
                    @else
                        <span class="text-xs text-slate-400">Umum</span>
                    @endif
                </td>

                <td class="px-5 py-3.5 font-mono text-xs text-slate-700 dark:text-slate-200">
                    {{ $skill->students_count }} Siswa
                </td>

                <td class="px-5 py-3.5 font-mono text-xs text-slate-700 dark:text-slate-200">
                    {{ $skill->jobs_count }} Lowongan
                </td>

                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                        <x-ui.icon-button 
                            size="sm" 
                            icon="eye" 
                            label="Lihat Detail Keahlian" 
                            onclick="showSkillDetail({{ json_encode([
                                'nama' => $skill->nama,
                                'kategori' => $skill->kategori ?? 'Umum',
                                'students_count' => $skill->students_count . ' Siswa Tersertifikasi/Memiliki',
                                'jobs_count' => $skill->jobs_count . ' Lowongan Membutuhkan',
                            ]) }})"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            icon="edit-3" 
                            label="Ubah Data Keahlian" 
                            onclick="editSkill({{ json_encode([
                                'id' => $skill->id,
                                'update_url' => route('admin.skills.update', $skill->id),
                                'nama' => $skill->nama,
                                'kategori' => $skill->kategori ?? '',
                            ]) }})"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            variant="danger" 
                            icon="trash-2" 
                            label="Hapus Data Keahlian" 
                            onclick="window.submitDeleteForm('{{ route('admin.skills.destroy', $skill->id) }}', 'Keahlian {{ addslashes($skill->nama) }}')"
                        />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="py-12">
                    <x-ui.empty-state 
                        title="Belum Ada Data Keahlian"
                        description="Kompetensi keahlian belum tercatat atau hasil pencarian tidak ditemukan."
                        actionLabel="Tambah Keahlian Baru"
                        actionUrl="javascript:window.openModal('modal-create-skill')"
                    />
                </td>
            </tr>
        @endforelse
    </x-admin.data-table>
</div>

<!-- Modal 1: Filter Keahlian -->
<x-ui.modal 
    id="modal-filter-skills" 
    title="Filter Keahlian" 
    subtitle="Saring kompetensi kejuruan berdasarkan kategori bidang"
    size="md"
    overflow="visible"
>
    <form method="GET" action="{{ route('admin.skills.index') }}" class="space-y-4">
        @if (request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        @if (request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
        @endif

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Kategori Bidang
            </label>
            <select 
                name="filter_kategori" 
                class="choices-select w-full"
            >
                <option value="">Semua Kategori</option>
                @foreach ($kategoris as $kat)
                    <option value="{{ $kat }}" {{ request('filter_kategori') === $kat ? 'selected' : '' }}>
                        {{ $kat }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-between gap-3">
            <a 
                href="{{ route('admin.skills.index') }}" 
                class="px-4 py-2 text-xs font-semibold text-text-secondary hover:text-text-primary transition-colors"
            >
                Reset Filter
            </a>
            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    onclick="window.closeModal('modal-filter-skills')"
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

<!-- Modal 2: Tambah Keahlian -->
<x-ui.modal 
    id="modal-create-skill" 
    title="Tambah Kompetensi Keahlian" 
    subtitle="Registrasi keahlian industri untuk pemetaan kompetensi siswa"
    size="md"
>
    <form method="POST" action="{{ route('admin.skills.store') }}" class="space-y-4">
        @csrf

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Nama Keahlian / Skill
            </label>
            <input 
                type="text" 
                name="nama" 
                required 
                placeholder="Contoh: Laravel Framework, UI/UX Design, Mikrotik MTCNA"
                class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
            >
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Kategori Bidang Keahlian
            </label>
            <input 
                type="text" 
                name="kategori" 
                placeholder="Contoh: Pemrograman Web, Jaringan Komputer, Desain Grafis"
                class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
            >
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-end gap-3">
            <button 
                type="button" 
                onclick="window.closeModal('modal-create-skill')"
                class="px-5 py-2.5 text-xs sm:text-sm font-semibold rounded-full border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg transition-colors focus:outline-none"
            >
                Batal
            </button>
            <button 
                type="submit" 
                class="px-6 py-2.5 text-xs sm:text-sm font-semibold rounded-full bg-brand-primary text-text-on-brand-primary hover:brightness-105 transition-all shadow-md shadow-brand-primary/20 focus:outline-none"
            >
                Simpan Keahlian
            </button>
        </div>
    </form>
</x-ui.modal>

<!-- Modal 3: Edit Keahlian -->
<x-ui.modal 
    id="modal-edit-skill" 
    title="Ubah Data Keahlian" 
    subtitle="Perbarui nama kompetensi dan kategori keahlian"
    size="md"
>
    <form id="form-edit-skill" method="POST" action="" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Nama Keahlian / Skill
            </label>
            <input 
                type="text" 
                id="edit-skill-nama" 
                name="nama" 
                required 
                class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
            >
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Kategori Bidang
            </label>
            <input 
                type="text" 
                id="edit-skill-kategori" 
                name="kategori" 
                class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
            >
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-end gap-3">
            <button 
                type="button" 
                onclick="window.closeModal('modal-edit-skill')"
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

<!-- Modal 4: Detail Keahlian -->
<x-ui.modal 
    id="modal-detail-skill" 
    title="Detail Kompetensi Keahlian" 
    subtitle="Rincian informasi keahlian vokasi"
    size="md"
>
    <div class="space-y-4">
        <div class="p-4 rounded-2xl bg-surface-bg border border-border-default">
            <h4 id="detail-skill-nama" class="font-bold text-text-primary text-lg">
                Nama Keahlian
            </h4>
            <p id="detail-skill-kategori" class="text-xs text-text-secondary mt-1">
                Kategori
            </p>
        </div>

        <div class="divide-y divide-border-subtle text-xs sm:text-sm">
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Total Siswa Memiliki:</span>
                <span id="detail-skill-students" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Kebutuhan Industri (Loker):</span>
                <span id="detail-skill-jobs" class="font-semibold text-text-primary">-</span>
            </div>
        </div>

        <div class="pt-4 border-t border-border-subtle flex justify-end">
            <button 
                type="button" 
                onclick="window.closeModal('modal-detail-skill')"
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

    function showSkillDetail(data) {
        document.getElementById('detail-skill-nama').innerText = data.nama;
        document.getElementById('detail-skill-kategori').innerText = 'Kategori: ' + data.kategori;
        document.getElementById('detail-skill-students').innerText = data.students_count;
        document.getElementById('detail-skill-jobs').innerText = data.jobs_count;
        window.openModal('modal-detail-skill');
    }

    function editSkill(data) {
        document.getElementById('form-edit-skill').action = data.update_url;
        document.getElementById('edit-skill-nama').value = data.nama;
        document.getElementById('edit-skill-kategori').value = data.kategori;
        window.openModal('modal-edit-skill');
    }
</script>
@endpush
@endsection
