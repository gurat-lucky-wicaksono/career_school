@extends('layouts.admin')

@section('title', 'Data Peserta Didik / Siswa')

@section('content')
<div class="space-y-6">
    @php
        $headers = [
            ['key' => 'name', 'label' => 'Nama & Identitas Siswa', 'sortable' => false],
            ['key' => 'nisn', 'label' => 'NISN', 'sortable' => true],
            ['key' => 'class', 'label' => 'Kelas & Jurusan', 'sortable' => false],
            ['key' => 'angkatan', 'label' => 'Angkatan', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
        ];
        $isFilterActive = request()->filled('filter_major') || request()->filled('filter_class') || request()->filled('filter_status') || request()->filled('filter_angkatan');
    @endphp

    <x-admin.data-table 
        title="Data Peserta Didik"
        subtitle="Manajemen data siswa aktif, alumni vokasi, dan status kesiswaan SMK Negeri 1 Cirebon"
        searchPlaceholder="Cari NISN, nama siswa, atau kelas..."
        :search="request('search')"
        :items="$students"
        :headers="$headers"
        :currentSort="$sortField"
        :currentDirection="$sortDirection"
    >
        <x-slot:checkboxAll>
            <input 
                type="checkbox" 
                id="select-all-students"
                onclick="toggleSelectAll(this)"
                aria-label="Pilih Semua Data Siswa"
            >
        </x-slot:checkboxAll>

        <x-slot:filter>
            <button 
                type="button" 
                onclick="window.openModal('modal-filter-students')"
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
            <x-ui.button variant="primary" onclick="window.openModal('modal-create-student')">
                Tambah Siswa
            </x-ui.button>
        </x-slot:action>

        @forelse ($students as $student)
            <tr class="hover:bg-slate-50/80 dark:hover:bg-neutral-800/40 transition-colors group">
                <td class="w-12 px-4 py-3.5 text-center">
                    <input 
                        type="checkbox" 
                        aria-label="Pilih siswa {{ $student->user->name ?? '' }}"
                    >
                </td>

                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-200 font-semibold text-xs flex items-center justify-center flex-shrink-0 border border-slate-200/80 dark:border-white/10">
                            {{ strtoupper(substr($student->user->name ?? 'S', 0, 2)) }}
                        </div>
                        <div>
                            <span class="font-semibold text-slate-900 dark:text-white block">
                                {{ $student->user->name ?? 'Siswa Vokasi' }}
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 block font-mono">
                                {{ $student->user->email ?? '-' }}
                            </span>
                        </div>
                    </div>
                </td>

                <td class="px-5 py-3.5 font-mono text-xs text-slate-600 dark:text-slate-300">
                    {{ $student->nisn ?? '-' }}
                </td>

                <td class="px-5 py-3.5">
                    <div class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                        {{ $student->class->nama ?? '-' }}
                    </div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400">
                        {{ $student->major->nama ?? '-' }}
                    </div>
                </td>

                <td class="px-5 py-3.5 font-mono text-xs text-slate-600 dark:text-slate-300">
                    {{ $student->angkatan ?? '-' }}
                </td>

                <td class="px-5 py-3.5">
                    @php
                        $badgeVariant = match($student->status) {
                            'aktif' => 'success',
                            'lulus' => 'info',
                            'pindah' => 'warning',
                            default => 'secondary'
                        };
                    @endphp
                    <x-ui.badge :variant="$badgeVariant">
                        {{ ucfirst($student->status ?? 'aktif') }}
                    </x-ui.badge>
                </td>

                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                        <x-ui.icon-button 
                            size="sm" 
                            icon="eye" 
                            label="Lihat Detail Siswa" 
                            onclick="showStudentDetail({{ json_encode([
                                'name' => $student->user->name ?? '-',
                                'email' => $student->user->email ?? '-',
                                'username' => $student->user->username ?? '-',
                                'nisn' => $student->nisn ?? '-',
                                'major' => $student->major->nama ?? '-',
                                'class' => $student->class->nama ?? '-',
                                'angkatan' => $student->angkatan ?? '-',
                                'status' => ucfirst($student->status ?? 'aktif'),
                                'created_at' => $student->created_at ? $student->created_at->translatedFormat('d F Y H:i') : '-',
                            ]) }})"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            icon="edit-3" 
                            label="Ubah Data Siswa" 
                            onclick="editStudent({{ json_encode([
                                'id' => $student->id,
                                'update_url' => route('admin.students.update', $student->id),
                                'name' => $student->user->name ?? '',
                                'email' => $student->user->email ?? '',
                                'nisn' => $student->nisn ?? '',
                                'major_id' => $student->major_id,
                                'class_id' => $student->class_id,
                                'angkatan' => $student->angkatan,
                                'status' => $student->status,
                            ]) }})"
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            variant="danger" 
                            icon="trash-2" 
                            label="Hapus Data Siswa" 
                            onclick="window.submitDeleteForm('{{ route('admin.students.destroy', $student->id) }}', 'Siswa {{ addslashes($student->user->name ?? 'ini') }}')"
                        />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-12">
                    <x-ui.empty-state 
                        title="Belum Ada Data Siswa"
                        description="Data kesiswaan belum tercatat atau hasil pencarian tidak ditemukan."
                        actionLabel="Tambah Siswa Baru"
                        actionUrl="javascript:window.openModal('modal-create-student')"
                    />
                </td>
            </tr>
        @endforelse
    </x-admin.data-table>
