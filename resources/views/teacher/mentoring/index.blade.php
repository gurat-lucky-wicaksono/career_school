@extends('layouts.teacher')

@section('title', 'Pemantauan Siswa Bimbingan & PKL')

@section('content')
<div class="space-y-8">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Pemantauan Siswa Bimbingan & PKL
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Pantau progres persiapan karir, status lamaran kemitraan DUDI, serta penempatan Praktik Kerja Lapangan siswa.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button variant="secondary" size="md" href="{{ route('teacher.portfolios.index') }}">
                <i data-lucide="folder-git-2" class="w-4 h-4 mr-2"></i>
                Lihat Portofolio Siswa
            </x-ui.button>
            <x-ui.button variant="primary" size="md" href="{{ route('teacher.skills.index') }}">
                <i data-lucide="award" class="w-4 h-4 mr-2"></i>
                Validasi Keahlian
            </x-ui.button>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-neutral-900 border border-slate-200 dark:border-neutral-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Siswa Terdaftar</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ number_format($stats['total_students']) }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Siswa vokasi terpantau</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-neutral-900 border border-slate-200 dark:border-neutral-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Diterima PKL / Kerja</p>
                <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['placed_interns']) }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Terkonfirmasi di mitra DUDI</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-neutral-900 border border-slate-200 dark:border-neutral-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wider">Tahap Wawancara</p>
                <h3 class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ number_format($stats['interviewing']) }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Proses seleksi aktif</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-neutral-900 border border-slate-200 dark:border-neutral-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wider">Lamaran Menunggu</p>
                <h3 class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ number_format($stats['pending_applications']) }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ditinjau oleh HRD industri</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="p-5 rounded-2xl bg-white dark:bg-neutral-900 border border-slate-200 dark:border-neutral-800 shadow-sm">
        <form method="GET" action="{{ route('teacher.mentoring.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                    Cari Siswa / NISN
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        id="search"
                        value="{{ request('q') }}"
                        placeholder="Ketik nama lengkap atau nomor NISN..."
                        class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-slate-200 dark:border-neutral-700 bg-slate-50/50 dark:bg-neutral-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                </div>
            </div>

            <!-- Filter Kelas -->
            <div>
                <label for="class_id" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                    Rombongan Belajar
                </label>
                <select
                    name="class_id"
                    id="class_id"
                    class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 dark:border-neutral-700 bg-slate-50/50 dark:bg-neutral-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    onchange="this.form.submit()"
                >
                    <option value="">Semua Rombel / Kelas</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->nama }} (Tingkat {{ $class->tingkat }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status PKL -->
            <div>
                <label for="status" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                    Status Penempatan PKL
                </label>
                <select
                    name="status"
                    id="status"
                    class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 dark:border-neutral-700 bg-slate-50/50 dark:bg-neutral-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    onchange="this.form.submit()"
                >
                    <option value="">Semua Status Siswa</option>
                    <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Diterima PKL / Bekerja</option>
                    <option value="wawancara" {{ request('status') === 'wawancara' ? 'selected' : '' }}>Tahap Wawancara</option>
                    <option value="melamar" {{ request('status') === 'melamar' ? 'selected' : '' }}>Sedang Melamar Lowongan</option>
                    <option value="belum_melamar" {{ request('status') === 'belum_melamar' ? 'selected' : '' }}>Belum Mengajukan Lamaran</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Student Table -->
    <div class="bg-white dark:bg-neutral-900 border border-slate-200 dark:border-neutral-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-neutral-800 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                Daftar Siswa Bimbingan ({{ $students->total() }})
            </h2>
            <span class="text-xs text-slate-500 dark:text-slate-400">
                Menampilkan halaman {{ $students->currentPage() }} dari {{ $students->lastPage() }}
            </span>
        </div>

        @if ($students->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-neutral-800 bg-slate-50/75 dark:bg-neutral-800/50 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-6">Identitas Siswa</th>
                            <th class="py-3 px-4">Kelas & Jurusan</th>
                            <th class="py-3 px-4">Keahlian & Portofolio</th>
                            <th class="py-3 px-4">Status Lamaran / PKL Terakhir</th>
                            <th class="py-3 px-6 text-right">Aksi Bimbingan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-neutral-800/80 text-sm">
                        @foreach ($students as $student)
                            @php
                                $validatedCount = $student->studentSkills->where('tervalidasi', true)->count();
                                $totalSkills = $student->studentSkills->count();
                                $latestApplication = $student->applications->sortByDesc('created_at')->first();
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-neutral-800/40 transition-colors">
                                <!-- Student Info -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 flex items-center justify-center font-bold text-slate-700 dark:text-neutral-300 text-sm flex-shrink-0">
                                            {{ strtoupper(substr($student->user->name ?? 'S', 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-900 dark:text-white leading-tight">
                                                {{ $student->user->name ?? 'Nama Siswa' }}
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                NISN: {{ $student->nisn ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Class & Major -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 dark:bg-neutral-800 text-slate-800 dark:text-slate-200">
                                        {{ $student->schoolClass->nama ?? 'Kelas -' }}
                                    </span>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        {{ $student->schoolClass->major->nama ?? 'Jurusan Kejuruan' }}
                                    </p>
                                </td>

                                <!-- Skills & Portfolio Stats -->
                                <td class="py-4 px-4">
                                    <div class="flex flex-col gap-1 text-xs">
                                        <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300">
                                            <i data-lucide="award" class="w-3.5 h-3.5 text-emerald-500"></i>
                                            <span><strong>{{ $validatedCount }}</strong> / {{ $totalSkills }} Keahlian Valid</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300">
                                            <i data-lucide="folder-git-2" class="w-3.5 h-3.5 text-blue-500"></i>
                                            <span><strong>{{ $student->portfolios->count() }}</strong> Karya Portofolio</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Application / Internship Status -->
                                <td class="py-4 px-4">
                                    @if ($latestApplication)
                                        <div>
                                            @if ($latestApplication->status === 'diterima')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                    <i data-lucide="check" class="w-3 h-3"></i>
                                                    Diterima Magang / Kerja
                                                </span>
                                            @elseif ($latestApplication->status === 'wawancara')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                                    <i data-lucide="calendar" class="w-3 h-3"></i>
                                                    Tahap Wawancara
                                                </span>
                                            @elseif ($latestApplication->status === 'ditinjau' || $latestApplication->status === 'menunggu')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                                    Dalam Seleksi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-300">
                                                    {{ ucfirst($latestApplication->status) }}
                                                </span>
                                            @endif

                                            <p class="text-xs font-medium text-slate-800 dark:text-neutral-200 mt-1 line-clamp-1">
                                                {{ $latestApplication->job->judul ?? 'Lowongan PKL' }}
                                            </p>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                                {{ $latestApplication->job->company->nama ?? 'Mitra DUDI' }}
                                            </p>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 dark:bg-neutral-800 text-slate-500 dark:text-slate-400">
                                            Belum Melamar
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('teacher.skills.index', ['q' => $student->user->name]) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-neutral-700 text-xs font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-neutral-800 hover:bg-slate-50 dark:hover:bg-neutral-700/60 transition-colors"
                                            title="Validasi Keahlian Siswa Ini"
                                        >
                                            <i data-lucide="award" class="w-3.5 h-3.5 text-emerald-600"></i>
                                            Validasi
                                        </a>

                                        <a
                                            href="{{ route('teacher.portfolios.index', ['q' => $student->user->name]) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-neutral-700 text-xs font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-neutral-800 hover:bg-slate-50 dark:hover:bg-neutral-700/60 transition-colors"
                                            title="Periksa Karya Portofolio"
                                        >
                                            <i data-lucide="folder" class="w-3.5 h-3.5 text-blue-600"></i>
                                            Portofolio
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-100 dark:border-neutral-800">
                {{ $students->links() }}
            </div>
        @else
            <div class="py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-neutral-800 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="users" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Tidak ada data siswa ditemukan</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                    Coba sesuaikan filter rombongan belajar atau kata kunci pencarian nama/NISN.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
