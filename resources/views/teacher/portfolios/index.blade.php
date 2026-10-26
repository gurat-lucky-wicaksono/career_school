@extends('layouts.teacher')

@section('title', 'Portofolio Siswa Bimbingan')

@section('content')
<div class="space-y-8">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Portofolio Siswa Bimbingan
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Eksplorasi dan tinjau proyek kejuruan, repositori kode, dan hasil karya siswa sebagai referensi rekomendasi industri.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button variant="secondary" size="md" href="{{ route('teacher.mentoring.index') }}">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Kembali ke Bimbingan
            </x-ui.button>
            <x-ui.button variant="primary" size="md" href="{{ route('teacher.skills.index') }}">
                <i data-lucide="award" class="w-4 h-4 mr-2"></i>
                Validasi Keahlian
            </x-ui.button>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="p-5 rounded-2xl bg-white dark:bg-neutral-900 border border-slate-200 dark:border-neutral-800 shadow-sm">
        <form method="GET" action="{{ route('teacher.portfolios.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                    Cari Judul Karya / Siswa
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        id="search"
                        value="{{ request('q') }}"
                        placeholder="Ketik judul proyek, nama siswa, atau teknologi..."
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
                    <option value="">Semua Rombel</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->nama }} (Tingkat {{ $class->tingkat }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kategori -->
            <div>
                <label for="kategori" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                    Kategori Proyek
                </label>
                <select
                    name="kategori"
                    id="kategori"
                    class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 dark:border-neutral-700 bg-slate-50/50 dark:bg-neutral-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    onchange="this.form.submit()"
                >
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Portfolio Grid -->
    @if ($portfolios->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($portfolios as $portfolio)
                <div class="bg-white dark:bg-neutral-900 border border-slate-200 dark:border-neutral-800 rounded-2xl shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between overflow-hidden">
                    <div class="p-6">
                        <!-- Top Header: Student badge & Category -->
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-neutral-300 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                    {{ strtoupper(substr($portfolio->student->user->name ?? 'S', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-900 dark:text-white line-clamp-1">
                                        {{ $portfolio->student->user->name ?? 'Siswa' }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                        {{ $portfolio->student->schoolClass->nama ?? 'Kelas' }}
                                    </p>
                                </div>
                            </div>

                            @if ($portfolio->kategori)
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    {{ ucfirst($portfolio->kategori) }}
                                </span>
                            @endif
                        </div>

                        <!-- Title & Description -->
                        <h3 class="text-base font-bold text-slate-900 dark:text-white line-clamp-1 mb-2">
                            {{ $portfolio->judul }}
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">
                            {{ $portfolio->deskripsi ?: 'Tidak ada deskripsi detail untuk portofolio ini.' }}
                        </p>
                    </div>

                    <!-- Footer Details & Links -->
                    <div class="px-6 py-4 bg-slate-50/60 dark:bg-neutral-800/40 border-t border-slate-100 dark:border-neutral-800 flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400 text-[11px]">
                            {{ $portfolio->created_at ? $portfolio->created_at->format('d M Y') : 'Terbit' }}
                        </span>

                        <div class="flex items-center gap-2">
                            @if ($portfolio->tautan)
                                <a
                                    href="{{ $portfolio->tautan }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors font-medium text-xs"
                                >
                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    Kunjungi URL
                                </a>
                            @endif

                            @if ($portfolio->lampiran)
                                <a
                                    href="{{ asset('storage/' . $portfolio->lampiran) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors font-medium text-xs"
                                >
                                    <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                    Berkas
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $portfolios->links() }}
        </div>
    @else
        <div class="p-16 rounded-2xl bg-white dark:bg-neutral-900 border border-slate-200 dark:border-neutral-800 text-center shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-neutral-800 text-slate-400 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="folder-git-2" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Belum Ada Portofolio Ditemukan</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                Belum ada siswa yang mengunggah proyek pada kategori atau kelas yang dipilih.
            </p>
        </div>
    @endif
</div>
@endsection
