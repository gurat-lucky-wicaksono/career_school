@extends('layouts.admin')

@section('title', 'Manajemen Agenda Kegiatan BKK')

@section('content')
<div class="space-y-6">
    @php
        $headers = [
            ['key' => 'judul', 'label' => 'Nama Agenda Kegiatan', 'sortable' => false],
            ['key' => 'jenis', 'label' => 'Kategori Jenis', 'sortable' => false],
            ['key' => 'jadwal', 'label' => 'Jadwal & Waktu', 'sortable' => false],
            ['key' => 'lokasi', 'label' => 'Lokasi Pelaksanaan', 'sortable' => false],
            ['key' => 'penulis', 'label' => 'Penyelenggara', 'sortable' => false],
        ];
        $isFilterActive = request()->filled('jenis');
    @endphp

    <!-- Quick Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
        @php
            $currentJenis = request('jenis', '');
        @endphp
        <a 
            href="{{ route('admin.agendas.index', request()->except('jenis', 'page')) }}" 
            class="px-4 py-2 rounded-full font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentJenis === '' ? 'bg-brand-primary text-text-on-brand-primary font-bold shadow-xs' : 'bg-surface-card border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg' }}"
        >
            <span>Semua Agenda</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-black/10 dark:bg-white/10">
                {{ $jenisCounts['all'] }}
            </span>
        </a>

        <a 
            href="{{ route('admin.agendas.index', array_merge(request()->except('page'), ['jenis' => 'akademik'])) }}" 
            class="px-4 py-2 rounded-full font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentJenis === 'akademik' ? 'bg-brand-primary text-text-on-brand-primary font-bold shadow-xs' : 'bg-surface-card border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg' }}"
        >
            <span>Akademik / PKL</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-500/20 text-sky-600 dark:text-sky-400">
                {{ $jenisCounts['akademik'] }}
            </span>
        </a>

        <a 
            href="{{ route('admin.agendas.index', array_merge(request()->except('page'), ['jenis' => 'umum'])) }}" 
            class="px-4 py-2 rounded-full font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentJenis === 'umum' ? 'bg-brand-primary text-text-on-brand-primary font-bold shadow-xs' : 'bg-surface-card border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg' }}"
        >
            <span>Umum / Job Fair</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                {{ $jenisCounts['umum'] }}
            </span>
        </a>

        <a 
            href="{{ route('admin.agendas.index', array_merge(request()->except('page'), ['jenis' => 'ujian'])) }}" 
            class="px-4 py-2 rounded-full font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentJenis === 'ujian' ? 'bg-brand-primary text-text-on-brand-primary font-bold shadow-xs' : 'bg-surface-card border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg' }}"
        >
            <span>Uji Kompetensi / Seleksi</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/20 text-purple-600 dark:text-purple-400">
                {{ $jenisCounts['ujian'] }}
            </span>
        </a>
    </div>

    <!-- Data Table Component -->
    <x-admin.data-table 
        title="Agenda Kegiatan BKK SMKN 1 Cirebon"
        subtitle="Jadwalkan agenda kunjungan industri, workshop karir, pembekalan PKL, dan job fair vokasi"
        searchPlaceholder="Cari judul agenda atau lokasi..."
        :search="request('search', request('q'))"
        :items="$agendas"
        :headers="$headers"
    >
        <x-slot:filter>
            <button 
                type="button" 
                onclick="window.openModal('modal-filter-agendas')"
                class="relative h-11 px-4 text-xs sm:text-sm font-semibold rounded-full border border-border-default bg-surface-card text-text-primary hover:border-text-secondary/70 hover:bg-surface-bg flex items-center gap-2 transition-all shadow-xs"
            >
                <i data-lucide="filter" class="w-4 h-4 text-text-secondary"></i>
                <span>Filter</span>
                @if ($isFilterActive)
                    <span class="w-2 h-2 rounded-full bg-brand-primary"></span>
                @endif
            </button>
        </x-slot:filter>

        <x-slot:action>
            <x-ui.button variant="primary" href="{{ route('admin.agendas.create') }}">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Tambah Agenda Kegiatan
            </x-ui.button>
        </x-slot:action>

        @forelse ($agendas as $item)
            <tr class="hover:bg-surface-bg/50 transition-colors group">
                <!-- Column: Nama Agenda & Deskripsi -->
                <td class="px-5 py-3.5 max-w-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-surface-bg border border-border-default flex items-center justify-center font-bold text-xs text-brand-secondary dark:text-brand-primary shrink-0">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="font-semibold text-text-primary block truncate text-xs sm:text-sm" title="{{ $item->judul }}">
                                {{ $item->judul }}
                            </span>
                            @if ($item->deskripsi)
                                <span class="text-xs text-text-secondary block line-clamp-1 mt-0.5">
                                    {{ Str::limit(strip_tags($item->deskripsi), 70) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </td>

                <!-- Column: Kategori Jenis -->
                <td class="px-5 py-3.5 whitespace-nowrap">
                    @if ($item->jenis === 'akademik')
                        <x-ui.badge variant="info">Akademik / PKL</x-ui.badge>
                    @elseif ($item->jenis === 'umum')
                        <x-ui.badge variant="success">Umum / Job Fair</x-ui.badge>
                    @elseif ($item->jenis === 'ujian')
                        <x-ui.badge variant="warning">Uji Kompetensi</x-ui.badge>
                    @else
                        <x-ui.badge variant="secondary">Lainnya</x-ui.badge>
                    @endif
                </td>

                <!-- Column: Jadwal & Waktu -->
                <td class="px-5 py-3.5 text-xs text-text-secondary whitespace-nowrap">
                    <div class="font-medium text-text-primary">
                        {{ $item->tanggal_mulai ? $item->tanggal_mulai->format('d M Y') : '-' }}
                    </div>
                    @if ($item->tanggal_selesai && $item->tanggal_selesai->format('Y-m-d') !== ($item->tanggal_mulai ? $item->tanggal_mulai->format('Y-m-d') : ''))
                        <div class="text-[11px] text-text-muted mt-0.5">s.d. {{ $item->tanggal_selesai->format('d M Y') }}</div>
                    @endif
                    @if ($item->jam_mulai)
                        <div class="text-[11px] text-text-muted mt-0.5">
                            {{ substr($item->jam_mulai, 0, 5) }} {{ $item->jam_selesai ? '- ' . substr($item->jam_selesai, 0, 5) : 'WIB' }}
                        </div>
                    @endif
                </td>

                <!-- Column: Lokasi -->
                <td class="px-5 py-3.5 text-xs text-text-secondary whitespace-nowrap">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-text-muted shrink-0"></i>
                        <span class="truncate max-w-[180px]">{{ $item->lokasi ?? 'SMKN 1 Cirebon' }}</span>
                    </div>
                </td>

                <!-- Column: Penulis / Penyelenggara -->
                <td class="px-5 py-3.5 text-xs text-text-secondary whitespace-nowrap">
                    {{ $item->creator->name ?? 'Admin BKK' }}
                </td>

                <!-- Column: Aksi -->
                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                        <x-ui.icon-button 
                            size="sm" 
                            icon="eye" 
                            href="{{ route('admin.agendas.show', $item) }}"
                            label="Lihat Detail Agenda" 
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            icon="pencil" 
                            href="{{ route('admin.agendas.edit', $item) }}"
                            label="Edit Agenda" 
                        />
                        <form method="POST" action="{{ route('admin.agendas.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda kegiatan ini?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-ui.icon-button 
                                size="sm" 
                                icon="trash-2" 
                                variant="danger" 
                                type="submit" 
                                label="Hapus Agenda" 
                            />
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="p-8 text-center text-text-secondary text-sm">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <i data-lucide="calendar" class="w-8 h-8 text-text-muted"></i>
                        <p class="font-semibold text-text-primary">Belum ada agenda kegiatan BKK</p>
                        <p class="text-xs text-text-muted">Mulai jadwalkan agenda kunjungan industri, workshop karier, pembekalan PKL, dan job fair.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.data-table>
</div>

<!-- Modal Filter Agenda -->
<x-ui.modal 
    id="modal-filter-agendas" 
    title="Filter Agenda Kegiatan" 
    subtitle="Saring agenda kegiatan berdasarkan kategori jenis"
    size="md"
>
    <form method="GET" action="{{ route('admin.agendas.index') }}" class="space-y-4">
        @if (request('search', request('q')))
            <input type="hidden" name="search" value="{{ request('search', request('q')) }}">
        @endif

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Kategori Jenis Agenda
            </label>
            <select 
                name="jenis" 
                class="choices-select w-full"
            >
                <option value="">Semua Agenda</option>
                <option value="akademik" {{ request('jenis') === 'akademik' ? 'selected' : '' }}>Akademik / PKL</option>
                <option value="umum" {{ request('jenis') === 'umum' ? 'selected' : '' }}>Umum / Job Fair</option>
                <option value="ujian" {{ request('jenis') === 'ujian' ? 'selected' : '' }}>Uji Kompetensi / Seleksi</option>
            </select>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-between gap-3">
            <a 
                href="{{ route('admin.agendas.index') }}" 
                class="px-4 py-2 text-xs font-semibold text-text-secondary hover:text-text-primary transition-colors"
            >
                Reset Filter
            </a>
            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    onclick="window.closeModal('modal-filter-agendas')"
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
@endsection
