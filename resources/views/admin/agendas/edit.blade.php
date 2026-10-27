@extends('layouts.admin')

@section('title', 'Edit Agenda Kegiatan BKK')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Edit Agenda Kegiatan BKK
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Perbarui detail jadwal, lokasi, atau keterangan pelaksanaan agenda kegiatan.
            </p>
        </div>

        <x-ui.button variant="secondary" size="md" href="{{ route('admin.agendas.index') }}">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Kembali
        </x-ui.button>
    </div>

    <!-- Form Card -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.agendas.update', $agenda) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Judul Agenda -->
            <div class="space-y-1.5">
                <label for="judul" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                    Nama Kegiatan / Agenda <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="judul"
                    id="judul"
                    value="{{ old('judul', $agenda->judul) }}"
                    required
                    maxlength="150"
                    placeholder="Contoh: Pembekalan Teknis & Etika Industri PKL Angkatan 2024"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                >
                @error('judul')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jenis Agenda & Lokasi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="jenis" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                        Jenis Agenda <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="jenis"
                        id="jenis"
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                    >
                        <option value="akademik" {{ old('jenis', $agenda->jenis) === 'akademik' ? 'selected' : '' }}>Akademik / Persiapan PKL</option>
                        <option value="umum" {{ old('jenis', $agenda->jenis) === 'umum' ? 'selected' : '' }}>Umum / Job Fair & Rekrutmen</option>
                        <option value="ujian" {{ old('jenis', $agenda->jenis) === 'ujian' ? 'selected' : '' }}>Uji Kompetensi / Seleksi DUDI</option>
                        <option value="libur" {{ old('jenis', $agenda->jenis) === 'libur' ? 'selected' : '' }}>Libur / Penutupan</option>
                    </select>
                    @error('jenis')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="lokasi" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                        Lokasi Pelaksanaan
                    </label>
                    <input
                        type="text"
                        name="lokasi"
                        id="lokasi"
                        value="{{ old('lokasi', $agenda->lokasi) }}"
                        placeholder="Contoh: Aula SMKN 1 Cirebon / Lab RPL"
                        maxlength="120"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                    >
                    @error('lokasi')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tanggal Pelaksanaan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="tanggal_mulai" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                        Tanggal Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="tanggal_mulai"
                        id="tanggal_mulai"
                        value="{{ old('tanggal_mulai', $agenda->tanggal_mulai ? $agenda->tanggal_mulai->format('Y-m-d') : '') }}"
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                    >
                    @error('tanggal_mulai')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="tanggal_selesai" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                        Tanggal Selesai (Opsional)
                    </label>
                    <input
                        type="date"
                        name="tanggal_selesai"
                        id="tanggal_selesai"
                        value="{{ old('tanggal_selesai', $agenda->tanggal_selesai ? $agenda->tanggal_selesai->format('Y-m-d') : '') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                    >
                    @error('tanggal_selesai')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi Agenda -->
            <div class="space-y-1.5">
                <label for="deskripsi" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                    Deskripsi & Keterangan Agenda
                </label>
                <textarea
                    name="deskripsi"
                    id="deskripsi"
                    rows="4"
                    placeholder="Tuliskan sasaran peserta, instruksi pakaian, syarat berkas yang harus dibawa, dsb..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                >{{ old('deskripsi', $agenda->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-end gap-3">
                <x-ui.button variant="secondary" size="md" href="{{ route('admin.agendas.index') }}">
                    Batal
                </x-ui.button>
                <x-ui.button variant="primary" size="md" type="submit">
                    <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                    Simpan Perubahan
                </x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection
