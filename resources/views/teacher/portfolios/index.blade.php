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

        <div class="flex items-center gap-3">
            <x-ui.button variant="secondary" size="md" href="{{ route('teacher.mentoring.index') }}">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Kembali ke bimbingan
            </x-ui.button>
            <x-ui.button variant="primary" size="md" href="{{ route('teacher.skills.index') }}">
                <i data-lucide="award" class="w-4 h-4 mr-2"></i>
                Validasi keahlian
            </x-ui.button>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="p-5 rounded-card bg-surface-card border border-border-subtle shadow-xs">
        <form method="GET" action="{{ route('teacher.portfolios.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1.5">
                    Cari judul karya / siswa
                </label>
                <div class="relative">
                    <input
                        type="text"
                        name="q"
                        id="search"
                        value="{{ request('q') }}"
                        placeholder="Ketik judul proyek, nama siswa, atau teknologi..."
                        class="w-full pl-10 pr-4 py-2 text-xs rounded-btn border border-border-subtle bg-surface-bg text-text-primary placeholder:text-text-muted focus:outline-none focus:ring-1 focus:ring-brand-primary"
                    >
                    <i data-lucide="search" class="w-4 h-4 text-text-muted absolute left-3.5 top-2.5"></i>
                </div>
            </div>

            <!-- Filter Kelas -->
            <div>
                <label for="class_id" class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1.5">
                    Rombongan belajar
                </label>
                <select
                    name="class_id"
                    id="class_id"
                    class="w-full px-3 py-2 text-xs rounded-btn border border-border-subtle bg-surface-bg text-text-primary focus:outline-none focus:ring-1 focus:ring-brand-primary"
                    onchange="this.form.submit()"
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
            <div>
                <label for="kategori" class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1.5">
                    Kategori proyek
                </label>
                <select
                    name="kategori"
                    id="kategori"
                    class="w-full px-3 py-2 text-xs rounded-btn border border-border-subtle bg-surface-bg text-text-primary focus:outline-none focus:ring-1 focus:ring-brand-primary"
                    onchange="this.form.submit()"
                >
                    <option value="">Semua kategori</option>
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
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-btn bg-surface-card border border-border-subtle text-text-secondary hover:text-text-primary active:scale-[0.96] transition-transform font-medium text-xs shadow-xs"
                                >
                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    Kunjungi tautan
                                </a>
                            @endif

                            @if ($portfolio->lampiran)
                                <a
                                    href="{{ asset('storage/' . $portfolio->lampiran) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-btn bg-surface-card border border-border-subtle text-text-secondary hover:text-text-primary active:scale-[0.96] transition-transform font-medium text-xs shadow-xs"
                                >
                                    <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
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
