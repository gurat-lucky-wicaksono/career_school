@extends('layouts.admin')

@section('title', 'Data Guru & Pendidik')

@section('content')
<div class="space-y-6">
    @php
        $headers = [
            ['key' => 'name', 'label' => 'Nama & Identitas Guru', 'sortable' => false],
            ['key' => 'nip', 'label' => 'NIP', 'sortable' => true],
            ['key' => 'mata_pelajaran', 'label' => 'Mata Pelajaran / Kejuruan', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => false],
        ];
        $isFilterActive = request()->filled('filter_mapel') || (request()->has('filter_wali') && request('filter_wali') !== '');
    @endphp

    <x-admin.data-table 
        title="Data Guru & Tenaga Pendidik"
        subtitle="Manajemen data pendidik kejuruan dan wali kelas SMK Negeri 1 Cirebon"
        searchPlaceholder="Cari NIP, nama guru, atau mata pelajaran..."
        :search="request('search')"
        :items="$teachers"
        :headers="$headers"
        :currentSort="$sortField"
        :currentDirection="$sortDirection"
    >
        <x-slot:checkboxAll>
            <input 
                type="checkbox" 
                id="select-all-teachers"
                onclick="toggleSelectAll(this)"
                aria-label="Pilih Semua Data Guru"
            >
        </x-slot:checkboxAll>

        <x-slot:filter>
            <button 
                type="button" 
                onclick="window.openModal('modal-filter-teachers')"
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
            <x-ui.button variant="primary" onclick="window.openModal('modal-create-teacher')">
                Tambah Guru
            </x-ui.button>
        </x-slot:action>

        @forelse ($teachers as $teacher)
            <tr class="hover:bg-slate-50/80 dark:hover:bg-neutral-800/40 transition-colors group">
                <td class="w-12 px-4 py-3.5 text-center">
                    <input 
                        type="checkbox" 
                        aria-label="Pilih guru {{ $teacher->user->name ?? '' }}"
                    >
                </td>

                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-200 font-semibold text-xs flex items-center justify-center flex-shrink-0 border border-slate-200/80 dark:border-white/10">
                            {{ strtoupper(substr($teacher->user->name ?? 'G', 0, 2)) }}
                        </div>
                        <div>
                            <span class="font-semibold text-slate-900 dark:text-white block">
                                {{ $teacher->user->name ?? 'Guru Kejuruan' }}
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 block font-mono">
                                {{ $teacher->user->email ?? '-' }}
                            </span>
                        </div>
                    </div>
                </td>

                <td class="px-5 py-3.5 font-mono text-xs text-slate-600 dark:text-slate-300">
                    {{ $teacher->nip ?? '-' }}
                </td>

                <td class="px-5 py-3.5 font-medium text-slate-700 dark:text-slate-200">
                    {{ $teacher->mata_pelajaran ?? '-' }}
                    @if ($teacher->is_wali_kelas)
                        <span class="ml-1 px-1.5 py-0.5 text-[10px] font-semibold rounded-md bg-brand-primary/20 text-emerald-800 dark:text-brand-primary">Wali Kelas</span>
                    @endif
                </td>

                <td class="px-5 py-3.5">
                    <x-ui.badge variant="success">
                        Aktif
                    </x-ui.badge>
                </td>

                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                        <x-ui.icon-button 
                            size="sm" 
                            icon="eye" 
                            label="Lihat Detail Guru" 
                            onclick="showTeacherDetail({{ json_encode([
                                'name' => $teacher->user->name ?? '-',
                                'email' => $teacher->user->email ?? '-',
                                'username' => $teacher->user->username ?? '-',
                                'nip' => $teacher->nip ?? '-',
                                'mata_pelajaran' => $teacher->mata_pelajaran ?? '-',
                                'is_wali_kelas' => $teacher->is_wali_kelas ? 'Ya (Wali Kelas)' : 'Bukan Wali Kelas',
                                'created_at' => $teacher->created_at ? $teacher->created_at->translatedFormat('d F Y H:i') : '-',
                            ]) }})"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            icon="edit-3" 
                            label="Ubah Data Guru" 
                            onclick="editTeacher({{ json_encode([
                                'id' => $teacher->id,
                                'update_url' => route('admin.teachers.update', $teacher->id),
                                'name' => $teacher->user->name ?? '',
                                'email' => $teacher->user->email ?? '',
                                'nip' => $teacher->nip ?? '',
                                'mata_pelajaran' => $teacher->mata_pelajaran ?? '',
                                'is_wali_kelas' => (bool)$teacher->is_wali_kelas,
                            ]) }})"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            variant="danger" 
                            icon="trash-2" 
                            label="Hapus Data Guru" 
                            onclick="window.submitDeleteForm('{{ route('admin.teachers.destroy', $teacher->id) }}', 'Guru {{ addslashes($teacher->user->name ?? 'ini') }}')"
                        />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="py-12">
                    <x-ui.empty-state 
                        title="Belum Ada Data Guru"
                        description="Data tenaga pendidik belum tercatat atau hasil pencarian tidak ditemukan."
                        actionLabel="Tambah Guru Baru"
                        actionUrl="javascript:window.openModal('modal-create-teacher')"
                    />
                </td>
            </tr>
        @endforelse
    </x-admin.data-table>
