@extends('layouts.company')

@section('title', 'Kelola Lowongan Kerja')

@section('content')
<div 
    x-data="{
        editJob: {
            id: null,
            judul: '',
            jenis: 'penuh_waktu',
            lokasi: '',
            kuota: 1,
            gaji_min: '',
            gaji_max: '',
            batas_lamaran: '',
            deskripsi: '',
            skills: []
        },
        detailJob: {
            id: null,
            judul: '',
            jenis: '',
            lokasi: '',
            kuota: 1,
            gaji_min: null,
            gaji_max: null,
            batas_lamaran: '',
            deskripsi: '',
            status: '',
            skills: []
        },
        createSkillsList: [
            { id: '', bobot: 3, wajib: true }
        ],
        editSkillsList: [],
        addCreateSkill() {
            this.createSkillsList.push({ id: '', bobot: 3, wajib: false });
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },
        removeCreateSkill(idx) {
            if (this.createSkillsList.length > 1) {
                this.createSkillsList.splice(idx, 1);
            }
        },
        addEditSkill() {
            this.editSkillsList.push({ id: '', bobot: 3, wajib: false });
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },
        removeEditSkill(idx) {
            this.editSkillsList.splice(idx, 1);
        },
        openEdit(job) {
            this.editJob = {
                id: job.id,
                judul: job.judul,
                jenis: job.jenis,
                lokasi: job.lokasi || '{{ $company->kota ?? "Cirebon" }}',
                kuota: job.kuota,
                gaji_min: job.gaji_min || '',
                gaji_max: job.gaji_max || '',
                batas_lamaran: job.batas_lamaran ? job.batas_lamaran.substring(0, 10) : '',
                deskripsi: job.deskripsi || '',
            };
            this.editSkillsList = (job.skills || []).map(s => ({
                id: s.id,
                bobot: s.pivot ? s.pivot.bobot : 3,
                wajib: s.pivot ? Boolean(s.pivot.wajib) : false
            }));
            const form = document.getElementById('form-edit-job');
            if (form) {
                form.action = '{{ url('/company/jobs') }}/' + job.id;
            }
            window.openModal('modal-edit-job');
            this.$nextTick(() => {
                if (window.setSelectValue) {
                    window.setSelectValue('edit_job_jenis', job.jenis);
                }
            });
        },
        openDetail(job) {
            this.detailJob = job;
            window.openModal('modal-detail-job');
        }
    }"
    x-init="
        if (new URLSearchParams(window.location.search).get('create') === '1') {
            window.openModal('modal-create-job');
        }
    "
    class="space-y-6"
