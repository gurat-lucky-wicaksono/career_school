@extends('layouts.teacher')

@section('title', 'Portofolio Siswa Bimbingan')

@section('content')
<div class="space-y-8">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-heading font-bold text-text-primary tracking-tight">
                Portofolio siswa bimbingan
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Eksplorasi dan tinjau proyek kejuruan, repositori kode, dan hasil karya siswa sebagai referensi rekomendasi industri.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <x-ui.button variant="secondary" size="md" href="{{ route('teacher.mentoring.index') }}">
                Kembali ke bimbingan
            </x-ui.button>
            <x-ui.button variant="primary" size="md" href="{{ route('teacher.skills.index') }}">
                Validasi keahlian
            </x-ui.button>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="p-5 rounded-card bg-surface-card border border-border-subtle shadow-xs">
        <form method="GET" action="{{ route('teacher.portfolios.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            <!-- Search -->
            <div class="lg:col-span-5">
                <label for="search" class="block text-xs font-semibold text-text-primary mb-1.5">
                    Cari judul karya / siswa
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        id="search"
                        value="{{ request('q') }}"
                        placeholder="Ketik judul proyek, nama siswa, atau teknologi..."
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
                    <option value="">Semua rombel</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->nama }} (Tingkat {{ $class->tingkat }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kategori -->
            <div class="lg:col-span-2">
                <label for="kategori" class="block text-xs font-semibold text-text-primary mb-1.5">
                    Kategori proyek
                </label>
                <select
                    name="kategori"
                    id="kategori"
                    class="choices-select w-full"
                >
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Actions -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <x-ui.button variant="primary" type="submit" class="flex-1 justify-center">
                    Filter
                </x-ui.button>
                @if (request()->hasAny(['q', 'class_id', 'kategori']))
                    <a href="{{ route('teacher.portfolios.index') }}" class="h-10 w-10 rounded-full border border-border-default hover:border-text-secondary/70 hover:bg-surface-bg text-text-secondary hover:text-text-primary flex items-center justify-center transition-all shadow-xs shrink-0" title="Reset filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Portfolio Grid -->
    @if ($portfolios->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($portfolios as $portfolio)
                <div class="bg-surface-card border border-border-subtle rounded-card shadow-xs hover:shadow-sm transition-shadow flex flex-col justify-between overflow-hidden">
                    <div class="p-6">
                        <!-- Top Header: Student badge & Category -->
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-neutral-100 dark:bg-neutral-800 text-text-primary font-bold text-xs flex items-center justify-center flex-shrink-0 border border-border-subtle">
                                    {{ strtoupper(substr($portfolio->student->user->name ?? 'S', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-xs font-heading font-semibold text-text-primary line-clamp-1">
                                        {{ $portfolio->student->user->name ?? 'Siswa' }}
                                    </p>
                                    <p class="text-[11px] text-text-muted">
                                        {{ $portfolio->student->schoolClass->nama ?? 'Kelas' }}
                                    </p>
                                </div>
                            </div>

                            @if ($portfolio->kategori)
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-state-success-bg text-state-success-text border border-state-success-border">
                                    {{ ucfirst($portfolio->kategori) }}
                                </span>
                            @endif
                        </div>

                        <!-- Title & Description -->
                        <h3 class="text-base font-heading font-bold text-text-primary line-clamp-1 mb-2">
                            {{ $portfolio->judul }}
                        </h3>
                        <p class="text-xs text-text-secondary line-clamp-3 leading-relaxed">
                            {{ $portfolio->deskripsi ?: 'Tidak ada deskripsi detail untuk portofolio ini.' }}
                        </p>
                    </div>

                    <!-- Footer Details & Links -->
                    <div class="px-6 py-4 bg-surface-bg border-t border-border-subtle flex items-center justify-between text-xs">
                        <span class="text-text-muted text-[11px]">
                            {{ $portfolio->created_at ? $portfolio->created_at->format('d M Y') : 'Terbit' }}
                        </span>

                        <div class="flex items-center gap-2">
                            @if ($portfolio->tautan)
                                <a
                                    href="{{ $portfolio->tautan }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="px-3 py-1.5 rounded-full bg-surface-card border border-border-default text-text-secondary hover:text-text-primary active:scale-[0.96] transition-transform font-medium text-xs shadow-xs"
                                >
                                    Kunjungi tautan
                                </a>
                            @endif

                            @if ($portfolio->lampiran)
                                <a
                                    href="{{ asset('storage/' . $portfolio->lampiran) }}"
                                    target="_blank"
                                    class="px-3 py-1.5 rounded-full bg-surface-card border border-border-default text-text-secondary hover:text-text-primary active:scale-[0.96] transition-transform font-medium text-xs shadow-xs"
                                >
                                    Lihat berkas
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
        <div class="p-16 rounded-card bg-surface-card border border-border-subtle text-center shadow-xs">
            <div class="w-16 h-16 rounded-card bg-surface-bg border border-border-subtle text-text-muted flex items-center justify-center mx-auto mb-4">
                <i data-lucide="folder-git-2" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base font-heading font-bold text-text-primary">Belum ada portofolio ditemukan</h3>
            <p class="text-sm text-text-secondary mt-1 max-w-sm mx-auto">
                Belum ada siswa yang mengunggah proyek pada kategori atau kelas yang dipilih.
            </p>
        </div>
    @endif
</div>
@endsection
