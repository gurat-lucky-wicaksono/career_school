@extends('layouts.admin')

@section('title', 'Pengaturan Sistem & Profil Sekolah')

@section('content')
<div class="space-y-8">
    <!-- Header Title -->
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
            Pengaturan Sistem & Identitas Sekolah
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Konfigurasi parameter operasional tahun ajaran, profil institusi, dan preferensi portal Career School SMKN 1 Cirebon.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Left: School Profile Form Card -->
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card 
                title="Identitas Resmi Sekolah" 
                subtitle="Data pokok institusi yang tampil pada kop laporan dan portal publik"
            >
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Institusi</label>
                            <input 
                                type="text" 
                                value="SMK Negeri 1 Cirebon" 
                                readonly
                                class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800 border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white font-medium"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">NPSN Sekolah</label>
                            <input 
                                type="text" 
                                value="20222159" 
                                readonly
                                class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800 border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white font-mono"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Alamat Kampus</label>
                        <input 
                            type="text" 
                            value="Jl. Perjuangan, Sunyaragi, Kec. Kesambi, Kota Cirebon, Jawa Barat 45131" 
                            readonly
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800 border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white"
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email Resmi</label>
                            <input 
                                type="email" 
                                value="info@smkn1-cirebon.sch.id" 
                                readonly
                                class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800 border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nomor Telepon</label>
                            <input 
                                type="text" 
                                value="(0231) 480202" 
                                readonly
                                class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800 border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white"
                            >
                        </div>
                    </div>

                    <div class="pt-3 flex justify-end">
                        <x-ui.button variant="primary" onclick="demoSaveSettings()">
                            Simpan Perubahan
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Right: Academic Year & System Status -->
        <div class="space-y-6">
            <x-ui.card 
                title="Tahun Ajaran Aktif" 
                subtitle="Konfigurasi periode akademik yang sedang berjalan"
            >
                <div class="space-y-4">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-neutral-800 border border-slate-200/80 dark:border-white/10 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Tahun Ajaran:</span>
                            <span class="font-bold text-xs text-slate-900 dark:text-white font-mono">2025/2026</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Semester:</span>
                            <x-ui.badge variant="success">Ganjil (Aktif)</x-ui.badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500 dark:text-slate-400">Status Sistem:</span>
                            <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Online & Beroperasi</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Ganti Periode Tahun Ajaran</label>
                        <select class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white">
                            <option selected>2025/2026 - Semester Ganjil</option>
                            <option>2025/2026 - Semester Genap</option>
                            <option>2026/2027 - Semester Ganjil</option>
                        </select>
                    </div>

                    <div class="pt-2">
                        <x-ui.button variant="secondary" class="w-full justify-center" onclick="demoUpdatePeriod()">
                            Perbarui Tahun Ajaran
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card 
                title="Informasi Lingkungan Sistem" 
                subtitle="Status server dan versi rilis Career School"
            >
                <div class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 font-mono">
                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-white/5">
                        <span>Framework:</span>
                        <span class="font-bold text-slate-900 dark:text-white">Laravel v{{ app()->version() }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-white/5">
                        <span>PHP Version:</span>
                        <span class="font-bold text-slate-900 dark:text-white">PHP {{ PHP_VERSION }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span>Versi Career School:</span>
                        <span class="font-bold text-brand-secondary dark:text-brand-primary">v1.0.0 Stable</span>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function demoSaveSettings() {
        if (window.notifyAlert) window.notifyAlert({ title: 'Pengaturan Disimpan', text: 'Perubahan identitas sekolah berhasil diperbarui.', icon: 'success' });
    }
    function demoUpdatePeriod() {
        if (window.notifyAlert) window.notifyAlert({ title: 'Tahun Ajaran Diperbarui', text: 'Sistem aktif berpindah ke periode yang dipilih.', icon: 'info' });
    }
</script>
@endpush
@endsection
