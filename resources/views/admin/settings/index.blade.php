@extends('layouts.admin')

@section('title', 'Pengaturan Sistem & Identitas Lembaga')

@section('content')
<div class="space-y-8" x-data="{ activeTab: 'identity' }">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Pengaturan Sistem & Identitas Lembaga
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola profil resmi sekolah, struktur pengelola BKK, kop dokumen cetak, serta parameter tahun ajaran berjalan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.reports.print-summary') }}" 
                target="_blank"
                class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200/80 dark:border-white/10 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-neutral-800 transition-colors shadow-sm"
            >
                <i data-lucide="printer" class="w-4 h-4 text-slate-400"></i>
                <span>Lihat Contoh Kop Laporan</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/50 flex items-start gap-3">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5"></i>
            <div>
                <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-200">Pembaruan Berhasil</h4>
                <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-800/50 flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5"></i>
            <div>
                <h4 class="text-sm font-semibold text-rose-900 dark:text-rose-200">Gagal Menyimpan Pengaturan</h4>
                <ul class="text-xs text-rose-700 dark:text-rose-400 mt-1 list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Tab Navigation -->
    <div class="flex items-center gap-2 border-b border-slate-200/80 dark:border-white/10 pb-px overflow-x-auto">
        <button 
            type="button" 
            @click="activeTab = 'identity'"
            :class="activeTab === 'identity' ? 'border-brand-primary text-brand-primary dark:text-brand-primary font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 font-medium'"
            class="inline-flex items-center gap-2 py-3 px-4 border-b-2 text-xs transition-colors whitespace-nowrap"
        >
            <i data-lucide="building" class="w-4 h-4"></i>
            <span>Identitas Resmi Sekolah</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'bkk'"
            :class="activeTab === 'bkk' ? 'border-brand-primary text-brand-primary dark:text-brand-primary font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 font-medium'"
            class="inline-flex items-center gap-2 py-3 px-4 border-b-2 text-xs transition-colors whitespace-nowrap"
        >
            <i data-lucide="briefcase" class="w-4 h-4"></i>
            <span>Struktur &amp; Izin BKK</span>
        </button>

        <button 
            type="button" 
            @click="activeTab = 'academic'"
            :class="activeTab === 'academic' ? 'border-brand-primary text-brand-primary dark:text-brand-primary font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 font-medium'"
            class="inline-flex items-center gap-2 py-3 px-4 border-b-2 text-xs transition-colors whitespace-nowrap"
        >
            <i data-lucide="calendar" class="w-4 h-4"></i>
            <span>Tahun Ajaran &amp; Aturan Sistem</span>
        </button>
    </div>

    <!-- Settings Main Form -->
    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- TAB 1: IDENTITAS SEKOLAH -->
        <div x-show="activeTab === 'identity'" class="space-y-6">
            <x-ui.card 
                title="Identitas Pokok Sekolah" 
                subtitle="Data lembaga yang dicantumkan pada kop surat laporan BKK dan dokumen resmi siswa"
            >
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="school_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nama Sekolah / Institusi <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="school_name" 
                            id="school_name" 
                            value="{{ old('school_name', $settings['school_name']->value ?? 'SMK Negeri 1 Cirebon') }}" 
                            required
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white focus:border-brand-primary focus:ring-brand-primary/20"
                        >
                    </div>

                    <div>
                        <label for="school_npsn" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nomor Pokok Sekolah Nasional (NPSN) <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="school_npsn" 
                            id="school_npsn" 
                            value="{{ old('school_npsn', $settings['school_npsn']->value ?? '20222159') }}" 
                            required
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white font-mono focus:border-brand-primary focus:ring-brand-primary/20"
                        >
                    </div>

                    <div class="sm:col-span-2">
                        <label for="school_address" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Alamat Lengkap Kampus <span class="text-rose-500">*</span>
                        </label>
                        <textarea 
                            name="school_address" 
                            id="school_address" 
                            rows="2"
                            required
                            class="w-full p-3 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white focus:border-brand-primary focus:ring-brand-primary/20"
                        >{{ old('school_address', $settings['school_address']->value ?? 'Jl. Perjuangan, Sunyaragi, Kec. Kesambi, Kota Cirebon, Jawa Barat 45131') }}</textarea>
                    </div>

                    <div>
                        <label for="school_phone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nomor Telepon Kantor
                        </label>
                        <input 
                            type="text" 
                            name="school_phone" 
                            id="school_phone" 
                            value="{{ old('school_phone', $settings['school_phone']->value ?? '(0231) 480202') }}" 
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white focus:border-brand-primary focus:ring-brand-primary/20"
                        >
                    </div>

                    <div>
                        <label for="school_email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Alamat Email Resmi
                        </label>
                        <input 
                            type="email" 
                            name="school_email" 
                            id="school_email" 
                            value="{{ old('school_email', $settings['school_email']->value ?? 'info@smkn1-cirebon.sch.id') }}" 
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white focus:border-brand-primary focus:ring-brand-primary/20"
                        >
                    </div>

                    <div class="sm:col-span-2">
                        <label for="school_website" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Situs Web Resmi
                        </label>
                        <input 
                            type="url" 
                            name="school_website" 
                            id="school_website" 
                            value="{{ old('school_website', $settings['school_website']->value ?? 'https://smkn1-cirebon.sch.id') }}" 
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white focus:border-brand-primary focus:ring-brand-primary/20"
                        >
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card 
                title="Pimpinan Sekolah (Kepala Sekolah)" 
                subtitle="Pejabat penandatangan laporan akreditasi dan berkas resmi kemitraan industri"
            >
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="principal_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nama Lengkap Kepala Sekolah <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="principal_name" 
                            id="principal_name" 
                            value="{{ old('principal_name', $settings['principal_name']->value ?? 'Drs. H. Arifudin, M.Pd.') }}" 
                            required
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white focus:border-brand-primary focus:ring-brand-primary/20"
                        >
                    </div>

                    <div>
                        <label for="principal_nip" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            NIP Kepala Sekolah <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="principal_nip" 
                            id="principal_nip" 
                            value="{{ old('principal_nip', $settings['principal_nip']->value ?? '19680512 199303 1 008') }}" 
                            required
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white font-mono focus:border-brand-primary focus:ring-brand-primary/20"
                        >
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- TAB 2: STRUKTUR & IZIN BKK -->
        <div x-show="activeTab === 'bkk'" class="space-y-6" x-cloak>
            <x-ui.card 
                title="Bursa Kerja Khusus (BKK) Bina Mandiri" 
                subtitle="Legalitas operasional dan pejabat pelaksana layanan bursa kerja sekolah"
            >
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label for="bkk_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nama Unit Pengelola BKK <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="bkk_name" 
                            id="bkk_name" 
                            value="{{ old('bkk_name', $settings['bkk_name']->value ?? 'Bursa Kerja Khusus (BKK) SMK Negeri 1 Cirebon') }}" 
                            required
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white focus:border-brand-primary focus:ring-brand-primary/20"
                        >
                    </div>

                    <div>
                        <label for="bkk_leader_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nama Ketua / Koordinator BKK <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="bkk_leader_name" 
                            id="bkk_leader_name" 
                            value="{{ old('bkk_leader_name', $settings['bkk_leader_name']->value ?? 'Bambang Sudarsono, S.Kom.') }}" 
                            required
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white focus:border-brand-primary focus:ring-brand-primary/20"
                        >
                    </div>

                    <div>
                        <label for="bkk_leader_nip" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            NIP Ketua BKK <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="bkk_leader_nip" 
                            id="bkk_leader_nip" 
                            value="{{ old('bkk_leader_nip', $settings['bkk_leader_nip']->value ?? '19820719 200902 1 003') }}" 
                            required
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white font-mono focus:border-brand-primary focus:ring-brand-primary/20"
                        >
                    </div>

                    <div>
                        <label for="bkk_sk_number" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Nomor SK Izin Operasional BKK Disnaker
                        </label>
                        <input 
                            type="text" 
                            name="bkk_sk_number" 
                            id="bkk_sk_number" 
                            value="{{ old('bkk_sk_number', $settings['bkk_sk_number']->value ?? '560/042/BKK-Disnaker/2023') }}" 
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white focus:border-brand-primary focus:ring-brand-primary/20"
                        >
                    </div>

                    <div>
                        <label for="bkk_contact_phone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Hotline / WhatsApp Layanan BKK
                        </label>
                        <input 
                            type="text" 
                            name="bkk_contact_phone" 
                            id="bkk_contact_phone" 
                            value="{{ old('bkk_contact_phone', $settings['bkk_contact_phone']->value ?? '0812-3456-7890') }}" 
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white focus:border-brand-primary focus:ring-brand-primary/20"
                        >
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- TAB 3: TAHUN AJARAN & ATURAN SISTEM -->
        <div x-show="activeTab === 'academic'" class="space-y-6" x-cloak>
            <x-ui.card 
                title="Periode Kalender Pendidikan Aktif" 
                subtitle="Acuan tahun ajaran berjalan untuk pendaftaran magang dan rekapitulasi data"
            >
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="active_academic_year" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tahun Ajaran Berjalan <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="active_academic_year" 
                            id="active_academic_year" 
                            value="{{ old('active_academic_year', $settings['active_academic_year']->value ?? '2025/2026') }}" 
                            placeholder="Contoh: 2025/2026"
                            required
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white font-mono focus:border-brand-primary focus:ring-brand-primary/20"
                        >
                    </div>

                    <div>
                        <label for="active_semester" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Semester Akademik Aktif <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            name="active_semester" 
                            id="active_semester" 
                            required
                            class="w-full h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white focus:border-brand-primary focus:ring-brand-primary/20"
                        >
                            <option value="Ganjil" {{ old('active_semester', $settings['active_semester']->value ?? 'Ganjil') === 'Ganjil' ? 'selected' : '' }}>
                                Semester Ganjil (Gasal)
                            </option>
                            <option value="Genap" {{ old('active_semester', $settings['active_semester']->value ?? 'Ganjil') === 'Genap' ? 'selected' : '' }}>
                                Semester Genap
                            </option>
                        </select>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card 
                title="Aturan Operasional &amp; Pembatasan Sistem" 
                subtitle="Kontrol kuota pendaftaran lowongan dan akses pendaftaran akun mandiri"
            >
                <div class="space-y-5">
                    <div>
                        <label for="max_active_applications_per_student" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Batas Maksimal Lamaran Aktif per Siswa <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-3 max-w-xs">
                            <input 
                                type="number" 
                                name="max_active_applications_per_student" 
                                id="max_active_applications_per_student" 
                                min="1" 
                                max="20" 
                                value="{{ old('max_active_applications_per_student', $settings['max_active_applications_per_student']->value ?? '3') }}" 
                                required
                                class="w-24 h-10 px-3.5 text-xs rounded-xl bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-900 dark:text-white font-mono focus:border-brand-primary focus:ring-brand-primary/20 text-center"
                            >
                            <span class="text-xs text-slate-500 dark:text-slate-400">lowongan bersamaan</span>
                        </div>
                        <p class="text-[11px] text-slate-400 dark:text-neutral-500 mt-1">
                            Siswa tidak dapat melamar ke lowongan baru jika jumlah lamaran dengan status tertunda/ditinjau/wawancara telah mencapai batas ini.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-white/5">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="allow_student_registration" 
                                value="1" 
                                {{ old('allow_student_registration', $settings['allow_student_registration']->value ?? '1') == '1' ? 'checked' : '' }}
                                class="mt-0.5 rounded border-slate-300 dark:border-white/10 text-brand-primary focus:ring-brand-primary/20"
                            >
                            <div>
                                <span class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                                    Buka Pendaftaran Akun Siswa Mandiri
                                </span>
                                <span class="block text-[11px] text-slate-500 dark:text-slate-400">
                                    Izinkan calon siswa atau siswa baru membuat akun secara mandiri melalui form registrasi portal.
                                </span>
                            </div>
                        </label>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Sticky Form Action Bar -->
        <div class="sticky bottom-4 z-20 p-4 rounded-2xl bg-surface-card/90 backdrop-blur-md border border-slate-200/80 dark:border-white/10 shadow-lg flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <i data-lucide="info" class="w-4 h-4 text-slate-400"></i>
                <span>Perubahan konfigurasi akan langsung mempengaruhi kop surat dan seluruh modul terkait.</span>
            </div>
            <div class="flex items-center gap-3">
                <button 
                    type="submit" 
                    class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-neutral-900 text-white dark:bg-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-slate-100 transition-colors shadow-sm inline-flex items-center gap-2"
                >
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Seluruh Pengaturan</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
