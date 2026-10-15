@extends('layouts.company')

@section('title', 'Daftar Pelamar Masuk')

@section('content')
<div class="space-y-6">
    @php
        $headers = [
            ['key' => 'student', 'label' => 'Kandidat Siswa', 'sortable' => false],
            ['key' => 'job', 'label' => 'Posisi Dilamar', 'sortable' => false],
            ['key' => 'skor', 'label' => 'Skor Kecocokan', 'sortable' => true],
            ['key' => 'created_at', 'label' => 'Tanggal Masuk', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status Seleksi', 'sortable' => true],
        ];
        $isFilterActive = request()->filled('job_id') || request()->filled('status') || (request()->filled('sort') && request('sort') !== 'skor');
    @endphp

    <x-admin.data-table 
        title="Daftar Pelamar Masuk"
        subtitle="Tinjau berkas kandidat siswa dan skor kecocokan kompetensi dengan lowongan industri"
        searchPlaceholder="Cari nama siswa atau NISN..."
        :search="request('search')"
        :items="$applications"
        :headers="$headers"
        :currentSort="request('sort', 'skor')"
        :currentDirection="'desc'"
    >
        <x-slot:filter>
            <button 
                type="button" 
                onclick="window.openModal('modal-filter-applications')"
                class="relative h-11 px-4 text-xs sm:text-sm font-semibold rounded-full border border-border-default bg-surface-card text-text-primary hover:border-text-secondary/70 hover:bg-surface-bg flex items-center gap-2 transition-all shadow-xs"
            >
                <i data-lucide="filter" class="w-4 h-4 text-text-secondary"></i>
                <span>Filter</span>
                @if ($isFilterActive)
                    <span class="w-2 h-2 rounded-full bg-brand-primary"></span>
                @endif
            </button>
        </x-slot:filter>

        @forelse ($applications as $app)
            <tr class="hover:bg-slate-50/80 dark:hover:bg-neutral-800/40 transition-colors group">
                <!-- Kandidat Siswa -->
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-brand-primary/20 border border-brand-primary/40 text-brand-secondary dark:text-brand-primary font-bold text-xs flex items-center justify-center flex-shrink-0">
                            {{ strtoupper(substr($app->student->user->name ?? 'S', 0, 2)) }}
                        </div>

                        <div class="min-w-0">
                            <div class="font-bold text-slate-900 dark:text-white text-sm truncate">
                                {{ $app->student->user->name ?? 'Siswa' }}
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate font-mono">
                                {{ $app->student->major->nama ?? 'Jurusan Vokasi' }} &bull; NISN: {{ $app->student->nisn }}
                            </div>
                        </div>
                    </div>
                </td>

                <!-- Posisi Dilamar -->
                <td class="px-5 py-3.5">
                    <div class="font-semibold text-slate-800 dark:text-slate-200 text-xs">
                        {{ $app->job->judul }}
                    </div>
                    <div class="text-[11px] text-slate-400 capitalize">
                        {{ str_replace('_', ' ', $app->job->jenis) }}
                    </div>
                </td>

                <!-- Skor Kecocokan -->
                <td class="px-5 py-3.5 text-center">
                    @php
                        $score = (float)$app->skor_kecocokan;
                        $scoreColor = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200/60';
                        if ($score < 50) {
                            $scoreColor = 'bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-slate-400 border-slate-200/60';
                        } elseif ($score < 75) {
                            $scoreColor = 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200/60';
                        }
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold border {{ $scoreColor }}">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                        <span>{{ number_format($score, 0) }}%</span>
                    </span>
                </td>

                <!-- Tanggal Masuk -->
                <td class="px-5 py-3.5 text-[11px] text-slate-500 dark:text-slate-400">
                    <div>{{ $app->created_at ? $app->created_at->format('d M Y') : '-' }}</div>
                    <div class="text-[10px] text-slate-400">{{ $app->created_at ? $app->created_at->diffForHumans() : '' }}</div>
                </td>

                <!-- Status Seleksi -->
                <td class="px-5 py-3.5">
                    @php
                        $statusClasses = [
                            'tertunda' => 'bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-slate-300',
                            'ditinjau' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/60',
                            'wawancara' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200/60',
                            'diterima' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/60',
                            'ditolak' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/60',
                        ];
                    @endphp
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold capitalize {{ $statusClasses[$app->status] ?? 'bg-slate-100' }}">
                        {{ $app->status }}
                    </span>
                </td>

                <!-- Aksi -->
                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                    <x-ui.button 
                        variant="secondary" 
                        size="sm" 
                        href="{{ route('company.applications.show', $app) }}"
                    >
                        Tinjau Berkas
                    </x-ui.button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">
                    <x-ui.empty-state 
                        title="Belum Ada Pelamar" 
                        description="Belum ada berkas pelamar yang memenuhi kriteria pencarian ini."
                        icon="users"
                    />
                </td>
            </tr>
        @endforelse
    </x-admin.data-table>

    <!-- MODAL FILTER PELAMAR (Choices.js Styled Dropdown) -->
    <x-ui.modal 
        id="modal-filter-applications" 
        title="Filter Berkas Pelamar" 
        subtitle="Saring daftar pelamar berdasarkan posisi lowongan dan status seleksi"
        size="md"
        overflow="visible"
    >
        <form method="GET" action="{{ route('company.applications.index') }}" class="space-y-4">
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Pilihan Lowongan Kerja
                </label>
                <select 
                    name="job_id" 
                    class="choices-select w-full"
                >
                    <option value="">Semua Lowongan Posisi</option>
                    @foreach ($companyJobs as $cj)
                        <option value="{{ $cj->id }}" {{ request('job_id') == $cj->id ? 'selected' : '' }}>
                            {{ $cj->judul }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Status Tahapan Seleksi
                </label>
                <select 
                    name="status" 
                    class="choices-select w-full"
                >
                    <option value="">Semua Status Seleksi</option>
                    <option value="tertunda" {{ request('status') === 'tertunda' ? 'selected' : '' }}>Tertunda (Antrean Baru)</option>
                    <option value="ditinjau" {{ request('status') === 'ditinjau' ? 'selected' : '' }}>Sedang Ditinjau</option>
                    <option value="wawancara" {{ request('status') === 'wawancara' ? 'selected' : '' }}>Tahap Wawancara</option>
                    <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Diterima Bekerja / Magang</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak / Tidak Lolos</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Urutan Prioritas
                </label>
                <select 
                    name="sort" 
                    class="choices-select w-full"
                >
                    <option value="skor" {{ request('sort') !== 'tanggal' ? 'selected' : '' }}>Skor Job Match Tertinggi</option>
                    <option value="tanggal" {{ request('sort') === 'tanggal' ? 'selected' : '' }}>Waktu Lamaran Terbaru</option>
                </select>
            </div>

            <div class="pt-5 border-t border-border-subtle flex items-center justify-between gap-3">
                <a 
                    href="{{ route('company.applications.index') }}" 
                    class="px-4 py-2 text-xs font-semibold text-text-secondary hover:text-text-primary transition-colors"
                >
                    Reset Filter
                </a>
                <div class="flex items-center gap-3">
                    <button 
                        type="button" 
                        onclick="window.closeModal('modal-filter-applications')"
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
</div>
@endsection
