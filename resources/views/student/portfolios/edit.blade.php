@extends('layouts.student')

@section('title', 'Edit Portofolio - ' . $portfolio->judul)

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <x-ui.button 
            variant="secondary" 
            size="sm" 
            href="{{ route('student.portfolios.index') }}"
        >
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i>
            Kembali ke Portofolio
        </x-ui.button>
    </div>

    <!-- Form Card -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-6">
        <div class="pb-4 border-b border-slate-100 dark:border-white/5">
            <h1 class="text-xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Edit Portofolio Karya
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Perbarui informasi proyek, tautan repositori, atau berkas lampiran pendukung.
            </p>
        </div>

        <form method="POST" action="{{ route('student.portfolios.update', $portfolio) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Judul Proyek -->
            <div class="space-y-1.5">
                <label for="judul" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                    Judul Proyek / Karya <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="judul" 
                    id="judul" 
                    value="{{ old('judul', $portfolio->judul) }}" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                >
            </div>

            <!-- Kategori Proyek -->
            <div class="space-y-1.5">
                <label for="kategori" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                    Kategori Bidang <span class="text-rose-500">*</span>
                </label>
                @php
                    $currentCategory = old('kategori', $portfolio->kategori);
                @endphp
                <select 
                    name="kategori" 
                    id="kategori" 
                    required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                >
                    <option value="Web Development" {{ $currentCategory === 'Web Development' ? 'selected' : '' }}>Web Development</option>
                    <option value="Mobile App" {{ $currentCategory === 'Mobile App' ? 'selected' : '' }}>Mobile Application</option>
                    <option value="UI/UX Design" {{ $currentCategory === 'UI/UX Design' ? 'selected' : '' }}>UI/UX Design & Prototyping</option>
                    <option value="Database & Backend" {{ $currentCategory === 'Database & Backend' ? 'selected' : '' }}>Database & Backend API</option>
                    <option value="Game Development" {{ $currentCategory === 'Game Development' ? 'selected' : '' }}>Game Development</option>
                    <option value="IoT & Hardware" {{ $currentCategory === 'IoT & Hardware' ? 'selected' : '' }}>IoT & Embedded Systems</option>
                    <option value="Jaringan & Keamanan" {{ $currentCategory === 'Jaringan & Keamanan' ? 'selected' : '' }}>Jaringan & Cyber Security</option>
                    <option value="Lainnya" {{ $currentCategory === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <!-- Deskripsi Proyek -->
            <div class="space-y-1.5">
                <label for="deskripsi" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                    Deskripsi Ringkas Proyek
                </label>
                <textarea 
                    name="deskripsi" 
                    id="deskripsi" 
                    rows="4" 
                    maxlength="2000"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all resize-none"
                >{{ old('deskripsi', $portfolio->deskripsi) }}</textarea>
            </div>

            <!-- Tautan Proyek (GitHub / Demo) -->
            <div class="space-y-1.5">
                <label for="tautan" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                    Tautan Proyek / Repositori (Opsional)
                </label>
                <div class="relative">
                    <i data-lucide="link" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input 
                        type="url" 
                        name="tautan" 
                        id="tautan" 
                        value="{{ old('tautan', $portfolio->tautan) }}" 
                        placeholder="https://github.com/username/project" 
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                    >
                </div>
            </div>

            <!-- Unggah Lampiran -->
            <div class="space-y-1.5">
                <label for="lampiran" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                    Ganti Berkas Lampiran (Opsional)
                </label>
                @if ($portfolio->lampiran)
                    <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-neutral-800/50 border border-slate-200/60 dark:border-white/5 flex items-center justify-between text-xs mb-2">
                        <span class="text-slate-600 dark:text-slate-300 truncate max-w-sm">
                            Berkas terpasang: {{ basename($portfolio->lampiran) }}
                        </span>
                        <a href="{{ asset('storage/' . $portfolio->lampiran) }}" target="_blank" class="text-brand-primary font-semibold hover:underline">
                            Lihat Berkas
                        </a>
                    </div>
                @endif
                <input 
                    type="file" 
                    name="lampiran" 
                    id="lampiran" 
                    accept=".pdf,.jpg,.jpeg,.png,.zip" 
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-primary/10 file:text-brand-primary hover:file:bg-brand-primary/20 outline-none transition-all cursor-pointer"
                >
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-end gap-3">
                <x-ui.button variant="secondary" size="md" href="{{ route('student.portfolios.index') }}">
                    Batal
                </x-ui.button>
                <x-ui.button variant="primary" size="md" type="submit">
                    <i data-lucide="check" class="w-4 h-4 mr-1.5"></i>
                    Simpan Perubahan
                </x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection
