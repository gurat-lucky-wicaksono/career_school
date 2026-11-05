@extends('layouts.company')

@section('title', 'Daftar Pelamar Masuk')

@section('content')
<div class="space-y-6">
    @php
        $headers = [
            ['key' => 'student', 'label' => 'Kandidat siswa', 'sortable' => false],
            ['key' => 'job', 'label' => 'Posisi dilamar', 'sortable' => false],
            ['key' => 'skor', 'label' => 'Skor kecocokan', 'sortable' => true],
            ['key' => 'created_at', 'label' => 'Tanggal masuk', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status seleksi', 'sortable' => true],
        ];
        $isFilterActive = request()->filled('job_id') || request()->filled('status') || (request()->filled('sort') && request('sort') !== 'skor');
    @endphp

    <x-admin.data-table 
        title="Daftar pelamar masuk"
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
                class="relative h-11 px-4 text-xs sm:text-sm font-semibold rounded-btn border border-border-subtle bg-surface-card text-text-primary hover:border-text-secondary/70 hover:bg-surface-bg flex items-center gap-2 transition-all shadow-xs active:scale-[0.96]"
            >
                <i data-lucide="filter" class="w-4 h-4 text-text-secondary"></i>
                <span>Filter</span>
                @if ($isFilterActive)
                    <span class="w-2 h-2 rounded-full bg-brand-primary"></span>
                @endif
            </button>
        </x-slot:filter>

        @forelse ($applications as $app)
            <tr class="hover:bg-surface-bg transition-colors group">
                <!-- Kandidat Siswa -->
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-neutral-100 dark:bg-neutral-800 border border-border-subtle text-text-primary font-bold text-xs flex items-center justify-center flex-shrink-0">
                            {{ strtoupper(substr($app->student->user->name ?? 'S', 0, 2)) }}
                        </div>

                        <div class="min-w-0">
                            <div class="font-heading font-bold text-text-primary text-sm truncate">
                                {{ $app->student->user->name ?? 'Siswa' }}
                            </div>
                            <div class="text-[11px] text-text-muted truncate font-mono">
                                {{ $app->student->major->nama ?? 'Jurusan Vokasi' }} &bull; NISN: {{ $app->student->nisn }}
                            </div>
                        </div>
                    </div>
                </td>

                <!-- Posisi Dilamar -->
                <td class="px-5 py-3.5">
                    <div class="font-semibold text-text-primary text-xs">
                        {{ $app->job->judul }}
                    </div>
                    <div class="text-[11px] text-text-muted capitalize">
                        {{ str_replace('_', ' ', $app->job->jenis) }}
                    </div>
                </td>

                <!-- Skor Kecocokan -->
                <td class="px-5 py-3.5 text-center">
                    @php
                        $score = (float)$app->skor_kecocokan;
                        $scoreColor = 'bg-state-success-bg text-state-success-text border-state-success-border';
                        if ($score < 50) {
                            $scoreColor = 'bg-neutral-100 text-text-secondary dark:bg-neutral-800 border-border-subtle';
                        } elseif ($score < 75) {
                            $scoreColor = 'bg-state-warning-bg text-state-warning-text border-state-warning-border';
                        }
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold border {{ $scoreColor }}">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-brand-secondary dark:text-brand-primary"></i>
                        <span>{{ number_format($score, 0) }}%</span>
                    </span>
                </td>

                <!-- Tanggal Masuk -->
                <td class="px-5 py-3.5 text-[11px] text-text-muted">
                    <div>{{ $app->created_at ? $app->created_at->format('d M Y') : '-' }}</div>
                    <div class="text-[10px] text-text-muted">{{ $app->created_at ? $app->created_at->diffForHumans() : '' }}</div>
                </td>

                <!-- Status Seleksi -->
                <td class="px-5 py-3.5">
                    @php
                        $statusClasses = [
                            'tertunda' => 'bg-neutral-100 text-text-secondary dark:bg-neutral-800 border-border-subtle',
                            'ditinjau' => 'bg-state-warning-bg text-state-warning-text border-state-warning-border',
                            'wawancara' => 'bg-state-info-bg text-state-info-text border-state-info-border',
                            'diterima' => 'bg-state-success-bg text-state-success-text border-state-success-border',
                            'ditolak' => 'bg-state-danger-bg text-state-danger-text border-state-danger-border',
                        ];
                    @endphp
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold border capitalize {{ $statusClasses[$app->status] ?? 'bg-neutral-100 text-text-secondary border-border-subtle' }}">
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
                        Tinjau berkas
                    </x-ui.button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">
                    <x-ui.empty-state 
                        title="Belum ada pelamar" 
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
        title="Filter berkas pelamar" 
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
                    Pilihan lowongan kerja
                </label>
                <select 
                    name="job_id" 
                    class="choices-select w-full"
                >
                    <option value="">Semua lowongan posisi</option>
                    @foreach ($companyJobs as $cj)
                        <option value="{{ $cj->id }}" {{ request('job_id') == $cj->id ? 'selected' : '' }}>
                            {{ $cj->judul }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Status tahapan seleksi
                </label>
                <select 
                    name="status" 
                    class="choices-select w-full"
                >
                    <option value="">Semua status seleksi</option>
                    <option value="tertunda" {{ request('status') === 'tertunda' ? 'selected' : '' }}>Tertunda (Antrean baru)</option>
                    <option value="ditinjau" {{ request('status') === 'ditinjau' ? 'selected' : '' }}>Sedang ditinjau</option>
                    <option value="wawancara" {{ request('status') === 'wawancara' ? 'selected' : '' }}>Tahap wawancara</option>
                    <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Diterima bekerja / magang</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak / tidak lolos</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-text-primary">
                    Urutan prioritas
                </label>
                <select 
                    name="sort" 
                    class="choices-select w-full"
                >
                    <option value="skor" {{ request('sort') !== 'tanggal' ? 'selected' : '' }}>Skor job match tertinggi</option>
                    <option value="tanggal" {{ request('sort') === 'tanggal' ? 'selected' : '' }}>Waktu lamaran terbaru</option>
                </select>
            </div>

            <div class="pt-5 border-t border-border-subtle flex items-center justify-between gap-3">
                <a 
                    href="{{ route('company.applications.index') }}" 
                    class="px-4 py-2 text-xs font-semibold text-text-secondary hover:text-text-primary transition-colors"
                >
                    Reset filter
                </a>
                <div class="flex items-center gap-3">
                    <button 
                        type="button" 
                        onclick="window.closeModal('modal-filter-applications')"
                        class="px-5 py-2.5 text-xs sm:text-sm font-semibold rounded-btn border border-border-subtle text-text-secondary hover:text-text-primary hover:bg-surface-bg active:scale-[0.96] transition-transform focus:outline-none"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 text-xs sm:text-sm font-semibold rounded-btn bg-brand-primary text-text-on-brand-primary hover:brightness-105 active:scale-[0.96] transition-transform shadow-xs focus:outline-none"
                    >
                        Terapkan filter
                    </button>
                </div>
            </div>
        </form>
    </x-ui.modal>
</div>
@endsection
