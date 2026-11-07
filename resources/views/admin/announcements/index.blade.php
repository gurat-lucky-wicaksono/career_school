@extends('layouts.admin')

@section('title', 'Manajemen Pengumuman BKK')

@section('content')
<div class="space-y-6">
    @php
        $headers = [
            ['key' => 'judul', 'label' => 'Judul Pengumuman', 'sortable' => false],
            ['key' => 'prioritas', 'label' => 'Prioritas', 'sortable' => false],
            ['key' => 'status', 'label' => 'Status', 'sortable' => false],
            ['key' => 'jadwal', 'label' => 'Jadwal Tayang', 'sortable' => false],
            ['key' => 'penulis', 'label' => 'Penulis', 'sortable' => false],
        ];
        $isFilterActive = request()->filled('status') || request()->filled('prioritas');
    @endphp

    <!-- Quick Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
        @php
            $currentStatus = request('status', '');
        @endphp
        <a 
            href="{{ route('admin.announcements.index', request()->except('status', 'page')) }}" 
            class="px-4 py-2 rounded-full font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentStatus === '' ? 'bg-brand-primary text-text-on-brand-primary font-bold shadow-xs' : 'bg-surface-card border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg' }}"
        >
            <span>Semua</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-black/10 dark:bg-white/10">
                {{ $statusCounts['all'] }}
            </span>
        </a>

        <a 
            href="{{ route('admin.announcements.index', array_merge(request()->except('page'), ['status' => 'terbit'])) }}" 
            class="px-4 py-2 rounded-full font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentStatus === 'terbit' ? 'bg-brand-primary text-text-on-brand-primary font-bold shadow-xs' : 'bg-surface-card border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg' }}"
        >
            <span>Terbit</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                {{ $statusCounts['terbit'] }}
            </span>
        </a>

        <a 
            href="{{ route('admin.announcements.index', array_merge(request()->except('page'), ['status' => 'draft'])) }}" 
            class="px-4 py-2 rounded-full font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentStatus === 'draft' ? 'bg-brand-primary text-text-on-brand-primary font-bold shadow-xs' : 'bg-surface-card border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg' }}"
        >
            <span>Draft</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-600 dark:text-amber-400">
                {{ $statusCounts['draft'] }}
            </span>
        </a>

        <a 
            href="{{ route('admin.announcements.index', array_merge(request()->except('page'), ['status' => 'arsip'])) }}" 
            class="px-4 py-2 rounded-full font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentStatus === 'arsip' ? 'bg-brand-primary text-text-on-brand-primary font-bold shadow-xs' : 'bg-surface-card border border-border-default text-text-secondary hover:text-text-primary hover:bg-surface-bg' }}"
        >
            <span>Arsip</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-neutral-500/20 text-neutral-600 dark:text-neutral-400">
                {{ $statusCounts['arsip'] }}
            </span>
        </a>
    </div>

    <!-- Data Table Component -->
    <x-admin.data-table 
        title="Pengumuman BKK SMKN 1 Cirebon"
        subtitle="Publikasikan informasi rekrutmen massal, pengumuman magang/PKL, dan pedoman kerja"
        searchPlaceholder="Cari judul pengumuman..."
        :search="request('search', request('q'))"
        :items="$announcements"
        :headers="$headers"
    >
        <x-slot:filter>
            <button 
                type="button" 
                onclick="window.openModal('modal-filter-announcements')"
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
            <x-ui.button variant="primary" href="{{ route('admin.announcements.create') }}">
                Tulis Pengumuman Baru
            </x-ui.button>
        </x-slot:action>

        @forelse ($announcements as $item)
            <tr class="hover:bg-surface-bg/50 transition-colors group">
                <!-- Column: Judul & Lampiran -->
                <td class="px-5 py-3.5 max-w-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-surface-bg border border-border-default flex items-center justify-center font-bold text-xs text-brand-secondary dark:text-brand-primary shrink-0">
                            <i data-lucide="megaphone" class="w-4 h-4"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="font-semibold text-text-primary block truncate text-xs sm:text-sm" title="{{ $item->judul }}">
                                {{ $item->judul }}
                            </span>
                            <span class="text-xs text-text-secondary block line-clamp-1 mt-0.5">
                                {{ Str::limit(strip_tags($item->isi), 70) }}
                            </span>
                            @if ($item->lampiran)
                                <a href="{{ asset('storage/' . $item->lampiran) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-brand-secondary dark:text-brand-primary hover:underline mt-1">
                                    <i data-lucide="paperclip" class="w-3 h-3"></i>
                                    Lihat Lampiran
                                </a>
                            @endif
                        </div>
                    </div>
                </td>

                <!-- Column: Prioritas -->
                <td class="px-5 py-3.5 whitespace-nowrap">
                    @if ($item->prioritas === 'penting')
                        <x-ui.badge variant="danger">Penting</x-ui.badge>
                    @elseif ($item->prioritas === 'normal')
                        <x-ui.badge variant="info">Normal</x-ui.badge>
                    @else
                        <x-ui.badge variant="secondary">Rendah</x-ui.badge>
                    @endif
                </td>

                <!-- Column: Status -->
                <td class="px-5 py-3.5 whitespace-nowrap">
                    @if ($item->status === 'terbit')
                        <x-ui.badge variant="success">Terbit</x-ui.badge>
                    @elseif ($item->status === 'draft')
                        <x-ui.badge variant="warning">Draft</x-ui.badge>
                    @else
                        <x-ui.badge variant="secondary">Arsip</x-ui.badge>
                    @endif
                </td>

                <!-- Column: Jadwal Tayang -->
                <td class="px-5 py-3.5 text-xs text-text-secondary whitespace-nowrap">
                    <div>Mulai: {{ $item->mulai_tampil ? $item->mulai_tampil->format('d M Y') : 'Sekarang' }}</div>
                    @if ($item->selesai_tampil)
                        <div class="text-text-muted mt-0.5">Sampai: {{ $item->selesai_tampil->format('d M Y') }}</div>
                    @endif
                </td>

                <!-- Column: Penulis -->
                <td class="px-5 py-3.5 text-xs text-text-secondary whitespace-nowrap">
                    {{ $item->author->name ?? 'Admin BKK' }}
                </td>

                <!-- Column: Aksi -->
                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                        <x-ui.icon-button 
                            size="sm" 
                            icon="eye" 
                            href="{{ route('admin.announcements.show', $item) }}"
                            label="Lihat Detail Pengumuman" 
                        />
                        <x-ui.icon-button 
                            size="sm" 
                            icon="pencil" 
                            href="{{ route('admin.announcements.edit', $item) }}"
                            label="Edit Pengumuman" 
                        />
                        <form method="POST" action="{{ route('admin.announcements.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-ui.icon-button 
                                size="sm" 
                                icon="trash-2" 
                                variant="danger" 
                                type="submit" 
                                label="Hapus Pengumuman" 
                            />
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="p-8 text-center text-text-secondary text-sm">
                    <div class="flex flex-col items-center justify-center gap-2">
                        <i data-lucide="megaphone" class="w-8 h-8 text-text-muted"></i>
                        <p class="font-semibold text-text-primary">Belum ada pengumuman BKK</p>
                        <p class="text-xs text-text-muted">Mulai publikasikan informasi resmi bursa kerja khusus dan panduan magang siswa vokasi.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.data-table>
