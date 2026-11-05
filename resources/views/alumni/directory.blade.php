@extends('layouts.landing')

@section('title', 'Direktori & Tracer Study Alumni SMKN 1 Cirebon')

@section('content')
<div class="py-12 sm:py-16 bg-surface-bg min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        <!-- Header Hero -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-primary/10 text-brand-secondary dark:text-brand-primary border border-brand-primary/20">
                <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i>
                <span>Jejaring lulusan & tracer study BKK</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-text-primary tracking-tight font-heading">
                Direktori Alumni Vokasi
            </h1>
            <p class="text-sm sm:text-base text-text-secondary">
                Menelusuri jejak karya dan keterserapan lulusan SMK Negeri 1 Cirebon di Dunia Usaha, Dunia Industri, Perguruan Tinggi, dan Wirausaha Mandiri (BMW).
            </p>
        </div>

        <!-- Banner Tracer Study Action -->
        <div class="relative overflow-hidden rounded-card bg-neutral-900 text-neutral-50 p-6 sm:p-8 shadow-xs border border-neutral-800">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-2 text-center md:text-left">
                    <span class="inline-block px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-brand-primary text-text-on-brand-primary">
                        Penelusuran tamatan resmi
                    </span>
                    <h2 class="text-xl sm:text-2xl font-bold font-heading">Apakah Anda alumni SMKN 1 Cirebon?</h2>
                    <p class="text-xs sm:text-sm text-neutral-300 max-w-2xl">
                        Partisipasi Anda dalam mengisi kuesioner Tracer Study sangat berharga untuk peningkatan mutu kurikulum kejuruan, akreditasi sekolah, dan membuka peluang kerja bagi adik kelas.
                    </p>
                </div>
                <div class="shrink-0">
                    <a
                        href="{{ route('tracer.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-3 rounded-btn bg-brand-primary text-text-on-brand-primary font-bold text-sm shadow-xs hover:brightness-105 active:scale-[0.96] transition-all"
                    >
                        <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                        <span>Isi kuesioner tracer study</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-surface-card border border-border-subtle rounded-card p-4 sm:p-5 shadow-xs text-center">
                <span class="text-xs font-medium text-text-secondary block mb-1">Total Alumni Terdata</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-text-primary font-heading">{{ $totalAlumni }}</span>
                <span class="text-[11px] text-text-secondary block mt-1">Lulusan SMKN 1 Cirebon</span>
            </div>

            <div class="bg-surface-card border border-border-subtle rounded-card p-4 sm:p-5 shadow-xs text-center">
                <span class="text-xs font-semibold text-state-success-text block mb-1">Bekerja di industri</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-state-success-text font-heading">{{ $bekerjaCount }}</span>
                <span class="text-[11px] text-text-secondary block mt-1">
                    {{ $totalAlumni > 0 ? round(($bekerjaCount / $totalAlumni) * 100, 1) : 0 }}% keterserapan
                </span>
            </div>

            <div class="bg-surface-card border border-border-subtle rounded-card p-4 sm:p-5 shadow-xs text-center">
                <span class="text-xs font-semibold text-state-info-text block mb-1">Lanjut studi / kuliah</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-state-info-text font-heading">{{ $kuliahCount }}</span>
                <span class="text-[11px] text-text-secondary block mt-1">
                    {{ $totalAlumni > 0 ? round(($kuliahCount / $totalAlumni) * 100, 1) : 0 }}% studi lanjut
                </span>
            </div>

            <div class="bg-surface-card border border-border-subtle rounded-card p-4 sm:p-5 shadow-xs text-center">
                <span class="text-xs font-semibold text-brand-secondary dark:text-brand-primary block mb-1">Wirausaha mandiri</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-brand-secondary dark:text-brand-primary font-heading">{{ $wirausahaCount }}</span>
                <span class="text-[11px] text-text-secondary block mt-1">
                    {{ $totalAlumni > 0 ? round(($wirausahaCount / $totalAlumni) * 100, 1) : 0 }}% mandiri
                </span>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('alumni.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="lg:col-span-2 relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama alumni, instansi, atau jabatan..."
                        class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-input bg-surface-bg border border-border-default/60 text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-colors"
                    >
                    <i data-lucide="search" class="w-4 h-4 text-text-secondary absolute left-3.5 top-3.5"></i>
                </div>

                <div>
                    <select
                        name="status"
                        class="w-full px-3 py-2.5 text-xs sm:text-sm rounded-input bg-surface-bg border border-border-default/60 text-text-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-colors"
                    >
                        <option value="all">Semua status BMW</option>
                        <option value="bekerja" {{ request('status') === 'bekerja' ? 'selected' : '' }}>Bekerja</option>
                        <option value="kuliah" {{ request('status') === 'kuliah' ? 'selected' : '' }}>Melanjutkan kuliah</option>
                        <option value="wirausaha" {{ request('status') === 'wirausaha' ? 'selected' : '' }}>Wirausaha / freelance</option>
                        <option value="mencari_kerja" {{ request('status') === 'mencari_kerja' ? 'selected' : '' }}>Mencari kerja</option>
                    </select>
                </div>

                <div>
                    <select
                        name="tahun"
                        class="w-full px-3 py-2.5 text-xs sm:text-sm rounded-input bg-surface-bg border border-border-default/60 text-text-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-colors"
                    >
                        <option value="all">Semua tahun lulus</option>
                        @foreach($years as $yr)
                            <option value="{{ $yr }}" {{ request('tahun') == $yr ? 'selected' : '' }}>Angkatan {{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="w-full py-2.5 px-4 rounded-btn bg-brand-primary text-text-on-brand-primary font-bold text-xs sm:text-sm shadow-xs hover:brightness-105 active:scale-[0.96] transition-all text-center"
                    >
                        Terapkan filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'tahun', 'major_id']))
                        <a
                            href="{{ route('alumni.index') }}"
                            class="p-2.5 rounded-btn border border-border-default/60 text-text-secondary hover:text-text-primary hover:bg-surface-bg transition-all active:scale-[0.96]"
                            title="Reset filter"
                        >
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Alumni Grid -->
        @if($alumni->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($alumni as $item)
                    <div class="bg-surface-card border border-border-subtle rounded-card p-5 shadow-xs flex flex-col justify-between hover:border-brand-primary/50 transition-all duration-150">
                        <div class="space-y-4">
                            <!-- User Card Header -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-btn bg-brand-primary text-text-on-brand-primary flex items-center justify-center font-bold text-base font-heading shrink-0 shadow-xs">
                                        {{ strtoupper(substr($item->user->name ?? 'A', 0, 2)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-text-primary text-sm sm:text-base font-heading line-clamp-1">
                                            {{ $item->user->name ?? 'Alumni SMKN 1 Cirebon' }}
                                        </h3>
                                        <p class="text-xs text-text-secondary">
                                            Angkatan {{ $item->tahun_lulus }} &bull; {{ $item->major->kode ?? 'PPLG' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                @if($item->status_bmw === 'bekerja')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-state-success-bg text-state-success-text border border-emerald-500/30 whitespace-nowrap">
                                        Bekerja
                                    </span>
                                @elseif($item->status_bmw === 'kuliah')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-state-info-bg text-state-info-text border border-sky-500/30 whitespace-nowrap">
                                        Kuliah
                                    </span>
                                @elseif($item->status_bmw === 'wirausaha')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-300 dark:border-purple-800 whitespace-nowrap">
                                        Wirausaha
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-state-warning-bg text-state-warning-text border border-amber-500/30 whitespace-nowrap">
                                        Mencari kerja
                                    </span>
                                @endif
                            </div>

                            <!-- Career / Study Info -->
                            <div class="bg-surface-bg rounded-btn p-3 border border-border-subtle space-y-1.5 text-xs">
                                <div class="flex items-center gap-2 text-text-primary">
                                    <i data-lucide="briefcase" class="w-3.5 h-3.5 text-text-secondary shrink-0"></i>
                                    <span class="font-semibold truncate">
                                        {{ $item->posisi_studi ?: ($item->pekerjaan ?: 'Belum mengisi posisi') }}
                                    </span>
                                </div>
                                @if($item->nama_instansi || $item->perusahaan)
                                    <div class="flex items-center gap-2 text-text-secondary">
                                        <i data-lucide="building" class="w-3.5 h-3.5 text-text-secondary shrink-0"></i>
                                        <span class="truncate">{{ $item->nama_instansi ?: $item->perusahaan }}</span>
                                    </div>
                                @endif
                                @if($item->keselarasan)
                                    <div class="flex items-center gap-2 pt-1 border-t border-border-subtle">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-500 shrink-0"></i>
                                        <span class="text-text-secondary text-[11px]">
                                            Kesesuaian kejuruan: <strong class="text-text-primary">{{ $item->keselarasan_label }}</strong>
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Testimonial snippet -->
                            @if($item->testimoni)
                                <p class="text-xs text-text-secondary italic line-clamp-2">
                                    &ldquo;{{ $item->testimoni }}&rdquo;
                                </p>
                            @endif
                        </div>

                        <!-- Card Actions -->
                        <div class="pt-4 mt-4 border-t border-border-subtle flex items-center justify-between gap-2">
                            <a
                                href="{{ route('alumni.show', $item->id) }}"
                                class="text-xs font-semibold text-brand-secondary dark:text-brand-primary hover:underline inline-flex items-center gap-1"
                            >
                                <span>Lihat jejak alumni</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>

                            @if($item->user && $item->user->username)
                                <a
                                    href="{{ route('community.profile', $item->user->username) }}"
                                    class="text-xs text-text-secondary hover:text-text-primary px-2.5 py-1 rounded-btn border border-border-subtle hover:border-text-secondary transition-all active:scale-[0.96]"
                                    title="Kunjungi profil komunitas"
                                >
                                    Komunitas
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pt-6">
                {{ $alumni->links() }}
            </div>
        @else
            <div class="bg-surface-card border border-border-subtle rounded-card p-12 text-center max-w-lg mx-auto space-y-3 shadow-xs">
                <div class="w-12 h-12 rounded-full bg-surface-bg text-text-secondary mx-auto flex items-center justify-center border border-border-subtle">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-text-primary text-base font-heading">Tidak ada data alumni</h3>
                <p class="text-xs text-text-secondary">
                    Tidak ditemukan data alumni yang sesuai dengan parameter pencarian atau filter yang dipilih.
                </p>
                <a
                    href="{{ route('alumni.index') }}"
                    class="inline-block text-xs font-semibold text-brand-secondary dark:text-brand-primary hover:underline mt-2"
                >
                    Kembali ke semua alumni
                </a>
            </div>
        @endif

    </div>
</div>
@endsection