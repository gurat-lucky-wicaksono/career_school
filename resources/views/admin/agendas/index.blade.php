@extends('layouts.admin')

@section('title', 'Manajemen Agenda Kegiatan BKK')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Agenda Kegiatan BKK SMKN 1 Cirebon
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Jadwalkan agenda kunjungan industri, workshop karir, pembekalan PKL, dan job fair vokasi.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button variant="primary" size="md" href="{{ route('admin.agendas.create') }}">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Tambah Agenda Kegiatan
            </x-ui.button>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-4 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Jenis Filter Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 text-xs">
                @php
                    $currentJenis = request('jenis', '');
                @endphp
                <a 
                    href="{{ route('admin.agendas.index') }}" 
                    class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentJenis === '' ? 'bg-brand-primary text-neutral-900 font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-800' }}"
                >
                    <span>Semua Agenda</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-black/10 dark:bg-white/10">
                        {{ $jenisCounts['all'] }}
                    </span>
                </a>

                <a 
                    href="{{ route('admin.agendas.index', ['jenis' => 'akademik']) }}" 
                    class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentJenis === 'akademik' ? 'bg-brand-primary text-neutral-900 font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-800' }}"
                >
                    <span>Akademik / PKL</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $currentJenis === 'akademik' ? 'bg-black/10' : 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' }}">
                        {{ $jenisCounts['akademik'] }}
                    </span>
                </a>

                <a 
                    href="{{ route('admin.agendas.index', ['jenis' => 'umum']) }}" 
                    class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentJenis === 'umum' ? 'bg-brand-primary text-neutral-900 font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-800' }}"
                >
                    <span>Umum / Job Fair</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $currentJenis === 'umum' ? 'bg-black/10' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' }}">
                        {{ $jenisCounts['umum'] }}
                    </span>
                </a>

                <a 
                    href="{{ route('admin.agendas.index', ['jenis' => 'ujian']) }}" 
                    class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentJenis === 'ujian' ? 'bg-brand-primary text-neutral-900 font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-800' }}"
                >
                    <span>Uji Kompetensi / Seleksi</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $currentJenis === 'ujian' ? 'bg-black/10' : 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' }}">
                        {{ $jenisCounts['ujian'] }}
                    </span>
                </a>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.agendas.index') }}" class="relative w-full sm:w-72">
                @if (request('jenis'))
                    <input type="hidden" name="jenis" value="{{ request('jenis') }}">
                @endif
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari judul agenda atau lokasi..."
                    class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary"
                >
                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5"></i>
            </form>
        </div>
    </div>

    <!-- Agenda Table -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden">
        @if ($agendas->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-white/5 bg-slate-50/75 dark:bg-neutral-800/50 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-6">Nama Agenda Kegiatan</th>
                            <th class="py-3 px-4">Jenis</th>
                            <th class="py-3 px-4">Tanggal Pelaksanaan</th>
                            <th class="py-3 px-4">Lokasi</th>
                            <th class="py-3 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-xs">
                        @foreach ($agendas as $agenda)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-neutral-800/40 transition-colors">
                                <!-- Judul & Deskripsi -->
                                <td class="py-4 px-6 max-w-sm">
                                    <p class="font-semibold text-slate-900 dark:text-white line-clamp-1">
                                        {{ $agenda->judul }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">
                                        {{ $agenda->deskripsi ?: 'Tidak ada deskripsi detail' }}
                                    </p>
                                </td>

                                <!-- Jenis -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if ($agenda->jenis === 'akademik')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                            Akademik / PKL
                                        </span>
                                    @elseif ($agenda->jenis === 'umum')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            Umum / Job Fair
                                        </span>
                                    @elseif ($agenda->jenis === 'ujian')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                            Ujian / Seleksi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-slate-300">
                                            Libur
                                        </span>
                                    @endif
                                </td>

                                <!-- Tanggal -->
                                <td class="py-4 px-4 whitespace-nowrap text-slate-700 dark:text-slate-300">
                                    <div class="flex items-center gap-1.5 font-medium">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span>{{ $agenda->tanggal_mulai ? $agenda->tanggal_mulai->format('d M Y') : '-' }}</span>
                                    </div>
                                    @if ($agenda->tanggal_selesai && $agenda->tanggal_selesai->ne($agenda->tanggal_mulai))
                                        <div class="text-[11px] text-slate-400 ml-5 mt-0.5">
                                            s.d. {{ $agenda->tanggal_selesai->format('d M Y') }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Lokasi -->
                                <td class="py-4 px-4 whitespace-nowrap text-slate-600 dark:text-slate-400">
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span>{{ $agenda->lokasi ?: 'Aula SMKN 1 Cirebon' }}</span>
                                    </div>
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a 
                                            href="{{ route('admin.agendas.edit', $agenda) }}" 
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors"
                                            title="Edit Agenda"
                                        >
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>

                                        <form method="POST" action="{{ route('admin.agendas.destroy', $agenda) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda kegiatan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors"
                                                title="Hapus Agenda"
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
                {{ $agendas->links() }}
            </div>
        @else
            <div class="py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-neutral-800 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="calendar" class="w-8 h-8"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Belum Ada Agenda Terjadwal</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                    Jadwalkan kegiatan rekrutmen kampus, pembekalan magang, atau workshop industri untuk siswa.
                </p>
                <div class="mt-4">
                    <x-ui.button variant="primary" size="sm" href="{{ route('admin.agendas.create') }}">
                        <i data-lucide="plus" class="w-3.5 h-3.5 mr-1.5"></i>
                        Tambah Agenda Sekarang
                    </x-ui.button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
