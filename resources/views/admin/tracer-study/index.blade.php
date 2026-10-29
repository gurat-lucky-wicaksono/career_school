@extends('layouts.admin')

@section('title', 'Tracer Study & Analitik Keterserapan Alumni')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold bg-brand-primary/10 text-brand-secondary dark:text-brand-primary border border-brand-primary/20 mb-2">
                <i data-lucide="line-chart" class="w-3.5 h-3.5"></i>
                <span>Standar Penelusuran Tamatan Vokasi Kemdikbud</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Tracer Study & Keterserapan Alumni
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Monitoring keterserapan lulusan (Bekerja, Melanjutkan Kuliah, Wirausaha - BMW) dan evaluasi keselarasan kurikulum PPLG SMKN 1 Cirebon.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('alumni.index') }}"
                target="_blank"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 transition-all shadow-xs"
            >
                <i data-lucide="external-link" class="w-4 h-4"></i>
                <span>Buka Direktori Publik</span>
            </a>
            <a
                href="{{ route('tracer.create') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-primary text-slate-950 font-bold text-xs shadow-xs hover:brightness-105 transition-all"
            >
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Input Data Mandiri</span>
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs flex items-center gap-3">
            <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- KPI Summary Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Terdata -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-4 sm:p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Alumni Terdata</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-600 dark:text-slate-300">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ $totalAlumni }}</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">Siswa Lulusan Terdaftar</span>
            </div>
        </div>

        <!-- Tingkat Keterserapan BMW -->
        <div class="bg-surface-card border border-emerald-500/20 rounded-2xl p-4 sm:p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Keterserapan BMW</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $persenTerserap }}%</span>
                    <span class="text-xs text-slate-400 font-medium">({{ $terserapCount }}/{{ $totalAlumni }})</span>
                </div>
                <span class="text-[11px] text-slate-400 block mt-0.5">Bekerja, Kuliah, atau Wirausaha</span>
            </div>
        </div>

        <!-- Keselarasan Kompetensi Vokasi -->
        <div class="bg-surface-card border border-brand-primary/20 rounded-2xl p-4 sm:p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-brand-secondary dark:text-brand-primary">Keselarasan Kurikulum</span>
                <div class="w-8 h-8 rounded-lg bg-brand-primary/10 flex items-center justify-center text-brand-secondary dark:text-brand-primary">
                    <i data-lucide="check-check" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">{{ $persenSelaras }}%</span>
                    <span class="text-xs text-slate-400 font-medium">Taraf Selaras</span>
                </div>
                <span class="text-[11px] text-slate-400 block mt-0.5">Sesuai / Sangat Sesuai Jurusan</span>
            </div>
        </div>

        <!-- Masih Mencari Kerja -->
        <div class="bg-surface-card border border-amber-500/20 rounded-2xl p-4 sm:p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">Mencari Kerja</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ $mencariKerjaCount }}</span>
                    <span class="text-xs text-slate-400 font-medium">
                        ({{ $totalAlumni > 0 ? round(($mencariKerjaCount / $totalAlumni) * 100, 1) : 0 }}%)
                    </span>
                </div>
                <span class="text-[11px] text-slate-400 block mt-0.5">Prioritas Fasilitasi BKK</span>
            </div>
        </div>
    </div>

    <!-- Visual Distribution Bars -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Breakdown Status BMW -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="pie-chart" class="w-4 h-4 text-brand-secondary dark:text-brand-primary"></i>
                    <span>Distribusi Status Keterserapan (BMW)</span>
                </h3>
                <span class="text-[11px] text-slate-400">Basis Target IKU SMK</span>
            </div>

            <div class="space-y-3 text-xs">
                <!-- Bekerja -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-slate-700 dark:text-slate-300">Bekerja di Perusahaan / Industri</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">
                            {{ $bekerjaCount }} ({{ $totalAlumni > 0 ? round(($bekerjaCount / $totalAlumni) * 100, 1) : 0 }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $totalAlumni > 0 ? ($bekerjaCount / $totalAlumni) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- Kuliah -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-slate-700 dark:text-slate-300">Melanjutkan Pendidikan Tinggi (Kuliah)</span>
                        <span class="text-blue-600 dark:text-blue-400 font-bold">
                            {{ $kuliahCount }} ({{ $totalAlumni > 0 ? round(($kuliahCount / $totalAlumni) * 100, 1) : 0 }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full" style="width: {{ $totalAlumni > 0 ? ($kuliahCount / $totalAlumni) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- Wirausaha -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-slate-700 dark:text-slate-300">Wirausaha / Freelance Mandiri</span>
                        <span class="text-purple-600 dark:text-purple-400 font-bold">
                            {{ $wirausahaCount }} ({{ $totalAlumni > 0 ? round(($wirausahaCount / $totalAlumni) * 100, 1) : 0 }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                        <div class="h-full bg-purple-500 rounded-full" style="width: {{ $totalAlumni > 0 ? ($wirausahaCount / $totalAlumni) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- Mencari Kerja -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-slate-700 dark:text-slate-300">Sedang Mencari Kerja / Belum Bekerja</span>
                        <span class="text-amber-600 dark:text-amber-400 font-bold">
                            {{ $mencariKerjaCount }} ({{ $totalAlumni > 0 ? round(($mencariKerjaCount / $totalAlumni) * 100, 1) : 0 }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                        <div class="h-full bg-amber-500 rounded-full" style="width: {{ $totalAlumni > 0 ? ($mencariKerjaCount / $totalAlumni) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown Keselarasan -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="bar-chart-2" class="w-4 h-4 text-emerald-500"></i>
                    <span>Tingkat Keselarasan Kejuruan PPLG</span>
                </h3>
                <span class="text-[11px] text-slate-400">Evaluasi Link & Match</span>
            </div>

            <div class="space-y-3 text-xs">
                @php
                    $evalTotal = ($sangatSesuai + $sesuai + $kurangSesuai + $tidakSesuai) ?: 1;
                @endphp

                <!-- Sangat Sesuai -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-slate-700 dark:text-slate-300">Sangat Sesuai (Bidang IT/Software)</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">
                            {{ $sangatSesuai }} ({{ round(($sangatSesuai / $evalTotal) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ ($sangatSesuai / $evalTotal) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Sesuai -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-slate-700 dark:text-slate-300">Sesuai (Teknologi / Komputasi)</span>
                        <span class="text-teal-600 dark:text-teal-400 font-bold">
                            {{ $sesuai }} ({{ round(($sesuai / $evalTotal) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                        <div class="h-full bg-teal-500 rounded-full" style="width: {{ ($sesuai / $evalTotal) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Kurang Sesuai -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-slate-700 dark:text-slate-300">Kurang Sesuai (Administrasi / Terkait Sebagian)</span>
                        <span class="text-amber-600 dark:text-amber-400 font-bold">
                            {{ $kurangSesuai }} ({{ round(($kurangSesuai / $evalTotal) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                        <div class="h-full bg-amber-500 rounded-full" style="width: {{ ($kurangSesuai / $evalTotal) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Tidak Sesuai -->
                <div>
                    <div class="flex justify-between font-medium mb-1">
                        <span class="text-slate-700 dark:text-slate-300">Tidak Sesuai (Lintas Sektor)</span>
                        <span class="text-red-500 font-bold">
                            {{ $tidakSesuai }} ({{ round(($tidakSesuai / $evalTotal) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                        <div class="h-full bg-red-500 rounded-full" style="width: {{ ($tidakSesuai / $evalTotal) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Data Table -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-xs space-y-4">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.tracer.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2 relative">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama alumni, NISN, instansi, atau posisi..."
                    class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary"
                >
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
            </div>

            <div>
                <select
                    name="status"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary"
                >
                    <option value="all">Semua Status BMW</option>
                    <option value="bekerja" {{ request('status') === 'bekerja' ? 'selected' : '' }}>Bekerja</option>
                    <option value="kuliah" {{ request('status') === 'kuliah' ? 'selected' : '' }}>Melanjutkan Kuliah</option>
                    <option value="wirausaha" {{ request('status') === 'wirausaha' ? 'selected' : '' }}>Wirausaha</option>
                    <option value="mencari_kerja" {{ request('status') === 'mencari_kerja' ? 'selected' : '' }}>Mencari Kerja</option>
                </select>
            </div>

            <div>
                <select
                    name="tahun"
                    class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary"
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
                    class="w-full py-2 px-3 rounded-xl bg-brand-primary text-slate-950 font-bold text-xs shadow-xs hover:brightness-105 transition-all text-center"
                >
                    Filter Data
                </button>
                @if(request()->hasAny(['search', 'status', 'tahun', 'major_id']))
                    <a
                        href="{{ route('admin.tracer.index') }}"
                        class="p-2 rounded-xl border border-slate-200 dark:border-white/10 text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 transition-all"
                        title="Reset Filter"
                    >
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-400">
                <thead class="bg-slate-50 dark:bg-white/[0.03] text-slate-700 dark:text-slate-300 font-semibold uppercase text-[10px] tracking-wider border-b border-slate-200/80 dark:border-white/10">
                    <tr>
                        <th class="py-3 px-3">Alumni</th>
                        <th class="py-3 px-3">Tahun / Jurusan</th>
                        <th class="py-3 px-3">Status BMW</th>
                        <th class="py-3 px-3">Instansi & Posisi</th>
                        <th class="py-3 px-3">Keselarasan</th>
                        <th class="py-3 px-3">Visibilitas</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    @forelse($alumni as $item)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $item->user->name ?? 'Alumni' }}
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $item->user->email ?? '-' }}
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <div class="font-semibold text-slate-900 dark:text-white">
                                    Lulus {{ $item->tahun_lulus }}
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $item->major->kode ?? 'PPLG' }}
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                @if($item->status_bmw === 'bekerja')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        Bekerja
                                    </span>
                                @elseif($item->status_bmw === 'kuliah')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                                        Kuliah
                                    </span>
                                @elseif($item->status_bmw === 'wirausaha')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
                                        Wirausaha
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        Mencari Kerja
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 max-w-[200px]">
                                <div class="font-medium text-slate-800 dark:text-slate-200 truncate">
                                    {{ $item->posisi_studi ?: ($item->pekerjaan ?: '-') }}
                                </div>
                                <div class="text-[11px] text-slate-400 truncate">
                                    {{ $item->nama_instansi ?: ($item->perusahaan ?: '-') }}
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="font-medium text-slate-700 dark:text-slate-300">
                                    {{ $item->keselarasan_label }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                <form method="POST" action="{{ route('admin.tracer.toggle-visibility', $item->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold border transition-colors {{ $item->is_public ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-slate-100 text-slate-500 border-slate-200 dark:bg-white/5 dark:text-slate-400 hover:bg-slate-200' }}"
                                        title="Klik untuk mengubah visibilitas"
                                    >
                                        {{ $item->is_public ? 'Publik' : 'Privat' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3 px-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a
                                        href="{{ route('alumni.show', $item->id) }}"
                                        target="_blank"
                                        class="p-1.5 rounded-lg border border-slate-200 dark:border-white/10 hover:text-brand-secondary dark:hover:text-brand-primary transition-colors"
                                        title="Lihat Profil Publik"
                                    >
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>

                                    <form method="POST" action="{{ route('admin.tracer.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data alumni ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="p-1.5 rounded-lg border border-slate-200 dark:border-white/10 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                                            title="Hapus Data"
                                        >
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada data kuesioner tracer study alumni yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-4 border-t border-slate-100 dark:border-white/5">
            {{ $alumni->links() }}
        </div>
    </div>

</div>
@endsection