</div>

<!-- Modal 1: Filter Data Guru -->
<x-ui.modal 
    id="modal-filter-teachers" 
    title="Filter Data Guru" 
    subtitle="Saring daftar tenaga pendidik berdasarkan mata pelajaran dan status keaktifan"
    size="md"
    overflow="visible"
>
    <form method="GET" action="{{ route('admin.teachers.index') }}" class="space-y-4">
        @if (request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        @if (request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
        @endif

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Mata Pelajaran / Keahlian
            </label>
            <select 
                name="filter_mapel" 
                class="choices-select w-full"
            >
                <option value="">Semua Mata Pelajaran</option>
                @foreach ($availableMapels as $mapel)
                    <option value="{{ $mapel }}" {{ request('filter_mapel') === $mapel ? 'selected' : '' }}>
                        {{ $mapel }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Tugas Tambahan (Wali Kelas)
            </label>
            <select 
                name="filter_wali" 
                class="choices-select w-full"
            >
                <option value="">Semua Status</option>
                <option value="1" {{ request('filter_wali') === '1' ? 'selected' : '' }}>Hanya Wali Kelas</option>
                <option value="0" {{ request('filter_wali') === '0' ? 'selected' : '' }}>Bukan Wali Kelas</option>
            </select>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-between gap-3">
            <a 
                href="{{ route('admin.teachers.index') }}" 
                class="px-4 py-2 text-xs font-semibold text-text-secondary hover:text-text-primary transition-colors"
            >
                Reset Filter
            </a>
            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    onclick="window.closeModal('modal-filter-teachers')"
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

<!-- Modal 2: Tambah Guru Baru -->
<x-ui.modal 
    id="modal-create-teacher" 
    title="Tambah Guru / Pendidik Baru" 
    subtitle="Masukkan identitas guru dan akun sistem SMK Negeri 1 Cirebon"
    size="lg"
>
    <form method="POST" action="{{ route('admin.teachers.store') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Nama Lengkap beserta Gelar
                </label>
                <input 
                    type="text" 
                    name="name" 
                    required 
                    placeholder="Contoh: Budi Santoso, S.Kom., M.T."
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Email Akun Guru
                </label>
                <input 
                    type="email" 
                    name="email" 
                    required 
                    placeholder="budi.santoso@smkn1cirebon.sch.id"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    NIP / Identitas Pendidik
                </label>
                <input 
                    type="text" 
                    name="nip" 
                    required 
                    placeholder="Contoh: 198501152010011002"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-mono"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Mata Pelajaran / Kejuruan
                </label>
                <input 
                    type="text" 
                    name="mata_pelajaran" 
                    placeholder="Contoh: Rekayasa Perangkat Lunak"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Password Awal
                </label>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Default: password123"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="pt-5">
                <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                    <input 
                        type="checkbox" 
                        name="is_wali_kelas" 
                        value="1"
                        class="w-4 h-4 rounded text-brand-primary focus:ring-brand-primary border-border-default bg-surface-bg"
                    >
                    <span class="text-xs font-medium text-text-primary">
                        Ditugaskan sebagai Wali Kelas
                    </span>
                </label>
            </div>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-end gap-3">
            <button 
                type="button" 
                onclick="window.closeModal('modal-create-teacher')"
                class="px-5 py-2.5 text-xs sm:text-sm font-semibold rounded-full border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg transition-colors focus:outline-none"
            >
                Batal
            </button>
            <button 
                type="submit" 
                class="px-6 py-2.5 text-xs sm:text-sm font-semibold rounded-full bg-brand-primary text-text-on-brand-primary hover:brightness-105 transition-all shadow-md shadow-brand-primary/20 focus:outline-none"
            >
                Simpan Guru Baru
            </button>
        </div>
    </form>
</x-ui.modal>

<!-- Modal 3: Edit Data Guru -->
<x-ui.modal 
    id="modal-edit-teacher" 
    title="Ubah Data Guru" 
    subtitle="Perbarui identitas guru dan penugasan mengajar"
    size="lg"
>
    <form id="form-edit-teacher" method="POST" action="" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Nama Lengkap beserta Gelar
                </label>
                <input 
                    type="text" 
                    id="edit-teacher-name" 
                    name="name" 
                    required 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Email Akun Guru
                </label>
                <input 
                    type="email" 
                    id="edit-teacher-email" 
                    name="email" 
                    required 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    NIP / Identitas Pendidik
                </label>
                <input 
                    type="text" 
                    id="edit-teacher-nip" 
                    name="nip" 
                    required 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-mono"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Mata Pelajaran / Kejuruan
                </label>
                <input 
                    type="text" 
                    id="edit-teacher-mapel" 
                    name="mata_pelajaran" 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Ganti Password (Opsional)
                </label>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Kosongkan jika tidak diubah"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="pt-5">
                <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                    <input 
                        type="checkbox" 
                        id="edit-teacher-wali" 
                        name="is_wali_kelas" 
                        value="1"
                        class="w-4 h-4 rounded text-brand-primary focus:ring-brand-primary border-border-default bg-surface-bg"
                    >
                    <span class="text-xs font-medium text-text-primary">
                        Ditugaskan sebagai Wali Kelas
                    </span>
                </label>
            </div>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-end gap-3">
            <button 
                type="button" 
                onclick="window.closeModal('modal-edit-teacher')"
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

<!-- Modal 4: Detail Data Guru -->
<x-ui.modal 
    id="modal-detail-teacher" 
    title="Detail Data Tenaga Pendidik" 
    subtitle="Profil terperinci guru dan riwayat akun"
    size="md"
>
    <div class="space-y-4">
        <div class="flex items-center gap-4 p-4 rounded-2xl bg-surface-bg border border-border-default">
            <div id="detail-teacher-avatar" class="w-12 h-12 rounded-full bg-brand-primary/20 text-brand-secondary dark:text-brand-primary font-bold text-base flex items-center justify-center border border-brand-primary/30">
                GT
            </div>
            <div>
                <h4 id="detail-teacher-name" class="font-bold text-text-primary text-base">
                    Nama Guru
                </h4>
                <p id="detail-teacher-email" class="text-xs text-text-secondary font-mono">
                    email@smkn1cirebon.sch.id
                </p>
            </div>
        </div>

        <div class="divide-y divide-border-subtle text-xs sm:text-sm">
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">NIP:</span>
                <span id="detail-teacher-nip" class="font-mono font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Mata Pelajaran:</span>
                <span id="detail-teacher-mapel" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Status Tugas:</span>
                <span id="detail-teacher-wali" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Username Akun:</span>
                <span id="detail-teacher-username" class="font-mono text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Terdaftar Sejak:</span>
                <span id="detail-teacher-created" class="text-text-primary">-</span>
            </div>
        </div>

        <div class="pt-4 border-t border-border-subtle flex justify-end">
            <button 
                type="button" 
                onclick="window.closeModal('modal-detail-teacher')"
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

    function showTeacherDetail(data) {
        document.getElementById('detail-teacher-name').innerText = data.name;
        document.getElementById('detail-teacher-email').innerText = data.email;
        document.getElementById('detail-teacher-nip').innerText = data.nip;
        document.getElementById('detail-teacher-mapel').innerText = data.mata_pelajaran;
        document.getElementById('detail-teacher-wali').innerText = data.is_wali_kelas;
        document.getElementById('detail-teacher-username').innerText = data.username;
        document.getElementById('detail-teacher-created').innerText = data.created_at;
        document.getElementById('detail-teacher-avatar').innerText = (data.name || 'G').substring(0, 2).toUpperCase();
        window.openModal('modal-detail-teacher');
    }

    function editTeacher(data) {
        document.getElementById('form-edit-teacher').action = data.update_url;
        document.getElementById('edit-teacher-name').value = data.name;
        document.getElementById('edit-teacher-email').value = data.email;
        document.getElementById('edit-teacher-nip').value = data.nip;
        document.getElementById('edit-teacher-mapel').value = data.mata_pelajaran;
        document.getElementById('edit-teacher-wali').checked = data.is_wali_kelas;
        window.openModal('modal-edit-teacher');
    }
</script>
@endpush
@endsection