</div>

<!-- Modal 1: Filter Siswa -->
<x-ui.modal 
    id="modal-filter-students" 
    title="Filter Data Siswa" 
    subtitle="Saring peserta didik berdasarkan jurusan, kelas, angkatan, atau status"
    size="md"
    overflow="visible"
>
    <form method="GET" action="{{ route('admin.students.index') }}" class="space-y-4">
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

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Rombel / Kelas
            </label>
            <select 
                name="filter_class" 
                class="choices-select w-full"
            >
                <option value="">Semua Kelas</option>
                @foreach ($classes as $c)
                    <option value="{{ $c->id }}" {{ request('filter_class') == $c->id ? 'selected' : '' }}>
                        {{ $c->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Tahun Angkatan
                </label>
                <select 
                    name="filter_angkatan" 
                    class="choices-select w-full"
                >
                    <option value="">Semua</option>
                    @foreach ($angkatans as $angkatan)
                        <option value="{{ $angkatan }}" {{ request('filter_angkatan') == $angkatan ? 'selected' : '' }}>
                            {{ $angkatan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Status Kesiswaan
                </label>
                <select 
                    name="filter_status" 
                    class="choices-select w-full"
                >
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('filter_status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="lulus" {{ request('filter_status') === 'lulus' ? 'selected' : '' }}>Lulus / Alumni</option>
                    <option value="pindah" {{ request('filter_status') === 'pindah' ? 'selected' : '' }}>Pindah / Keluar</option>
                </select>
            </div>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-between gap-3">
            <a 
                href="{{ route('admin.students.index') }}" 
                class="px-4 py-2 text-xs font-semibold text-text-secondary hover:text-text-primary transition-colors"
            >
                Reset Filter
            </a>
            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    onclick="window.closeModal('modal-filter-students')"
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

<!-- Modal 2: Tambah Siswa Baru -->
<x-ui.modal 
    id="modal-create-student" 
    title="Tambah Peserta Didik Baru" 
    subtitle="Registrasi data pokok siswa dan pembuatan akun portal vokasi"
    size="lg"
>
    <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Nama Lengkap Siswa
                </label>
                <input 
                    type="text" 
                    name="name" 
                    required 
                    placeholder="Contoh: Ahmad Fauzi"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Email Akun Siswa
                </label>
                <input 
                    type="email" 
                    name="email" 
                    required 
                    placeholder="ahmad.fauzi@smkn1cirebon.sch.id"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    NISN (Nomor Induk Siswa Nasional)
                </label>
                <input 
                    type="text" 
                    name="nisn" 
                    required 
                    placeholder="Contoh: 0061234567"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-mono"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Tahun Angkatan
                </label>
                <input 
                    type="number" 
                    name="angkatan" 
                    required 
                    value="{{ date('Y') }}"
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-mono"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Jurusan
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

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Kelas
                </label>
                <select 
                    name="class_id" 
                    required
                    class="choices-select w-full"
                >
                    <option value="">Pilih Kelas</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Status
                </label>
                <select 
                    name="status" 
                    required
                    class="choices-select w-full"
                >
                    <option value="aktif">Aktif</option>
                    <option value="lulus">Lulus / Alumni</option>
                    <option value="pindah">Pindah</option>
                </select>
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Password Akun (Opsional)
            </label>
            <input 
                type="password" 
                name="password" 
                placeholder="Default: password123"
                class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
            >
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-end gap-3">
            <button 
                type="button" 
                onclick="window.closeModal('modal-create-student')"
                class="px-5 py-2.5 text-xs sm:text-sm font-semibold rounded-full border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg transition-colors focus:outline-none"
            >
                Batal
            </button>
            <button 
                type="submit" 
                class="px-6 py-2.5 text-xs sm:text-sm font-semibold rounded-full bg-brand-primary text-text-on-brand-primary hover:brightness-105 transition-all shadow-md shadow-brand-primary/20 focus:outline-none"
            >
                Simpan Siswa Baru
            </button>
        </div>
    </form>
</x-ui.modal>

<!-- Modal 3: Edit Data Siswa -->
<x-ui.modal 
    id="modal-edit-student" 
    title="Ubah Data Siswa" 
    subtitle="Perbarui data akademik dan status peserta didik"
    size="lg"
>
    <form id="form-edit-student" method="POST" action="" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Nama Lengkap Siswa
                </label>
                <input 
                    type="text" 
                    id="edit-student-name" 
                    name="name" 
                    required 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Email Akun Siswa
                </label>
                <input 
                    type="email" 
                    id="edit-student-email" 
                    name="email" 
                    required 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    NISN
                </label>
                <input 
                    type="text" 
                    id="edit-student-nisn" 
                    name="nisn" 
                    required 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-mono"
                >
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Tahun Angkatan
                </label>
                <input 
                    type="number" 
                    id="edit-student-angkatan" 
                    name="angkatan" 
                    required 
                    class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-mono"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Jurusan
                </label>
                <select 
                    id="edit-student-major" 
                    name="major_id" 
                    required
                    class="choices-select w-full"
                >
                    @foreach ($majors as $m)
                        <option value="{{ $m->id }}">{{ $m->kode }} - {{ $m->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Kelas
                </label>
                <select 
                    id="edit-student-class" 
                    name="class_id" 
                    required
                    class="choices-select w-full"
                >
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Status
                </label>
                <select 
                    id="edit-student-status" 
                    name="status" 
                    required
                    class="choices-select w-full"
                >
                    <option value="aktif">Aktif</option>
                    <option value="lulus">Lulus / Alumni</option>
                    <option value="pindah">Pindah</option>
                </select>
            </div>
        </div>

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

        <div class="pt-5 border-t border-border-subtle flex items-center justify-end gap-3">
            <button 
                type="button" 
                onclick="window.closeModal('modal-edit-student')"
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

<!-- Modal 4: Detail Data Siswa -->
<x-ui.modal 
    id="modal-detail-student" 
    title="Detail Data Siswa" 
    subtitle="Rincian informasi kesiswaan dan identitas portal"
    size="md"
>
    <div class="space-y-4">
        <div class="flex items-center gap-4 p-4 rounded-2xl bg-surface-bg border border-border-default">
            <div id="detail-student-avatar" class="w-12 h-12 rounded-full bg-brand-primary/20 text-brand-secondary dark:text-brand-primary font-bold text-base flex items-center justify-center border border-brand-primary/30">
                SW
            </div>
            <div>
                <h4 id="detail-student-name" class="font-bold text-text-primary text-base">
                    Nama Siswa
                </h4>
                <p id="detail-student-email" class="text-xs text-text-secondary font-mono">
                    siswa@smkn1cirebon.sch.id
                </p>
            </div>
        </div>

        <div class="divide-y divide-border-subtle text-xs sm:text-sm">
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">NISN:</span>
                <span id="detail-student-nisn" class="font-mono font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Kelas:</span>
                <span id="detail-student-class" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Konsentrasi Keahlian:</span>
                <span id="detail-student-major" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Tahun Angkatan:</span>
                <span id="detail-student-angkatan" class="font-mono font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Status Kesiswaan:</span>
                <span id="detail-student-status" class="font-semibold text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Username Akun:</span>
                <span id="detail-student-username" class="font-mono text-text-primary">-</span>
            </div>
            <div class="py-2.5 flex items-center justify-between">
                <span class="text-text-secondary">Terdaftar Sejak:</span>
                <span id="detail-student-created" class="text-text-primary">-</span>
            </div>
        </div>

        <div class="pt-4 border-t border-border-subtle flex justify-end">
            <button 
                type="button" 
                onclick="window.closeModal('modal-detail-student')"
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

    function showStudentDetail(data) {
        document.getElementById('detail-student-name').innerText = data.name;
        document.getElementById('detail-student-email').innerText = data.email;
        document.getElementById('detail-student-nisn').innerText = data.nisn;
        document.getElementById('detail-student-class').innerText = data.class;
        document.getElementById('detail-student-major').innerText = data.major;
        document.getElementById('detail-student-angkatan').innerText = data.angkatan;
        document.getElementById('detail-student-status').innerText = data.status;
        document.getElementById('detail-student-username').innerText = data.username;
        document.getElementById('detail-student-created').innerText = data.created_at;
        document.getElementById('detail-student-avatar').innerText = (data.name || 'S').substring(0, 2).toUpperCase();
        window.openModal('modal-detail-student');
    }

    function editStudent(data) {
        document.getElementById('form-edit-student').action = data.update_url;
        document.getElementById('edit-student-name').value = data.name;
        document.getElementById('edit-student-email').value = data.email;
        document.getElementById('edit-student-nisn').value = data.nisn;
        document.getElementById('edit-student-angkatan').value = data.angkatan;
        
        window.setSelectValue('edit-student-major', data.major_id);
        window.setSelectValue('edit-student-class', data.class_id);
        window.setSelectValue('edit-student-status', data.status);

        window.openModal('modal-edit-student');
    }
</script>
@endpush
@endsection
