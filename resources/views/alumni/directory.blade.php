@extends('layouts.landing')

@section('title', 'Direktori & Tracer Study Alumni SMKN 1 Cirebon')

@section('content')
<div class="py-12 sm:py-16 bg-surface-bg min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        <!-- Header Hero -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-primary/10 text-brand-secondary dark:text-brand-primary border border-brand-primary/20">
                <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i>
                <span>Jejaring Lulusan & Tracer Study BKK</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight font-sans">
                Direktori Alumni Vokasi
            </h1>
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">
                Menelusuri jejak karya dan keterserapan lulusan SMK Negeri 1 Cirebon di Dunia Usaha, Dunia Industri, Perguruan Tinggi, dan Wirausaha Mandiri (BMW).
            </p>
        </div>

        <!-- Banner Tracer Study Action -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-brand-secondary/90 via-brand-secondary to-indigo-900 text-white p-6 sm:p-8 shadow-md">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-2 text-center md:text-left">
                    <span class="inline-block px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-white/20 text-white">
                        Penelusuran Tamatan Resmi
                    </span>
                    <h2 class="text-xl sm:text-2xl font-bold">Apakah Anda Alumni SMKN 1 Cirebon?</h2>
                    <p class="text-xs sm:text-sm text-slate-200 max-w-2xl">
                        Partisipasi Anda dalam mengisi kuesioner Tracer Study sangat berharga untuk peningkatan mutu kurikulum kejuruan, akreditasi sekolah, dan membuka peluang kerja bagi adik kelas.
                    </p>
                </div>
                <div class="shrink-0">
                    <a
                        href="{{ route('tracer.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-brand-primary text-slate-950 font-bold text-sm shadow hover:brightness-105 active:scale-95 transition-all"
                    >
                        <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                        <span>Isi Kuesioner Tracer Study</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-4 sm:p-5 shadow-xs text-center">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 block mb-1">Total Alumni Terdata</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ $totalAlumni }}</span>
                <span class="text-[11px] text-slate-400 block mt-1">Lulusan SMKN 1 Cirebon</span>
            </div>

            <div class="bg-surface-card border border-emerald-500/20 rounded-2xl p-4 sm:p-5 shadow-xs text-center">
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 block mb-1">Bekerja di Industri</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $bekerjaCount }}</span>
                <span class="text-[11px] text-slate-400 block mt-1">
                    {{ $totalAlumni > 0 ? round(($bekerjaCount / $totalAlumni) * 100, 1) : 0 }}% Keterserapan
                </span>
            </div>

            <div class="bg-surface-card border border-blue-500/20 rounded-2xl p-4 sm:p-5 shadow-xs text-center">
                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 block mb-1">Lanjut Kuliah (PTN/PTS)</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-blue-600 dark:text-blue-400">{{ $kuliahCount }}</span>
                <span class="text-[11px] text-slate-400 block mt-1">
                    {{ $totalAlumni > 0 ? round(($kuliahCount / $totalAlumni) * 100, 1) : 0 }}% Studi Lanjut
                </span>
            </div>

            <div class="bg-surface-card border border-purple-500/20 rounded-2xl p-4 sm:p-5 shadow-xs text-center">
                <span class="text-xs font-semibold text-purple-600 dark:text-purple-400 block mb-1">Wirausaha / Freelance</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-purple-600 dark:text-purple-400">{{ $wirausahaCount }}</span>
                <span class="text-[11px] text-slate-400 block mt-1">
                    {{ $totalAlumni > 0 ? round(($wirausahaCount / $totalAlumni) * 100, 1) : 0 }}% Mandiri
                </span>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('alumni.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="lg:col-span-2 relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama alumni, instansi, atau jabatan..."
                        class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary"
                    >
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                </div>

                <div>
                    <select
                        name="status"
                        class="w-full px-3 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary"
                    >
                        <option value="all">Semua Status BMW</option>
                        <option value="bekerja" {{ request('status') === 'bekerja' ? 'selected' : '' }}>Bekerja</option>
                        <option value="kuliah" {{ request('status') === 'kuliah' ? 'selected' : '' }}>Melanjutkan Kuliah</option>
                        <option value="wirausaha" {{ request('status') === 'wirausaha' ? 'selected' : '' }}>Wirausaha / Freelance</option>
                        <option value="mencari_kerja" {{ request('status') === 'mencari_kerja' ? 'selected' : '' }}>Mencari Kerja</option>
                    </select>
                </div>

                <div>
                    <select
                        name="tahun"
                        class="w-full px-3 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary"
                    >
                        <option value="all">Semua Tahun Lulus</option>
                        @foreach($years as $yr)
                            <option value="{{ $yr }}" {{ request('tahun') == $yr ? 'selected' : '' }}>Angkatan {{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-brand-primary text-slate-950 font-bold text-xs sm:text-sm shadow-xs hover:brightness-105 transition-all text-center"
                    >
                        Terapkan Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'tahun', 'major_id']))
                        <a
                            href="{{ route('alumni.index') }}"
                            class="p-2.5 rounded-xl border border-slate-200 dark:border-white/10 text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 transition-all"
                            title="Reset Filter"
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
                    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-xs flex flex-col justify-between hover:shadow-md hover:border-brand-primary/40 transition-all duration-200">
                        <div class="space-y-4">
                            <!-- User Card Header -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-white/10 text-brand-secondary dark:text-brand-primary flex items-center justify-center font-bold text-base border border-slate-200 dark:border-white/10 shrink-0">
                                        {{ strtoupper(substr($item->user->name ?? 'A', 0, 2)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 dark:text-white text-sm sm:text-base line-clamp-1">
                                            {{ $item->user->name ?? 'Alumni SMKN 1 Cirebon' }}
                                        </h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            Angkatan {{ $item->tahun_lulus }} &bull; {{ $item->major->kode ?? 'PPLG' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                @if($item->status_bmw === 'bekerja')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 whitespace-nowrap">
                                        Bekerja
                                    </span>
                                @elseif($item->status_bmw === 'kuliah')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 whitespace-nowrap">
                                        Kuliah
                                    </span>
                                @elseif($item->status_bmw === 'wirausaha')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 whitespace-nowrap">
                                        Wirausaha
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 whitespace-nowrap">
                                        Mencari Kerja
                                    </span>
                                @endif
                            </div>

                            <!-- Career / Study Info -->
                            <div class="bg-slate-50 dark:bg-white/[0.03] rounded-xl p-3 border border-slate-100 dark:border-white/5 space-y-1.5 text-xs">
                                <div class="flex items-center gap-2 text-slate-700 dark:text-slate-300">
                                    <i data-lucide="briefcase" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                    <span class="font-semibold truncate">
                                        {{ $item->posisi_studi ?: ($item->pekerjaan ?: 'Belum Mengisi Posisi') }}
                                    </span>
                                </div>
                                @if($item->nama_instansi || $item->perusahaan)
                                    <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                                        <i data-lucide="building" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                        <span class="truncate">{{ $item->nama_instansi ?: $item->perusahaan }}</span>
                                    </div>
                                @endif
                                @if($item->keselarasan)
                                    <div class="flex items-center gap-2 pt-1 border-t border-slate-200/60 dark:border-white/5">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-500 shrink-0"></i>
                                        <span class="text-slate-600 dark:text-slate-400 text-[11px]">
                                            Kesesuaian Kejuruan: <strong class="text-slate-900 dark:text-white">{{ $item->keselarasan_label }}</strong>
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Testimonial snippet -->
                            @if($item->testimoni)
                                <p class="text-xs text-slate-600 dark:text-slate-400 italic line-clamp-2">
                                    &ldquo;{{ $item->testimoni }}&rdquo;
                                </p>
                            @endif
                        </div>

                        <!-- Card Actions -->
                        <div class="pt-4 mt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between gap-2">
                            <a
                                href="{{ route('alumni.show', $item->id) }}"
                                class="text-xs font-semibold text-brand-secondary dark:text-brand-primary hover:underline inline-flex items-center gap-1"
                            >
                                <span>Lihat Jejak Alumni</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>

                            @if($item->user && $item->user->username)
                                <a
                                    href="{{ route('community.profile', $item->user->username) }}"
                                    class="text-xs text-slate-500 hover:text-slate-900 dark:hover:text-white px-2.5 py-1 rounded-lg border border-slate-200 dark:border-white/10 transition-colors"
                                    title="Kunjungi Profil Komunitas"
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
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-12 text-center max-w-lg mx-auto space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-white/10 text-slate-400 mx-auto flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <h3 class="font-bold text-slate-900 dark:text-white text-base">Tidak Ada Data Alumni</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Tidak ditemukan data alumni yang sesuai dengan parameter pencarian atau filter yang dipilih.
                </p>
                <a
                    href="{{ route('alumni.index') }}"
                    class="inline-block text-xs font-semibold text-brand-secondary dark:text-brand-primary hover:underline mt-2"
                >
                    Kembali ke Semua Alumni
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
