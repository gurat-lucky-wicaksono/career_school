@extends('layouts.company')

@section('title', 'Edit Lowongan Kerja')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Edit Lowongan Pekerjaan
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Perbarui detail lowongan dan kriteria keahlian untuk posisi <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $job->judul }}</span>.
            </p>
        </div>

        <a 
            href="{{ route('company.jobs.index') }}" 
            class="px-3.5 py-2 rounded-xl border border-slate-200/80 dark:border-white/10 text-slate-600 dark:text-slate-300 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors"
        >
            &larr; Kembali
        </a>
    </div>

    @php
        $existingSkills = $job->skills->map(function ($s) {
            return [
                'id' => (string) $s->id,
                'bobot' => (int) $s->pivot->bobot,
                'wajib' => (bool) $s->pivot->wajib,
            ];
        })->values();

        if ($existingSkills->isEmpty()) {
            $existingSkills = collect([['id' => '', 'bobot' => 3, 'wajib' => false]]);
        }
    @endphp

    <!-- Form Card -->
    <form 
        method="POST" 
        action="{{ route('company.jobs.update', $job) }}" 
        class="space-y-6"
        x-data="{
            skillsList: {{ $existingSkills->toJson() }},
            addSkill() {
                this.skillsList.push({ id: '', bobot: 3, wajib: false });
                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            },
            removeSkill(index) {
                if (this.skillsList.length > 1) {
                    this.skillsList.splice(index, 1);
                }
            }
        }"
    >
        @csrf
        @method('PUT')

        <!-- 1. Informasi Utama Posisi -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-5">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px] pb-3 border-b border-slate-100 dark:border-white/5">
                Informasi Utama Posisi
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Judul Posisi -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Judul Posisi Lowongan <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="judul" 
                        value="{{ old('judul', $job->judul) }}"
                        required
                        class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >
                    @error('judul')
                        <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Pekerjaan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Jenis Pekerjaan <span class="text-rose-500">*</span>
                    </label>
                    <select 
                        name="jenis" 
                        required
                        class="w-full h-10 px-3 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >
                        <option value="penuh_waktu" {{ old('jenis', $job->jenis) === 'penuh_waktu' ? 'selected' : '' }}>Penuh Waktu (Full Time)</option>
                        <option value="magang" {{ old('jenis', $job->jenis) === 'magang' ? 'selected' : '' }}>Magang / Praktik Kerja Lapangan (PKL)</option>
                        <option value="paruh_waktu" {{ old('jenis', $job->jenis) === 'paruh_waktu' ? 'selected' : '' }}>Paruh Waktu (Part Time)</option>
                        <option value="proyek" {{ old('jenis', $job->jenis) === 'proyek' ? 'selected' : '' }}>Kontrak Proyek / Freelance</option>
                    </select>
                </div>

                <!-- Lokasi Penempatan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Lokasi Penempatan
                    </label>
                    <input 
                        type="text" 
                        name="lokasi" 
                        value="{{ old('lokasi', $job->lokasi) }}"
                        class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >
                </div>

                <!-- Kuota Penerimaan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Kuota Rekrutmen (Orang) <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="kuota" 
                        value="{{ old('kuota', $job->kuota) }}"
                        min="1" 
                        max="100" 
                        required
                        class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >
                </div>

                <!-- Batas Waktu Lamaran -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Batas Waktu Lamaran (Deadline)
                    </label>
                    <input 
                        type="date" 
                        name="batas_lamaran" 
                        value="{{ old('batas_lamaran', $job->batas_lamaran ? $job->batas_lamaran->format('Y-m-d') : '') }}"
                        class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >
                </div>

                <!-- Rentang Gaji (Opsional) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Estimasi Gaji / Uang Saku Min (Rp)
                    </label>
                    <input 
                        type="number" 
                        name="gaji_min" 
                        value="{{ old('gaji_min', $job->gaji_min ? (int)$job->gaji_min : '') }}"
                        step="50000"
                        class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Estimasi Gaji / Uang Saku Max (Rp)
                    </label>
                    <input 
                        type="number" 
                        name="gaji_max" 
                        value="{{ old('gaji_max', $job->gaji_max ? (int)$job->gaji_max : '') }}"
                        step="50000"
                        class="w-full h-10 px-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >
                </div>

                <!-- Deskripsi & Kualifikasi -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        Deskripsi Pekerjaan & Tanggung Jawab <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        name="deskripsi" 
                        rows="5" 
                        required
                        class="w-full p-3.5 text-xs rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                    >{{ old('deskripsi', $job->deskripsi) }}</textarea>
                </div>
            </div>
        </div>

        <!-- 2. Syarat Keahlian & Pembobotan -->
        <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100 dark:border-white/5">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider text-[11px]">
                        Syarat Keahlian & Pembobotan (Job Matching)
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Sesuaikan standar kompetensi yang dicocokkan ke profil pelamar.
                    </p>
                </div>

                <button 
                    type="button" 
                    @click="addSkill()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-neutral-800 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-neutral-700 transition-colors"
                >
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Tambah Syarat Skill</span>
                </button>
            </div>

            <!-- Dynamic Skills Rows -->
            <div class="space-y-3">
                <template x-for="(skillRow, idx) in skillsList" :key="idx">
                    <div class="p-3.5 rounded-xl bg-slate-50/70 dark:bg-neutral-800/40 border border-slate-200/60 dark:border-white/5 flex flex-col sm:flex-row sm:items-center gap-3">
                        <!-- Select Skill -->
                        <div class="flex-1">
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                Kompetensi / Keahlian
                            </label>
                            <select 
                                :name="'skills[' + idx + '][id]'" 
                                x-model="skillRow.id"
                                required
                                class="w-full h-9 px-3 text-xs rounded-lg bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                            >
                                <option value="">-- Pilih Standar Keahlian --</option>
                                @foreach ($skills as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->kategori ?? 'Umum' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Bobot Skill (1 - 5) -->
                        <div class="w-full sm:w-36">
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                Bobot Pengaruh
                            </label>
                            <select 
                                :name="'skills[' + idx + '][bobot]'" 
                                x-model="skillRow.bobot"
                                class="w-full h-9 px-3 text-xs rounded-lg bg-surface-card border border-slate-200/80 dark:border-white/10 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-primary/50"
                            >
                                <option value="1">1 - Dasar</option>
                                <option value="2">2 - Pendukung</option>
                                <option value="3">3 - Menengah (Standar)</option>
                                <option value="4">4 - Sangat Penting</option>
                                <option value="5">5 - Wajib Menguasai</option>
                            </select>
                        </div>

                        <!-- Wajib Checklist -->
                        <div class="flex items-center gap-2 pt-4 sm:pt-4">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs text-slate-700 dark:text-slate-300">
                                <input 
                                    type="checkbox" 
                                    :name="'skills[' + idx + '][wajib]'" 
                                    value="1" 
                                    x-model="skillRow.wajib"
                                    class="w-4 h-4 rounded text-brand-primary focus:ring-brand-primary border-slate-300 dark:border-white/20"
                                >
                                <span class="text-[11px] font-medium">Syarat Wajib</span>
                            </label>
                        </div>

                        <!-- Remove Row Button -->
                        <div class="sm:self-end sm:pt-4">
                            <button 
                                type="button" 
                                @click="removeSkill(idx)" 
                                :disabled="skillsList.length === 1"
                                class="p-2 rounded-lg text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 disabled:opacity-30 disabled:pointer-events-none transition-colors"
                                title="Hapus syarat ini"
                            >
                                <i data-lucide="trash" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <x-ui.button 
                variant="secondary" 
                href="{{ route('company.jobs.index') }}"
            >
                Batal
            </x-ui.button>

            <x-ui.button 
                variant="primary" 
                type="submit"
            >
                Simpan Perubahan
            </x-ui.button>
        </div>
    </form>
</div>
@endsection
