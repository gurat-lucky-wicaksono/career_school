@extends('layouts.company')

@section('title', 'Profil Perusahaan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Profil Perusahaan Mitra
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Perbarui identitas profil perusahaan, alamat operasional, dan kontak resmi HRD.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border {{ $company->isVerified() ? 'border-emerald-200/80 bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300' : 'border-amber-200/80 bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300' }} text-xs font-semibold">
                <span class="w-1.5 h-1.5 rounded-full {{ $company->isVerified() ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                <span>{{ $company->isVerified() ? 'Mitra Terverifikasi' : 'Menunggu Verifikasi BKK' }}</span>
            </span>
        </div>
    </div>

    <!-- Form Card -->
    <form method="POST" action="{{ route('company.profile.update') }}" enctype="multipart/form-data" class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <!-- Identitas Perusahaan -->
        <div class="space-y-4">
            <h2 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-2 border-b border-slate-100 dark:border-white/5">
                Identitas Badan Usaha
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Nama Perusahaan -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Nama Perusahaan / Instansi <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        value="{{ old('nama', $company->nama) }}" 
                        required
                        class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >
                    @error('nama')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bidang Usaha -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Bidang Industri / Sektor
                    </label>
                    <input 
                        type="text" 
                        name="bidang" 
                        value="{{ old('bidang', $company->bidang) }}" 
                        placeholder="Contoh: Teknologi Informasi & Software, Manufaktur Otomotif"
                        class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >
                </div>

                <!-- Kota -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Kota / Domisili
                    </label>
                    <input 
                        type="text" 
                        name="kota" 
                        value="{{ old('kota', $company->kota) }}" 
                        placeholder="Contoh: Kota Cirebon"
                        class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >
                </div>

                <!-- Website -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Situs Web Resmi
                    </label>
                    <input 
                        type="url" 
                        name="situs" 
                        value="{{ old('situs', $company->situs) }}" 
                        placeholder="https://perusahaan.co.id"
                        class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >
                </div>

                <!-- Nomor Telepon -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Nomor Telepon / Kontak HRD
                    </label>
                    <input 
                        type="text" 
                        name="telepon" 
                        value="{{ old('telepon', $user->profile?->telepon) }}" 
                        placeholder="Contoh: 081234567890 / 0231-123456"
                        class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >
                </div>

                <!-- Alamat Lengkap -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Alamat Kantor Operasional
                    </label>
                    <textarea 
                        name="alamat" 
                        rows="3"
                        placeholder="Jl. Raya Sunan Gunung Jati No. 12, Cirebon..."
                        class="w-full p-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >{{ old('alamat', $company->alamat) }}</textarea>
                </div>

                <!-- Tentang Perusahaan -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Tentang Perusahaan / Profil Singkat
                    </label>
                    <textarea 
                        name="deskripsi" 
                        rows="4"
                        placeholder="Jelaskan bidang bisnis, visi, budaya kerja, atau teknologi yang dikembangkan perusahaan..."
                        class="w-full p-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >{{ old('deskripsi', $company->deskripsi) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-white/5">
            <x-ui.button 
                variant="primary" 
                type="submit"
            >
                Simpan Perubahan Profil
            </x-ui.button>
        </div>
    </form>
</div>
@endsection
