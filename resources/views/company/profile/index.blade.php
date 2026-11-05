@extends('layouts.company')

@section('title', 'Profil Perusahaan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-heading font-bold text-text-primary tracking-tight">
                Profil perusahaan mitra
            </h1>
            <p class="text-sm text-text-secondary mt-1">
                Perbarui identitas profil perusahaan, alamat operasional, dan kontak resmi HRD.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border {{ $company->isVerified() ? 'border-state-success-border bg-state-success-bg text-state-success-text' : 'border-state-warning-border bg-state-warning-bg text-state-warning-text' }} text-xs font-semibold">
                <span class="w-1.5 h-1.5 rounded-full {{ $company->isVerified() ? 'bg-state-success-text' : 'bg-state-warning-text' }}"></span>
                <span>{{ $company->isVerified() ? 'Mitra terverifikasi' : 'Menunggu verifikasi BKK' }}</span>
            </span>
        </div>
    </div>

    <!-- Form Card -->
    <form method="POST" action="{{ route('company.profile.update') }}" enctype="multipart/form-data" class="bg-surface-card border border-border-subtle rounded-card p-6 shadow-xs space-y-6">
        @csrf
        @method('PUT')

        <!-- Identitas Perusahaan -->
        <div class="space-y-4">
            <h2 class="text-xs font-heading font-bold text-text-primary uppercase tracking-wider pb-2 border-b border-border-subtle">
                Identitas badan usaha
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Nama Perusahaan -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-text-secondary mb-1.5">
                        Nama perusahaan / instansi <span class="text-state-danger-text">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        value="{{ old('nama', $company->nama) }}" 
                        required
                        class="w-full h-10 px-3.5 text-xs rounded-btn bg-surface-bg border border-border-subtle text-text-primary placeholder:text-text-muted focus:outline-none focus:ring-1 focus:ring-brand-primary"
                    >
                    @error('nama')
                        <p class="text-state-danger-text text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bidang Usaha -->
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1.5">
                        Bidang industri / sektor
                    </label>
                    <input 
                        type="text" 
                        name="bidang" 
                        value="{{ old('bidang', $company->bidang) }}" 
                        placeholder="Contoh: Teknologi Informasi & Software, Manufaktur Otomotif"
                        class="w-full h-10 px-3.5 text-xs rounded-btn bg-surface-bg border border-border-subtle text-text-primary placeholder:text-text-muted focus:outline-none focus:ring-1 focus:ring-brand-primary"
                    >
                </div>

                <!-- Kota -->
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1.5">
                        Kota / domisili
                    </label>
                    <input 
                        type="text" 
                        name="kota" 
                        value="{{ old('kota', $company->kota) }}" 
                        placeholder="Contoh: Kota Cirebon"
                        class="w-full h-10 px-3.5 text-xs rounded-btn bg-surface-bg border border-border-subtle text-text-primary placeholder:text-text-muted focus:outline-none focus:ring-1 focus:ring-brand-primary"
                    >
                </div>

                <!-- Website -->
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1.5">
                        Situs web resmi
                    </label>
                    <input 
                        type="url" 
                        name="situs" 
                        value="{{ old('situs', $company->situs) }}" 
                        placeholder="https://perusahaan.co.id"
                        class="w-full h-10 px-3.5 text-xs rounded-btn bg-surface-bg border border-border-subtle text-text-primary placeholder:text-text-muted focus:outline-none focus:ring-1 focus:ring-brand-primary"
                    >
                </div>

                <!-- Nomor Telepon -->
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1.5">
                        Nomor telepon / kontak HRD
                    </label>
                    <input 
                        type="text" 
                        name="telepon" 
                        value="{{ old('telepon', $user->profile?->telepon) }}" 
                        placeholder="Contoh: 081234567890 / 0231-123456"
                        class="w-full h-10 px-3.5 text-xs rounded-btn bg-surface-bg border border-border-subtle text-text-primary placeholder:text-text-muted focus:outline-none focus:ring-1 focus:ring-brand-primary"
                    >
                </div>

                <!-- Alamat Lengkap -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-text-secondary mb-1.5">
                        Alamat kantor operasional
                    </label>
                    <textarea 
                        name="alamat" 
                        rows="3"
                        placeholder="Jl. Raya Sunan Gunung Jati No. 12, Cirebon..."
                        class="w-full p-3.5 text-xs rounded-btn bg-surface-bg border border-border-subtle text-text-primary placeholder:text-text-muted focus:outline-none focus:ring-1 focus:ring-brand-primary resize-none"
                    >{{ old('alamat', $company->alamat) }}</textarea>
                </div>

                <!-- Tentang Perusahaan -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-text-secondary mb-1.5">
                        Tentang perusahaan / profil singkat
                    </label>
                    <textarea 
                        name="deskripsi" 
                        rows="4"
                        placeholder="Jelaskan bidang bisnis, visi, budaya kerja, atau teknologi yang dikembangkan perusahaan..."
                        class="w-full p-3.5 text-xs rounded-btn bg-surface-bg border border-border-subtle text-text-primary placeholder:text-text-muted focus:outline-none focus:ring-1 focus:ring-brand-primary resize-none"
                    >{{ old('deskripsi', $company->deskripsi) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-border-subtle">
            <x-ui.button 
                variant="primary" 
                type="submit"
            >
                Simpan perubahan profil
            </x-ui.button>
        </div>
    </form>
</div>
@endsection