>
    @php
        $headers = [
            ['key' => 'judul', 'label' => 'Posisi & Lowongan', 'sortable' => true],
            ['key' => 'jenis', 'label' => 'Tipe Posisi', 'sortable' => true],
            ['key' => 'kuota', 'label' => 'Kuota & Batas', 'sortable' => true],
            ['key' => 'applications_count', 'label' => 'Pelamar', 'sortable' => false],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
        ];
        $isFilterActive = request()->filled('filter_jenis') || request()->filled('filter_status') || request()->filled('jenis') || request()->filled('status');
    @endphp

    <x-admin.data-table 
        title="Kelola Lowongan Kerja"
        subtitle="Kelola seluruh lowongan pekerjaan dan program magang yang dipublikasikan oleh {{ $company->nama }}"
        searchPlaceholder="Cari judul posisi, lokasi, atau deskripsi..."
        :search="request('search')"
        :items="$jobs"
        :headers="$headers"
        :currentSort="$sortField ?? 'created_at'"
        :currentDirection="$sortDirection ?? 'desc'"
    >
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
            @if ($company->isVerified())
                <x-ui.button variant="primary" onclick="window.openModal('modal-create-job')">
                    Tambah Lowongan
                </x-ui.button>
            @else
                <x-ui.button variant="secondary" disabled title="Akun harus diverifikasi administrator BKK terlebih dahulu">
                    Menunggu Verifikasi BKK
                </x-ui.button>
            @endif
        </x-slot:action>

        @forelse ($jobs as $job)
            <tr class="hover:bg-slate-50/80 dark:hover:bg-neutral-800/40 transition-colors group">
                <!-- Posisi -->
                <td class="px-5 py-3.5">
                    <button 
                        type="button" 
                        @click="openDetail({{ json_encode($job) }})"
                        class="text-left font-bold text-slate-900 dark:text-white block hover:text-brand-secondary dark:hover:text-brand-primary transition-colors focus:outline-none"
                    >
                        {{ $job->judul }}
                    </button>
                    <div class="flex flex-wrap gap-1 mt-1.5">
                        @forelse ($job->skills->take(3) as $skill)
                            <span class="px-2 py-0.5 rounded-md text-[10px] bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300 font-medium">
                                {{ $skill->nama }}
                            </span>
                        @empty
                            <span class="text-[10px] text-slate-400 italic">Tanpa syarat keahlian khusus</span>
                        @endforelse
                        @if ($job->skills->count() > 3)
                            <span class="px-1.5 py-0.5 rounded-md text-[10px] bg-slate-100 dark:bg-neutral-800 text-slate-400">
                                +{{ $job->skills->count() - 3 }}
                            </span>
                        @endif
                    </div>
                </td>

                <!-- Tipe & Lokasi -->
                <td class="px-5 py-3.5">
                    <div class="font-medium text-slate-800 dark:text-slate-200 text-xs">
                        {{ ucfirst(str_replace('_', ' ', $job->jenis)) }}
                    </div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $job->lokasi ?: ($company->kota ?: 'Cirebon') }}
                    </div>
                </td>

                <!-- Kuota & Batas -->
                <td class="px-5 py-3.5 text-xs text-slate-700 dark:text-slate-300">
                    <div>Kuota: <span class="font-semibold text-slate-900 dark:text-white font-mono">{{ $job->kuota }}</span> orang</div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                        Batas: {{ $job->batas_lamaran ? $job->batas_lamaran->format('d M Y') : 'Tanpa batas' }}
                    </div>
                </td>

                <!-- Total Pelamar -->
                <td class="px-5 py-3.5 text-center">
                    <a 
                        href="{{ route('company.applications.index', ['job_id' => $job->id]) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 dark:bg-neutral-800 hover:bg-brand-primary/20 text-slate-700 dark:text-slate-300 hover:text-brand-secondary dark:hover:text-brand-primary font-semibold text-xs transition-colors"
                        title="Lihat Pelamar Lowongan Ini"
                    >
                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
                        <span>{{ $job->applications_count }}</span>
                    </a>
                </td>

                <!-- Status -->
                <td class="px-5 py-3.5">
                    <form method="POST" action="{{ route('company.jobs.toggle-status', $job) }}">
                        @csrf
                        @method('PATCH')
                        <button 
                            type="submit" 
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold transition-all {{ $job->status === 'dibuka' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-slate-400 hover:bg-slate-200' }}"
                            title="Klik untuk mengubah status buka/tutup"
                        >
                            <span class="w-1.5 h-1.5 rounded-full {{ $job->status === 'dibuka' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                            <span>{{ ucfirst($job->status) }}</span>
                        </button>
                    </form>
                </td>

                <!-- Aksi -->
                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                        <button 
                            type="button" 
                            @click="openDetail({{ json_encode($job) }})" 
                            class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-neutral-800 flex items-center justify-center transition-colors"
                            title="Pratinjau Detail Lowongan"
                        >
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>

                        <button 
                            type="button" 
                            @click="openEdit({{ json_encode($job) }})" 
                            class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-neutral-800 flex items-center justify-center transition-colors"
                            title="Ubah Lowongan (Modal)"
                        >
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </button>

                        <button 
                            type="button" 
                            onclick="submitDeleteForm('{{ route('company.jobs.destroy', $job) }}', 'lowongan {{ addslashes($job->judul) }}')" 
                            class="w-8 h-8 rounded-lg text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 flex items-center justify-center transition-colors"
                            title="Hapus Lowongan"
                        >
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">
                    <x-ui.empty-state 
                        title="Belum Ada Lowongan Kerja" 
                        description="Anda belum memiliki lowongan pekerjaan yang sesuai dengan kriteria pencarian ini."
                        icon="briefcase"
                    >
                        @if ($company->isVerified())
                            <x-ui.button variant="primary" onclick="window.openModal('modal-create-job')">
                                Tambah Lowongan
                            </x-ui.button>
                        @endif
                    </x-ui.empty-state>
                </td>
            </tr>
        @endforelse
    </x-admin.data-table>

    <!-- MODAL 1: Filter Lowongan Kerja (Choices.js Styled Dropdown) -->
    <x-ui.modal 
        id="modal-filter-jobs" 
        title="Filter Lowongan Kerja" 
        subtitle="Saring daftar lowongan berdasarkan jenis pekerjaan dan status publikasi"
        size="md"
        overflow="visible"
    >
        <form method="GET" action="{{ route('company.jobs.index') }}" class="space-y-4">
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Jenis Pekerjaan / Posisi
                </label>
                <select 
                    name="filter_jenis" 
                    class="choices-select w-full"
                >
                    <option value="">Semua Jenis Pekerjaan</option>
                    <option value="penuh_waktu" {{ request('filter_jenis', request('jenis')) === 'penuh_waktu' ? 'selected' : '' }}>Penuh Waktu (Full Time)</option>
                    <option value="magang" {{ request('filter_jenis', request('jenis')) === 'magang' ? 'selected' : '' }}>Magang / PKL</option>
                    <option value="paruh_waktu" {{ request('filter_jenis', request('jenis')) === 'paruh_waktu' ? 'selected' : '' }}>Paruh Waktu</option>
                    <option value="proyek" {{ request('filter_jenis', request('jenis')) === 'proyek' ? 'selected' : '' }}>Proyek / Kontrak</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Status Publikasi
                </label>
                <select 
                    name="filter_status" 
                    class="choices-select w-full"
                >
                    <option value="">Semua Status</option>
                    <option value="dibuka" {{ request('filter_status', request('status')) === 'dibuka' ? 'selected' : '' }}>Dibuka</option>
                    <option value="ditutup" {{ request('filter_status', request('status')) === 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                    <option value="selesai" {{ request('filter_status', request('status')) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div class="pt-5 border-t border-border-subtle flex items-center justify-between gap-3">
                <a 
                    href="{{ route('company.jobs.index') }}" 
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

    <!-- MODAL 2: Tambah Lowongan Baru (Floating Modal with Choices.js Dropdown) -->
    <x-ui.modal 
        id="modal-create-job" 
        title="Publikasikan Lowongan Baru" 
        subtitle="Buka kesempatan magang atau karier bagi peserta didik dan alumni SMKN 1 Cirebon"
        size="xl"
        overflow="visible"
    >
        <form method="POST" action="{{ route('company.jobs.store') }}" class="space-y-4">
            @csrf

            <!-- Informasi Utama -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Judul Posisi Lowongan <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="judul" 
                        required 
                        placeholder="Contoh: Junior Web Developer, Teknisi Jaringan, UI Designer"
                        class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                    >
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Jenis Pekerjaan <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        name="jenis" 
                        required
                        class="choices-select w-full"
                    >
                        <option value="penuh_waktu" selected>Penuh Waktu (Full Time)</option>
                        <option value="magang">Magang / PKL Vokasi</option>
                        <option value="paruh_waktu">Paruh Waktu (Part Time)</option>
                        <option value="proyek">Proyek / Kontrak</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Lokasi Penempatan
                    </label>
                    <input 
                        type="text" 
                        name="lokasi" 
                        value="{{ $company->kota ?? 'Cirebon' }}"
                        placeholder="Contoh: Kota Cirebon / Hybrid / WFH"
                        class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                    >
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Kuota Rekrutmen <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="kuota" 
                        required 
                        value="1" 
                        min="1" 
                        max="100"
                        class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal font-mono"
                    >
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Batas Akhir Pendaftaran
                    </label>
                    <input 
                        type="date" 
                        name="batas_lamaran" 
                        class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                    >
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Estimasi Gaji Min (Rp)
                    </label>
                    <input 
                        type="number" 
                        name="gaji_min" 
                        placeholder="Contoh: 2500000" 
                        step="50000"
                        class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                    >
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Estimasi Gaji Max (Rp)
                    </label>
                    <input 
                        type="number" 
                        name="gaji_max" 
                        placeholder="Contoh: 4500000" 
                        step="50000"
                        class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                    >
                </div>

                <div class="sm:col-span-2 space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Deskripsi & Kualifikasi Lowongan <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        name="deskripsi" 
                        rows="4" 
                        required
                        placeholder="Tuliskan uraian tugas, kriteria keahlian, dan keuntungan yang didapatkan kandidat..."
                        class="w-full p-4 rounded-2xl bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
                    ></textarea>
                </div>
            </div>

            <!-- Syarat Keahlian (Job Matching Engine) -->
            <div class="pt-4 border-t border-border-subtle space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-text-primary uppercase tracking-wider">
                            Syarat Keahlian & Pembobotan Match
                        </h4>
                        <p class="text-[11px] text-text-secondary">
                            Digunakan oleh sistem untuk menghitung persentase kecocokan pelamar secara otomatis.
                        </p>
                    </div>

                    <button 
                        type="button" 
                        @click="addCreateSkill()"
                        class="px-4 py-1.5 rounded-full border border-border-default hover:bg-surface-bg text-text-primary text-xs font-semibold transition-colors"
                    >
                        + Tambah Syarat Skill
                    </button>
                </div>

                <div class="space-y-2.5 max-h-56 overflow-y-auto pr-1">
                    <template x-for="(skillRow, idx) in createSkillsList" :key="idx">
                        <div class="p-3 rounded-2xl bg-surface-bg border border-border-default flex flex-col sm:flex-row items-center gap-3">
                            <div class="flex-1 w-full">
                                <select 
                                    :name="'skills[' + idx + '][id]'" 
                                    x-model="skillRow.id"
                                    required
                                    class="w-full h-10 px-3 text-xs rounded-xl bg-surface-card border border-border-default text-text-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/30"
                                >
                                    <option value="">-- Pilih Standar Keahlian SMK --</option>
                                    @foreach ($skills as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->kategori ?? 'Umum' }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="w-full sm:w-36">
                                <select 
                                    :name="'skills[' + idx + '][bobot]'" 
                                    x-model="skillRow.bobot"
                                    class="w-full h-10 px-3 text-xs rounded-xl bg-surface-card border border-border-default text-text-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/30"
                                >
                                    <option value="1">1 - Dasar</option>
                                    <option value="2">2 - Pendukung</option>
                                    <option value="3">3 - Menengah (Standar)</option>
                                    <option value="4">4 - Sangat Penting</option>
                                    <option value="5">5 - Wajib Menguasai</option>
                                </select>
                            </div>

                            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-start">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-text-primary">
                                    <input 
                                        type="checkbox" 
                                        :name="'skills[' + idx + '][wajib]'" 
                                        value="1" 
                                        x-model="skillRow.wajib"
                                        class="w-4 h-4 rounded text-brand-primary focus:ring-brand-primary border-border-default"
                                    >
                                    <span class="text-[11px] font-medium">Syarat Mutlak</span>
                                </label>

                                <button 
                                    type="button" 
                                    @click="removeCreateSkill(idx)" 
                                    :disabled="createSkillsList.length === 1"
                                    class="p-1.5 rounded-lg text-text-secondary hover:text-rose-600 disabled:opacity-30 disabled:pointer-events-none transition-colors"
                                >
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Footer Toolbar (Admin Style Pill Buttons) -->
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

    <!-- MODAL 3: Edit Lowongan Pekerjaan (Floating Modal with Choices.js Dropdown) -->
    <x-ui.modal 
        id="modal-edit-job" 
        title="Edit Lowongan Pekerjaan" 
        subtitle="Perbarui kriteria, kuota, atau syarat keahlian posisi"
        size="xl"
        overflow="visible"
    >
        <form id="form-edit-job" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Judul Posisi Lowongan <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="judul" 
                        required 
                        x-model="editJob.judul"
                        class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                    >
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Jenis Pekerjaan <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        id="edit_job_jenis"
                        name="jenis" 
                        required
                        class="choices-select w-full"
                    >
                        <option value="penuh_waktu">Penuh Waktu (Full Time)</option>
                        <option value="magang">Magang / PKL Vokasi</option>
                        <option value="paruh_waktu">Paruh Waktu (Part Time)</option>
                        <option value="proyek">Proyek / Kontrak</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Lokasi Penempatan
                    </label>
                    <input 
                        type="text" 
                        name="lokasi" 
                        x-model="editJob.lokasi"
                        class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                    >
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Kuota Rekrutmen <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="kuota" 
                        required 
                        min="1" 
                        max="100"
                        x-model="editJob.kuota"
                        class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal font-mono"
                    >
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Batas Akhir Pendaftaran
                    </label>
                    <input 
                        type="date" 
                        name="batas_lamaran" 
                        x-model="editJob.batas_lamaran"
                        class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                    >
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Estimasi Gaji Min (Rp)
                    </label>
                    <input 
                        type="number" 
                        name="gaji_min" 
                        step="50000"
                        x-model="editJob.gaji_min"
                        class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                    >
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Estimasi Gaji Max (Rp)
                    </label>
                    <input 
                        type="number" 
                        name="gaji_max" 
                        step="50000"
                        x-model="editJob.gaji_max"
                        class="w-full px-5 py-3 rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary text-sm transition-all outline-none shadow-xs font-normal"
                    >
                </div>

                <div class="sm:col-span-2 space-y-1.5">
                    <label class="block text-xs font-semibold text-text-primary">
                        Deskripsi & Kualifikasi Lowongan <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        name="deskripsi" 
                        rows="4" 
                        required
                        x-model="editJob.deskripsi"
                        class="w-full p-4 rounded-2xl bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 text-sm transition-all outline-none shadow-xs font-normal"
                    ></textarea>
                </div>
            </div>

            <!-- Syarat Keahlian Edit -->
            <div class="pt-4 border-t border-border-subtle space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-text-primary uppercase tracking-wider">
                            Syarat Keahlian & Pembobotan Match
                        </h4>
                        <p class="text-[11px] text-text-secondary">
                            Sesuaikan bobot kompetensi siswa untuk lowongan ini.
                        </p>
                    </div>

                    <button 
                        type="button" 
                        @click="addEditSkill()"
                        class="px-4 py-1.5 rounded-full border border-border-default hover:bg-surface-bg text-text-primary text-xs font-semibold transition-colors"
                    >
                        + Tambah Syarat Skill
                    </button>
                </div>

                <div class="space-y-2.5 max-h-56 overflow-y-auto pr-1">
                    <template x-for="(skillRow, idx) in editSkillsList" :key="idx">
                        <div class="p-3 rounded-2xl bg-surface-bg border border-border-default flex flex-col sm:flex-row items-center gap-3">
                            <div class="flex-1 w-full">
                                <select 
                                    :name="'skills[' + idx + '][id]'" 
                                    x-model="skillRow.id"
                                    required
                                    class="w-full h-10 px-3 text-xs rounded-xl bg-surface-card border border-border-default text-text-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/30"
                                >
                                    <option value="">-- Pilih Standar Keahlian SMK --</option>
                                    @foreach ($skills as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->kategori ?? 'Umum' }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="w-full sm:w-36">
                                <select 
                                    :name="'skills[' + idx + '][bobot]'" 
                                    x-model="skillRow.bobot"
                                    class="w-full h-10 px-3 text-xs rounded-xl bg-surface-card border border-border-default text-text-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/30"
                                >
                                    <option value="1">1 - Dasar</option>
                                    <option value="2">2 - Pendukung</option>
                                    <option value="3">3 - Menengah (Standar)</option>
                                    <option value="4">4 - Sangat Penting</option>
                                    <option value="5">5 - Wajib Menguasai</option>
                                </select>
                            </div>

                            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-start">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-text-primary">
                                    <input 
                                        type="checkbox" 
                                        :name="'skills[' + idx + '][wajib]'" 
                                        value="1" 
                                        x-model="skillRow.wajib"
                                        class="w-4 h-4 rounded text-brand-primary focus:ring-brand-primary border-border-default"
                                    >
                                    <span class="text-[11px] font-medium">Syarat Mutlak</span>
                                </label>

                                <button 
                                    type="button" 
                                    @click="removeEditSkill(idx)" 
                                    class="p-1.5 rounded-lg text-text-secondary hover:text-rose-600 transition-colors"
                                >
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Footer Toolbar (Admin Style Pill Buttons) -->
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

    <!-- MODAL 4: Detail Lowongan Pekerjaan (Floating Modal) -->
    <x-ui.modal 
        id="modal-detail-job" 
        title="Detail Lowongan Pekerjaan" 
        subtitle="Informasi lengkap posisi dan persyaratan kualifikasi"
        size="lg"
    >
        <div class="space-y-4">
            <div class="p-5 rounded-2xl bg-surface-bg border border-border-default space-y-2">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-text-primary" x-text="detailJob.judul"></h3>
                        <p class="text-xs text-text-secondary mt-0.5">
                            <span x-text="detailJob.lokasi || 'Cirebon'"></span> &bull; 
                            <span class="capitalize" x-text="(detailJob.jenis || '').replace('_', ' ')"></span>
                        </p>
                    </div>
                    <span 
                        class="px-2.5 py-0.5 rounded-full text-[11px] font-bold capitalize"
                        :class="detailJob.status === 'dibuka' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60' : 'bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-slate-300'"
                        x-text="detailJob.status"
                    ></span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2 text-xs">
                    <div>
                        <span class="text-text-secondary block text-[11px]">Kuota:</span>
                        <span class="font-semibold text-text-primary" x-text="(detailJob.kuota || 1) + ' orang'"></span>
                    </div>
                    <div>
                        <span class="text-text-secondary block text-[11px]">Batas Lamaran:</span>
                        <span class="font-semibold text-text-primary" x-text="detailJob.batas_lamaran ? new Date(detailJob.batas_lamaran).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : 'Terbuka'"></span>
                    </div>
                    <div>
                        <span class="text-text-secondary block text-[11px]">Rentang Gaji:</span>
                        <span class="font-semibold text-text-primary" x-text="detailJob.gaji_min ? 'Rp ' + Number(detailJob.gaji_min).toLocaleString('id-ID') : 'Negosiasi'"></span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-xs font-bold text-text-primary uppercase tracking-wider mb-1.5">
                    Deskripsi Pekerjaan
                </h4>
                <div class="p-4 rounded-2xl bg-surface-bg border border-border-default text-xs text-text-primary leading-relaxed whitespace-pre-line" x-text="detailJob.deskripsi"></div>
            </div>

            <template x-if="detailJob.skills && detailJob.skills.length > 0">
                <div>
                    <h4 class="text-xs font-bold text-text-primary uppercase tracking-wider mb-1.5">
                        Standar Keahlian yang Dipersyaratkan
                    </h4>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="skill in detailJob.skills" :key="skill.id">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs bg-surface-bg border border-border-default text-text-primary">
                                <span class="font-medium" x-text="skill.nama"></span>
                                <span class="text-[10px] text-text-secondary" x-text="'(Bobot: ' + (skill.pivot ? skill.pivot.bobot : 1) + ')'"></span>
                                <template x-if="skill.pivot && skill.pivot.wajib">
                                    <span class="text-[9px] px-1 py-0.2 rounded bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 font-bold uppercase">Wajib</span>
                                </template>
                            </span>
                        </template>
                    </div>
                </div>
            </template>

            <div class="pt-5 border-t border-border-subtle flex items-center justify-end">
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
</div>
@endsection