</div>

<!-- Modal Filter Pengumuman -->
<x-ui.modal 
    id="modal-filter-announcements" 
    title="Filter Pengumuman BKK" 
    subtitle="Saring data pengumuman berdasarkan status penerbitan dan prioritas"
    size="md"
>
    <form method="GET" action="{{ route('admin.announcements.index') }}" class="space-y-4">
        @if (request('search', request('q')))
            <input type="hidden" name="search" value="{{ request('search', request('q')) }}">
        @endif

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Status Penerbitan
            </label>
            <select 
                name="status" 
                class="choices-select w-full"
            >
                <option value="">Semua Status</option>
                <option value="terbit" {{ request('status') === 'terbit' ? 'selected' : '' }}>Terbit</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="arsip" {{ request('status') === 'arsip' ? 'selected' : '' }}>Arsip</option>
            </select>
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-text-primary">
                Tingkat Prioritas
            </label>
            <select 
                name="prioritas" 
                class="choices-select w-full"
            >
                <option value="">Semua Prioritas</option>
                <option value="penting" {{ request('prioritas') === 'penting' ? 'selected' : '' }}>Penting</option>
                <option value="normal" {{ request('prioritas') === 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="rendah" {{ request('prioritas') === 'rendah' ? 'selected' : '' }}>Rendah</option>
            </select>
        </div>

        <div class="pt-5 border-t border-border-subtle flex items-center justify-between gap-3">
            <a 
                href="{{ route('admin.announcements.index') }}" 
                class="px-4 py-2 text-xs font-semibold text-text-secondary hover:text-text-primary transition-colors"
            >
                Reset Filter
            </a>
            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    onclick="window.closeModal('modal-filter-announcements')"
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
