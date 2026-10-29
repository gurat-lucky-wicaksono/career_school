@extends('layouts.landing')

@section('title', 'Kuesioner Tracer Study Alumni SMKN 1 Cirebon')

@section('content')
<div class="py-12 sm:py-16 bg-surface-bg min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Navigation Link -->
        <div class="flex items-center justify-between">
            <a
                href="{{ route('alumni.index') }}"
                class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors"
            >
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali ke Direktori Alumni</span>
            </a>
            <span class="text-xs text-slate-400">
                Format Resmi BKK SMKN 1 Cirebon
            </span>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-xs sm:text-sm flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-3xl p-6 sm:p-10 shadow-sm space-y-8"
             x-data="{
                 status: '{{ old('status_bmw', $alumni->status_bmw ?? 'bekerja') }}'
             }">

            <!-- Title & Brief -->
            <div class="space-y-2 border-b border-slate-100 dark:border-white/5 pb-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-primary/10 text-brand-secondary dark:text-brand-primary border border-brand-primary/20">
                    <i data-lucide="clipboard-list" class="w-3.5 h-3.5"></i>
                    <span>Instrumen Penelusuran Tamatan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Kuesioner Tracer Study Vokasi
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    Mohon isi data keterserapan Anda setelah menyelesaikan pendidikan di SMK Negeri 1 Cirebon. Informasi ini dijamin kerahasiaannya dan digunakan untuk evaluasi serta peningkatan mutu lulusan.
                </p>
            </div>

            <form method="POST" action="{{ route('tracer.store') }}" class="space-y-6">
                @csrf

                <!-- Section: Data Akademik -->
                <div class="space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        1. Informasi Kelulusan Sekolah
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Program / Konsentrasi Keahlian <span class="text-red-500">*</span>
                            </label>
                            <select
                                name="major_id"
                                required
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary"
                            >
                                <option value="">-- Pilih Konsentrasi Keahlian --</option>
                                @foreach($majors as $m)
                                    <option value="{{ $m->id }}" {{ old('major_id', $alumni->major_id) == $m->id ? 'selected' : '' }}>
                                        {{ $m->nama }} ({{ $m->kode }})
                                    </option>
                                @endforeach
                            </select>
                            @error('major_id')
                                <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Tahun Kelulusan <span class="text-red-500">*</span>
                            </label>
                            <select
                                name="tahun_lulus"
                                required
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary"
                            >
                                <option value="">-- Pilih Tahun Lulus --</option>
                                @foreach($years as $yr)
                                    <option value="{{ $yr }}" {{ old('tahun_lulus', $alumni->tahun_lulus ?? date('Y')) == $yr ? 'selected' : '' }}>
                                        Tahun {{ $yr }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tahun_lulus')
                                <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Status BMW -->
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-white/5">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        2. Status Keterserapan Saat Ini (BMW) <span class="text-red-500">*</span>
                    </h2>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <!-- Bekerja -->
                        <label
                            class="relative flex flex-col items-center justify-center p-4 rounded-2xl border cursor-pointer transition-all text-center space-y-2"
                            :class="status === 'bekerja' ? 'bg-emerald-500/10 border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold shadow-xs' : 'bg-slate-50 dark:bg-white/[0.02] border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:border-slate-300'"
                        >
                            <input type="radio" name="status_bmw" value="bekerja" x-model="status" class="sr-only">
                            <i data-lucide="briefcase" class="w-5 h-5"></i>
                            <span class="text-xs">Bekerja</span>
                        </label>

                        <!-- Kuliah -->
                        <label
                            class="relative flex flex-col items-center justify-center p-4 rounded-2xl border cursor-pointer transition-all text-center space-y-2"
                            :class="status === 'kuliah' ? 'bg-blue-500/10 border-blue-500 text-blue-600 dark:text-blue-400 font-bold shadow-xs' : 'bg-slate-50 dark:bg-white/[0.02] border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:border-slate-300'"
                        >
                            <input type="radio" name="status_bmw" value="kuliah" x-model="status" class="sr-only">
                            <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                            <span class="text-xs">Lanjut Kuliah</span>
                        </label>

                        <!-- Wirausaha -->
                        <label
                            class="relative flex flex-col items-center justify-center p-4 rounded-2xl border cursor-pointer transition-all text-center space-y-2"
                            :class="status === 'wirausaha' ? 'bg-purple-500/10 border-purple-500 text-purple-600 dark:text-purple-400 font-bold shadow-xs' : 'bg-slate-50 dark:bg-white/[0.02] border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:border-slate-300'"
                        >
                            <input type="radio" name="status_bmw" value="wirausaha" x-model="status" class="sr-only">
                            <i data-lucide="rocket" class="w-5 h-5"></i>
                            <span class="text-xs">Wirausaha</span>
                        </label>

                        <!-- Mencari Kerja -->
                        <label
                            class="relative flex flex-col items-center justify-center p-4 rounded-2xl border cursor-pointer transition-all text-center space-y-2"
                            :class="status === 'mencari_kerja' ? 'bg-amber-500/10 border-amber-500 text-amber-600 dark:text-amber-400 font-bold shadow-xs' : 'bg-slate-50 dark:bg-white/[0.02] border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:border-slate-300'"
                        >
                            <input type="radio" name="status_bmw" value="mencari_kerja" x-model="status" class="sr-only">
                            <i data-lucide="search" class="w-5 h-5"></i>
                            <span class="text-xs">Mencari Kerja</span>
                        </label>
                    </div>
                </div>

                <!-- Section: Detail Instansi / Usaha / Kuliah -->
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-white/5" x-show="status !== 'mencari_kerja'">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        3. Rincian Tempat & Aktivitas Saat Ini
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                <span x-show="status === 'bekerja'">Nama Perusahaan / Instansi Kerja</span>
                                <span x-show="status === 'kuliah'">Nama Perguruan Tinggi / Universitas</span>
                                <span x-show="status === 'wirausaha'">Nama Usaha / Brand / Usaha Mandiri</span>
                            </label>
                            <input
                                type="text"
                                name="nama_instansi"
                                value="{{ old('nama_instansi', $alumni->nama_instansi ?? $alumni->perusahaan) }}"
                                placeholder="Contoh: PT Telkom Akses / ITB / Grage Digital"
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary"
                            >
                            @error('nama_instansi')
                                <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                <span x-show="status === 'bekerja'">Posisi / Jabatan Pekerjaan</span>
                                <span x-show="status === 'kuliah'">Program Studi / Jurusan Kuliah</span>
                                <span x-show="status === 'wirausaha'">Bidang Jasa / Produk Usaha</span>
                            </label>
                            <input
                                type="text"
                                name="posisi_studi"
                                value="{{ old('posisi_studi', $alumni->posisi_studi ?? $alumni->pekerjaan) }}"
                                placeholder="Contoh: Frontend Developer / Teknik Informatika / Jasa Desain Web"
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary"
                            >
                            @error('posisi_studi')
                                <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Gaji / Pendapatan (hanya untuk bekerja & wirausaha) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="status === 'bekerja' || status === 'wirausaha'">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Estimasi Rentang Pendapatan Bulanan
                            </label>
                            <select
                                name="pendapatan_bulanan"
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary"
                            >
                                <option value="">-- Pilih Rentang Gaji / Omzet --</option>
                                <option value="< Rp 2.000.000" {{ old('pendapatan_bulanan', $alumni->pendapatan_bulanan) === '< Rp 2.000.000' ? 'selected' : '' }}>&lt; Rp 2.000.000</option>
                                <option value="Rp 2.000.000 - Rp 4.000.000" {{ old('pendapatan_bulanan', $alumni->pendapatan_bulanan) === 'Rp 2.000.000 - Rp 4.000.000' ? 'selected' : '' }}>Rp 2.000.000 - Rp 4.000.000</option>
                                <option value="Rp 4.000.000 - Rp 7.000.000" {{ old('pendapatan_bulanan', $alumni->pendapatan_bulanan) === 'Rp 4.000.000 - Rp 7.000.000' ? 'selected' : '' }}>Rp 4.000.000 - Rp 7.000.000</option>
                                <option value="> Rp 7.000.000" {{ old('pendapatan_bulanan', $alumni->pendapatan_bulanan) === '> Rp 7.000.000' ? 'selected' : '' }}>&gt; Rp 7.000.000</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Masa Tunggu Memperoleh Peluang (Bulan)
                            </label>
                            <input
                                type="number"
                                min="0"
                                max="120"
                                name="waktu_tunggu_bulan"
                                value="{{ old('waktu_tunggu_bulan', $alumni->waktu_tunggu_bulan ?? 1) }}"
                                placeholder="0 = langsung bekerja saat lulus"
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary"
                            >
                        </div>
                    </div>
                </div>

                <!-- Section: Keselarasan & Testimoni -->
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-white/5">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        4. Keselarasan Kompetensi Vokasi & Testimoni
                    </h2>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tingkat Keselarasan Pekerjaan / Kuliah dengan Jurusan PPLG di Sekolah
                        </label>
                        <select
                            name="keselarasan"
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary"
                        >
                            <option value="">-- Pilih Tingkat Kesesuaian --</option>
                            <option value="sangat_sesuai" {{ old('keselarasan', $alumni->keselarasan) === 'sangat_sesuai' ? 'selected' : '' }}>Sangat Sesuai (Kompetensi PPLG terpakai langsung secara dominan)</option>
                            <option value="sesuai" {{ old('keselarasan', $alumni->keselarasan) === 'sesuai' ? 'selected' : '' }}>Sesuai (Sebagian besar keterampilan teknologi diterapkan)</option>
                            <option value="kurang_sesuai" {{ old('keselarasan', $alumni->keselarasan) === 'kurang_sesuai' ? 'selected' : '' }}>Kurang Sesuai (Hanya dasar komputer yang digunakan)</option>
                            <option value="tidak_sesuai" {{ old('keselarasan', $alumni->keselarasan) === 'tidak_sesuai' ? 'selected' : '' }}>Tidak Sesuai (Bekerja di bidang yang sepenuhnya berbeda)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Testimoni / Saran untuk Sekolah & Adik Kelas
                        </label>
                        <textarea
                            name="testimoni"
                            rows="3"
                            placeholder="Bagikan tips belajar, pengalaman di dunia kerja, atau pesan motivasi..."
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-primary"
                        >{{ old('testimoni', $alumni->testimoni) }}</textarea>
                    </div>

                    <!-- Visibility Toggle -->
                    <div class="flex items-center gap-3 pt-2">
                        <input
                            type="checkbox"
                            id="is_public"
                            name="is_public"
                            value="1"
                            {{ old('is_public', $alumni->is_public ?? true) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-slate-300 text-brand-primary focus:ring-brand-primary"
                        >
                        <label for="is_public" class="text-xs text-slate-600 dark:text-slate-300 cursor-pointer">
                            Izinkan data karier dan nama saya tampil di <strong>Direktori Publik Alumni</strong>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-slate-100 dark:border-white/5 flex items-center justify-end gap-3">
                    <a
                        href="{{ route('alumni.index') }}"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="px-6 py-2.5 rounded-xl bg-brand-primary text-slate-950 font-bold text-xs sm:text-sm shadow hover:brightness-105 active:scale-95 transition-all flex items-center gap-2"
                    >
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Simpan Data Tracer Study</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
