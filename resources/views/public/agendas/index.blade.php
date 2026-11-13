@extends('layouts.landing')

@section('title', 'Kalender & Agenda Kegiatan BKK SMKN 1 Cirebon')

@section('content')
<div class="py-12 sm:py-16 bg-surface-bg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <!-- Hero Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-primary/10 text-brand-secondary dark:text-brand-primary border border-brand-primary/20">
                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                <span>Jadwal & agenda vokasi</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-text-primary tracking-tight font-heading">
                Kalender Kegiatan BKK & Industri
            </h1>
            <p class="text-sm sm:text-base text-text-secondary">
                Informasi jadwal kunjungan industri, workshop karier, seleksi rekrutmen mitra, uji kompetensi, dan pembekalan Praktik Kerja Lapangan.
            </p>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-surface-card border border-border-subtle rounded-card p-4 sm:p-5 shadow-xs max-w-4xl mx-auto">
            <form method="GET" action="{{ route('public.agendas') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2 relative">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari kegiatan atau lokasi pelaksanaan..."
                        class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-input border border-border-default/60 bg-surface-bg text-text-primary placeholder:text-text-secondary/60 focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-colors"
                    >
                    <i data-lucide="search" class="w-4 h-4 text-text-secondary absolute left-3.5 top-3"></i>
                </div>

                <div class="flex items-center gap-2">
                    <select
                        name="jenis"
                        class="choices-select w-full px-3 py-2.5 text-xs sm:text-sm rounded-input border border-border-default/60 bg-surface-bg text-text-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-colors"
                        onchange="this.form.submit()"
                    >
                        <option value="">Semua kategori</option>
                        <option value="akademik" {{ request('jenis') === 'akademik' ? 'selected' : '' }}>Akademik / PKL</option>
                        <option value="umum" {{ request('jenis') === 'umum' ? 'selected' : '' }}>Umum / Job Fair</option>
                        <option value="ujian" {{ request('jenis') === 'ujian' ? 'selected' : '' }}>Uji kompetensi / seleksi</option>
                        <option value="libur" {{ request('jenis') === 'libur' ? 'selected' : '' }}>Libur</option>
                    </select>

                    <button type="submit" class="px-5 py-2.5 rounded-btn bg-brand-primary text-text-on-brand-primary font-semibold text-xs sm:text-sm hover:brightness-105 transition-all active:scale-[0.96] shadow-xs">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Agendas Timeline Cards -->
        @if ($agendas->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($agendas as $agenda)
                    <div class="bg-surface-card border border-border-subtle rounded-card p-6 shadow-xs hover:border-brand-primary/50 transition-all flex flex-col justify-between">
                        <div>
                            <!-- Header badge & date -->
                            <div class="flex items-center justify-between gap-2 mb-4">
                                @if ($agenda->jenis === 'akademik')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-state-info-bg text-state-info-text border border-sky-500/30">
                                        Akademik / PKL
                                    </span>
                                @elseif ($agenda->jenis === 'umum')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-state-success-bg text-state-success-text border border-emerald-500/30">
                                        Umum / rekrutmen
                                    </span>
                                @elseif ($agenda->jenis === 'ujian')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-300 dark:border-purple-800">
                                        Seleksi DUDI
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-surface-bg text-text-secondary border border-border-subtle">
                                        Libur
                                    </span>
                                @endif

                                <div class="text-[11px] font-semibold text-text-secondary">
                                    {{ $agenda->tanggal_mulai ? $agenda->tanggal_mulai->format('d M Y') : '-' }}
                                </div>
                            </div>

                            <h3 class="text-base font-bold text-text-primary font-heading line-clamp-2 mb-2">
                                {{ $agenda->judul }}
                            </h3>

                            <p class="text-xs text-text-secondary line-clamp-3 leading-relaxed mb-4">
                                {{ $agenda->deskripsi ?: 'Tidak ada keterangan tambahan untuk agenda ini.' }}
                            </p>
                        </div>

                        <!-- Footer Meta -->
                        <div class="pt-4 border-t border-border-subtle space-y-1.5 text-xs text-text-secondary">
                            <div class="flex items-center gap-2">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-text-secondary flex-shrink-0"></i>
                                <span class="line-clamp-1">{{ $agenda->lokasi ?: 'Aula SMKN 1 Cirebon' }}</span>
                            </div>
                            @if ($agenda->tanggal_selesai && $agenda->tanggal_selesai->ne($agenda->tanggal_mulai))
                                <div class="flex items-center gap-2 text-[11px]">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-text-secondary flex-shrink-0"></i>
                                    <span>Berlangsung s.d. {{ $agenda->tanggal_selesai->format('d M Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $agendas->links() }}
            </div>
        @else
            <div class="py-16 text-center bg-surface-card border border-border-subtle rounded-card max-w-xl mx-auto p-8 shadow-xs">
                <div class="w-16 h-16 rounded-card bg-surface-bg text-text-secondary flex items-center justify-center mx-auto mb-4 border border-border-subtle">
                    <i data-lucide="calendar-x" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-bold text-text-primary font-heading">Tidak ada agenda terjadwal</h3>
                <p class="text-xs text-text-secondary mt-1 max-w-sm mx-auto mb-4">
                    Belum ada agenda kegiatan yang terdaftar untuk kriteria yang Anda cari.
                </p>
                <a href="{{ route('public.agendas') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-btn bg-surface-bg border border-border-default/60 text-xs font-semibold text-text-primary hover:border-text-primary transition-all active:scale-[0.96]">
                    Reset filter
                </a>
            </div>
        @endif
    </div>
</div>
@endsection