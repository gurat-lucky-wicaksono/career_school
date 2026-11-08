@extends('layouts.teacher')

@section('title', 'Pemantauan Siswa Bimbingan & PKL')

@section('content')
<div class="space-y-8">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-heading font-bold text-text-primary tracking-tight">
                Pemantauan Siswa Bimbingan & PKL
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Pantau progres persiapan karir, status lamaran kemitraan DUDI, serta penempatan Praktik Kerja Lapangan siswa.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <x-ui.button variant="secondary" size="md" href="{{ route('teacher.portfolios.index') }}">
                Lihat portofolio siswa
            </x-ui.button>
            <x-ui.button variant="primary" size="md" href="{{ route('teacher.skills.index') }}">
                Validasi keahlian
            </x-ui.button>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-card bg-surface-card border border-border-subtle shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-text-muted uppercase tracking-wider">Total Siswa Terdaftar</p>
                <h3 class="text-2xl font-heading font-bold text-text-primary mt-1">{{ number_format($stats['total_students']) }}</h3>
                <p class="text-xs text-text-secondary mt-0.5">Siswa vokasi terpantau</p>
            </div>
            <div class="w-12 h-12 rounded-btn bg-brand-primary/10 text-brand-secondary dark:text-brand-primary flex items-center justify-center">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-5 rounded-card bg-surface-card border border-border-subtle shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-state-success-text uppercase tracking-wider">Diterima PKL / kerja</p>
                <h3 class="text-2xl font-heading font-bold text-state-success-text mt-1">{{ number_format($stats['placed_interns']) }}</h3>
                <p class="text-xs text-text-secondary mt-0.5">Terkonfirmasi di mitra DUDI</p>
            </div>
            <div class="w-12 h-12 rounded-btn bg-state-success-bg text-state-success-text flex items-center justify-center">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-5 rounded-card bg-surface-card border border-border-subtle shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-state-info-text uppercase tracking-wider">Tahap wawancara</p>
                <h3 class="text-2xl font-heading font-bold text-state-info-text mt-1">{{ number_format($stats['interviewing']) }}</h3>
                <p class="text-xs text-text-secondary mt-0.5">Proses seleksi aktif</p>
            </div>
            <div class="w-12 h-12 rounded-btn bg-state-info-bg text-state-info-text flex items-center justify-center">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-5 rounded-card bg-surface-card border border-border-subtle shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-state-warning-text uppercase tracking-wider">Lamaran menunggu</p>
                <h3 class="text-2xl font-heading font-bold text-state-warning-text mt-1">{{ number_format($stats['pending_applications']) }}</h3>
                <p class="text-xs text-text-secondary mt-0.5">Ditinjau oleh HRD industri</p>
            </div>
            <div class="w-12 h-12 rounded-btn bg-state-warning-bg text-state-warning-text flex items-center justify-center">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="p-5 rounded-card bg-surface-card border border-border-subtle shadow-xs">
        <form method="GET" action="{{ route('teacher.mentoring.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <!-- Search -->
            <div class="lg:col-span-5">
                <label for="search" class="block text-xs font-semibold text-text-primary mb-1.5">
                    Cari siswa / NISN
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        id="search"
                        value="{{ request('q') }}"
                        placeholder="Ketik nama lengkap atau nomor NISN..."
                        class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-full border border-border-default bg-surface-bg text-text-primary placeholder:text-text-muted focus:outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 shadow-xs transition-all"
                    >
                    <i data-lucide="search" class="w-4 h-4 text-text-muted absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                </div>
            </div>

            <!-- Filter Kelas -->
            <div class="lg:col-span-3">
                <label for="class_id" class="block text-xs font-semibold text-text-primary mb-1.5">
                    Rombongan belajar
                </label>
                <select
                    name="class_id"
                    id="class_id"
                    class="choices-select w-full"
                >
                    <option value="">Semua rombel / kelas</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->nama }} (Tingkat {{ $class->tingkat }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status PKL -->
            <div class="lg:col-span-2">
                <label for="status" class="block text-xs font-semibold text-text-primary mb-1.5">
                    Status penempatan PKL
                </label>
                <select
                    name="status"
                    id="status"
                    class="choices-select w-full"
                >
                    <option value="">Semua status siswa</option>
                    <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Diterima PKL / bekerja</option>
                    <option value="wawancara" {{ request('status') === 'wawancara' ? 'selected' : '' }}>Tahap wawancara</option>
                    <option value="melamar" {{ request('status') === 'melamar' ? 'selected' : '' }}>Sedang melamar lowongan</option>
                    <option value="belum_melamar" {{ request('status') === 'belum_melamar' ? 'selected' : '' }}>Belum mengajukan lamaran</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <x-ui.button variant="primary" type="submit" class="flex-1 justify-center">
                    Filter
                </x-ui.button>
                @if (request()->hasAny(['q', 'class_id', 'status']))
                    <a href="{{ route('teacher.mentoring.index') }}" class="h-10 w-10 rounded-full border border-border-default hover:border-text-secondary/70 hover:bg-surface-bg text-text-secondary hover:text-text-primary flex items-center justify-center transition-all shadow-xs shrink-0" title="Reset filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Student Table -->
    <div class="bg-surface-card border border-border-subtle rounded-card shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-border-subtle flex items-center justify-between">
            <h2 class="text-base font-heading font-bold text-text-primary">
                Daftar siswa bimbingan ({{ $students->total() }})
            </h2>
            <span class="text-xs text-text-muted">
                Menampilkan halaman {{ $students->currentPage() }} dari {{ $students->lastPage() }}
            </span>
        </div>

        @if ($students->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-border-subtle bg-surface-bg text-[10px] font-semibold text-text-secondary uppercase tracking-wider">
                            <th class="py-3 px-6">Identitas siswa</th>
                            <th class="py-3 px-4">Kelas & jurusan</th>
                            <th class="py-3 px-4">Keahlian & portofolio</th>
                            <th class="py-3 px-4">Status lamaran / PKL terakhir</th>
                            <th class="py-3 px-6 text-right">Aksi bimbingan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle">
                        @foreach ($students as $student)
                            @php
                                $validatedCount = $student->studentSkills->where('tervalidasi', true)->count();
                                $totalSkills = $student->studentSkills->count();
                                $latestApplication = $student->applications->sortByDesc('created_at')->first();
                            @endphp
                            <tr class="hover:bg-surface-bg transition-colors">
                                <!-- Student Info -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-neutral-100 dark:bg-neutral-800 border border-border-subtle flex items-center justify-center font-bold text-text-primary text-xs flex-shrink-0">
                                            {{ strtoupper(substr($student->user->name ?? 'S', 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-heading font-semibold text-text-primary leading-tight">
                                                {{ $student->user->name ?? 'Nama Siswa' }}
                                            </p>
                                            <p class="text-xs text-text-muted mt-0.5">
                                                NISN: {{ $student->nisn ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Class & Major -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-neutral-100 dark:bg-neutral-800 text-text-primary">
                                        {{ $student->schoolClass->nama ?? 'Kelas -' }}
                                    </span>
                                    <p class="text-xs text-text-muted mt-1">
                                        {{ $student->schoolClass->major->nama ?? 'Jurusan Kejuruan' }}
                                    </p>
                                </td>

                                <!-- Skills & Portfolio Stats -->
                                <td class="py-4 px-4">
                                    <div class="flex flex-col gap-1 text-xs">
                                        <div class="flex items-center gap-1.5 text-text-secondary">
                                            <i data-lucide="award" class="w-3.5 h-3.5 text-brand-secondary dark:text-brand-primary"></i>
                                            <span><strong>{{ $validatedCount }}</strong> / {{ $totalSkills }} keahlian valid</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-text-secondary">
                                            <i data-lucide="folder-git-2" class="w-3.5 h-3.5 text-state-info-text"></i>
                                            <span><strong>{{ $student->portfolios->count() }}</strong> karya portofolio</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Application / Internship Status -->
                                <td class="py-4 px-4">
                                    @if ($latestApplication)
                                        <div>
                                            @if ($latestApplication->status === 'diterima')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-state-success-bg text-state-success-text border border-state-success-border">
                                                    <i data-lucide="check" class="w-3 h-3"></i>
                                                    Diterima magang / kerja
                                                </span>
                                            @elseif ($latestApplication->status === 'wawancara')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-state-info-bg text-state-info-text border border-state-info-border">
                                                    <i data-lucide="calendar" class="w-3 h-3"></i>
                                                    Tahap wawancara
                                                </span>
                                            @elseif ($latestApplication->status === 'ditinjau' || $latestApplication->status === 'menunggu')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-state-warning-bg text-state-warning-text border border-state-warning-border">
                                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                                    Dalam seleksi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-neutral-100 dark:bg-neutral-800 text-text-secondary border border-border-subtle">
                                                    {{ ucfirst($latestApplication->status) }}
                                                </span>
                                            @endif

                                            <p class="text-xs font-medium text-text-primary mt-1 line-clamp-1">
                                                {{ $latestApplication->job->judul ?? 'Lowongan PKL' }}
                                            </p>
                                            <p class="text-[11px] text-text-muted">
                                                {{ $latestApplication->job->company->nama ?? 'Mitra DUDI' }}
                                            </p>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-neutral-100 dark:bg-neutral-800 text-text-muted">
                                            Belum melamar
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-ui.button
                                            variant="secondary"
                                            size="sm"
                                            href="{{ route('teacher.skills.index', ['q' => $student->user->name]) }}"
                                            title="Validasi keahlian siswa ini"
                                        >
                                            Validasi
                                        </x-ui.button>

                                        <x-ui.button
                                            variant="secondary"
                                            size="sm"
                                            href="{{ route('teacher.portfolios.index', ['q' => $student->user->name]) }}"
                                            title="Periksa karya portofolio"
                                        >
                                            Portofolio
                                        </x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-border-subtle">
                {{ $students->links() }}
            </div>
        @else
            <div class="py-16 text-center">
                <div class="w-16 h-16 rounded-card bg-surface-bg border border-border-subtle text-text-muted flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="users" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-heading font-bold text-text-primary">Tidak ada data siswa ditemukan</h3>
                <p class="text-sm text-text-secondary mt-1 max-w-sm mx-auto">
                    Coba sesuaikan filter rombongan belajar atau kata kunci pencarian nama/NISN.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
