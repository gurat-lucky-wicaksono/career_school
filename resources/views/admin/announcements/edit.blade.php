@extends('layouts.admin')

@section('title', 'Edit Pengumuman BKK')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Edit Pengumuman BKK
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Perbarui rincian informasi pengumuman, rentang tanggal tayang, atau ganti berkas lampiran.
            </p>
        </div>

        <x-ui.button variant="secondary" size="md" href="{{ route('admin.announcements.index') }}">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Kembali
        </x-ui.button>
    </div>

    <!-- Form Card -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Judul Pengumuman -->
            <div class="space-y-1.5">
                <label for="judul" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                    Judul Pengumuman <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    name="judul"
                    id="judul"
                    value="{{ old('judul', $announcement->judul) }}"
                    required
                    maxlength="150"
                    placeholder="Contoh: Pembukaan Rekrutmen Bersama PT Telkom Akses Batch II"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                >
                @error('judul')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Prioritas & Status Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="prioritas" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                        Tingkat Prioritas <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="prioritas"
                        id="prioritas"
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                    >
                        <option value="normal" {{ old('prioritas', $announcement->prioritas) === 'normal' ? 'selected' : '' }}>Normal (Informasi Reguler)</option>
                        <option value="penting" {{ old('prioritas', $announcement->prioritas) === 'penting' ? 'selected' : '' }}>Penting (Wajib Diperhatikan Siswa)</option>
                        <option value="rendah" {{ old('prioritas', $announcement->prioritas) === 'rendah' ? 'selected' : '' }}>Rendah (Arsip Tambahan)</option>
                    </select>
                    @error('prioritas')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="status" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                        Status Publikasi <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="status"
                        id="status"
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                    >
                        <option value="terbit" {{ old('status', $announcement->status) === 'terbit' ? 'selected' : '' }}>Langsung Terbitkan (Publik)</option>
                        <option value="draft" {{ old('status', $announcement->status) === 'draft' ? 'selected' : '' }}>Simpan Sebagai Draft</option>
                        <option value="arsip" {{ old('status', $announcement->status) === 'arsip' ? 'selected' : '' }}>Arsipkan</option>
                    </select>
                    @error('status')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Rentang Tanggal Tayang -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="mulai_tampil" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                        Mulai Ditampilkan
                    </label>
                    <input
                        type="datetime-local"
                        name="mulai_tampil"
                        id="mulai_tampil"
                        value="{{ old('mulai_tampil', $announcement->mulai_tampil ? $announcement->mulai_tampil->format('Y-m-d\TH:i') : '') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                    >
                    @error('mulai_tampil')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="selesai_tampil" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                        Selesai Ditampilkan
                    </label>
                    <input
                        type="datetime-local"
                        name="selesai_tampil"
                        id="selesai_tampil"
                        value="{{ old('selesai_tampil', $announcement->selesai_tampil ? $announcement->selesai_tampil->format('Y-m-d\TH:i') : '') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                    >
                    @error('selesai_tampil')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Isi Pengumuman -->
            <div class="space-y-1.5">
                <label for="isi" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                    Isi Pengumuman Lengkap <span class="text-rose-500">*</span>
                </label>
                <textarea
                    name="isi"
                    id="isi"
                    rows="8"
                    required
                    placeholder="Tuliskan isi pengumuman, kriteria, alur pendaftaran, dan rincian kontak secara detail..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                >{{ old('isi', $announcement->isi) }}</textarea>
                @error('isi')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lampiran Berkas -->
            <div class="space-y-1.5">
                <label for="lampiran" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                    Unggah Berkas Lampiran Baru (Opsional)
                </label>
                @if ($announcement->lampiran)
                    <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-100 dark:bg-neutral-800 text-xs text-slate-700 dark:text-slate-300 mb-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-emerald-500"></i>
                        <span>Berkas saat ini: <strong>{{ basename($announcement->lampiran) }}</strong></span>
                        <a href="{{ asset('storage/' . $announcement->lampiran) }}" target="_blank" class="ml-auto text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                            Lihat Berkas
                        </a>
                    </div>
                @endif
                <input
                    type="file"
                    name="lampiran"
                    id="lampiran"
                    accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-200 dark:file:bg-neutral-700 file:text-slate-800 dark:file:text-white hover:file:bg-slate-300"
                >
                <span class="text-[11px] text-slate-400 block">Kosongkan jika tidak ingin mengubah berkas lampiran. Format didukung: PDF, PNG, JPG (Maks. 5 MB)</span>
                @error('lampiran')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-end gap-3">
                <x-ui.button variant="secondary" size="md" href="{{ route('admin.announcements.index') }}">
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
