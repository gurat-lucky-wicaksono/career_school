@extends('layouts.admin')

@section('title', 'Manajemen Pengumuman BKK')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Pengumuman BKK SMKN 1 Cirebon
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Publikasikan informasi rekrutmen massal, pengumuman magang/PKL, dan pedoman kerja untuk siswa, guru, dan publik.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button variant="primary" size="md" href="{{ route('admin.announcements.create') }}">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Tulis Pengumuman Baru
            </x-ui.button>
        </div>
    </div>

    <!-- Status Tabs & Filter -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-4 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 text-xs">
                @php
                    $currentStatus = request('status', '');
                @endphp
                <a 
                    href="{{ route('admin.announcements.index') }}" 
                    class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentStatus === '' ? 'bg-brand-primary text-neutral-900 font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-800' }}"
                >
                    <span>Semua</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-black/10 dark:bg-white/10">
                        {{ $statusCounts['all'] }}
                    </span>
                </a>

                <a 
                    href="{{ route('admin.announcements.index', ['status' => 'terbit']) }}" 
                    class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentStatus === 'terbit' ? 'bg-brand-primary text-neutral-900 font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-800' }}"
                >
                    <span>Terbit</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus === 'terbit' ? 'bg-black/10' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' }}">
                        {{ $statusCounts['terbit'] }}
                    </span>
                </a>

                <a 
                    href="{{ route('admin.announcements.index', ['status' => 'draft']) }}" 
                    class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentStatus === 'draft' ? 'bg-brand-primary text-neutral-900 font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-800' }}"
                >
                    <span>Draft</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus === 'draft' ? 'bg-black/10' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                        {{ $statusCounts['draft'] }}
                    </span>
                </a>

                <a 
                    href="{{ route('admin.announcements.index', ['status' => 'arsip']) }}" 
                    class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentStatus === 'arsip' ? 'bg-brand-primary text-neutral-900 font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-800' }}"
                >
                    <span>Arsip</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus === 'arsip' ? 'bg-black/10' : 'bg-slate-200 text-slate-700 dark:bg-neutral-700 dark:text-slate-300' }}">
                        {{ $statusCounts['arsip'] }}
                    </span>
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.announcements.index') }}" class="relative w-full sm:w-72">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari judul pengumuman..."
                    class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary"
                >
                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5"></i>
            </form>
        </div>
    </div>

    <!-- Announcement Table -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden">
        @if ($announcements->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-white/5 bg-slate-50/75 dark:bg-neutral-800/50 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-6">Judul Pengumuman</th>
                            <th class="py-3 px-4">Prioritas</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Jadwal Tayang</th>
                            <th class="py-3 px-4">Penulis</th>
                            <th class="py-3 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-xs">
                        @foreach ($announcements as $item)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-neutral-800/40 transition-colors">
                                <!-- Judul & Lampiran -->
                                <td class="py-4 px-6 max-w-xs">
                                    <p class="font-semibold text-slate-900 dark:text-white line-clamp-1">
                                        {{ $item->judul }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">
                                        {{ Str::limit(strip_tags($item->isi), 80) }}
                                    </p>
                                    @if ($item->lampiran)
                                        <a href="{{ asset('storage/' . $item->lampiran) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-600 dark:text-emerald-400 hover:underline mt-1">
                                            <i data-lucide="paperclip" class="w-3 h-3"></i>
                                            Lihat Lampiran
                                        </a>
                                    @endif
                                </td>

                                <!-- Prioritas -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if ($item->prioritas === 'penting')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                            Penting
                                        </span>
                                    @elseif ($item->prioritas === 'normal')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                            Normal
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-slate-300">
                                            Rendah
                                        </span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if ($item->status === 'terbit')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            Terbit
                                        </span>
                                    @elseif ($item->status === 'draft')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            Draft
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-slate-300">
                                            Arsip
                                        </span>
                                    @endif
                                </td>

                                <!-- Jadwal Tayang -->
                                <td class="py-4 px-4 whitespace-nowrap text-slate-600 dark:text-slate-300 text-[11px]">
                                    <div>Mulai: {{ $item->mulai_tampil ? $item->mulai_tampil->format('d M Y') : 'Sekarang' }}</div>
                                    @if ($item->selesai_tampil)
                                        <div class="text-slate-400 mt-0.5">Sampai: {{ $item->selesai_tampil->format('d M Y') }}</div>
                                    @endif
                                </td>

                                <!-- Penulis -->
                                <td class="py-4 px-4 whitespace-nowrap text-slate-700 dark:text-slate-300">
                                    {{ $item->author->name ?? 'Admin BKK' }}
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a 
                                            href="{{ route('admin.announcements.edit', $item) }}" 
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors"
                                            title="Edit Pengumuman"
                                        >
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>

                                        <form method="POST" action="{{ route('admin.announcements.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors"
                                                title="Hapus Pengumuman"
                                            >
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5">
                {{ $announcements->links() }}
            </div>
        @else
            <div class="py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-neutral-800 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="megaphone" class="w-8 h-8"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Belum Ada Pengumuman BKK</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                    Mulai publikasikan informasi resmi bursa kerja khusus dan panduan magang siswa vokasi.
                </p>
                <div class="mt-4">
                    <x-ui.button variant="primary" size="sm" href="{{ route('admin.announcements.create') }}">
                        <i data-lucide="plus" class="w-3.5 h-3.5 mr-1.5"></i>
                        Buat Pengumuman Sekarang
                    </x-ui.button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
