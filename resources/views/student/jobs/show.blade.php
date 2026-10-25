@extends('layouts.student')

@section('title', $job->judul . ' - ' . ($job->company->nama ?? 'Mitra Industri'))

@section('content')
<div class="space-y-6" x-data="{ applyModalOpen: false }">
    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <x-ui.button 
            variant="secondary" 
            size="sm" 
            href="{{ route('student.jobs.index') }}"
        >
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i>
            Kembali ke Lowongan
        </x-ui.button>

        <span class="text-xs text-slate-400">
            Dipublikasikan: {{ $job->created_at ? $job->created_at->translatedFormat('d F Y') : '-' }}
        </span>
    </div>

    <!-- 1. Job Hero Card -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4 min-w-0">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-neutral-800 border border-slate-200/60 dark:border-white/10 text-slate-800 dark:text-slate-100 font-bold text-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                    {{ strtoupper(substr($job->company->nama ?? 'P', 0, 2)) }}
                </div>

                <div class="space-y-1.5 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold text-brand-primary">
                            {{ $job->company->nama ?? 'Mitra Industri' }}
                        </span>
                        @if ($job->company?->isVerified())
                            <span title="Mitra Terverifikasi BKK" class="text-brand-primary">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                            </span>
                        @endif
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $job->jenis === 'magang' ? 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200/60' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60' }}">
                            {{ $job->jenis === 'magang' ? 'Magang / PKL' : 'Penuh Waktu' }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                        {{ $job->judul }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400 pt-1">
                        <span class="flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                            {{ $job->lokasi }}
                        </span>
                        @if ($job->gaji_min)
                            <span class="flex items-center gap-1 font-medium text-slate-700 dark:text-slate-200">
                                <i data-lucide="wallet" class="w-3.5 h-3.5 text-slate-400"></i>
                                Rp {{ number_format($job->gaji_min, 0, ',', '.') }} @if ($job->gaji_max) - Rp {{ number_format($job->gaji_max, 0, ',', '.') }} @endif
                            </span>
                        @endif
                        <span class="flex items-center gap-1">
                            <i data-lucide="users" class="w-3.5 h-3.5 text-slate-400"></i>
                            Kuota: {{ $job->kuota ?? 'Fleksibel' }} siswa
                        </span>
                        @if ($job->batas_lamaran)
                            <span class="flex items-center gap-1 text-rose-600 dark:text-rose-400 font-medium">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                Batas: {{ $job->batas_lamaran->translatedFormat('d M Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Section -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-shrink-0">
                @if ($existingApplication)
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-neutral-800/80 border border-slate-200 dark:border-white/10 text-center sm:text-right">
                        <div class="flex items-center justify-center sm:justify-end gap-1.5 text-xs font-bold text-slate-900 dark:text-white">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                            <span>Sudah Diajukan</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 capitalize">
                            Status: <strong class="text-brand-primary">{{ $existingApplication->status }}</strong>
                        </p>
                    </div>
                    <x-ui.button variant="secondary" size="md" href="{{ route('student.applications.index') }}">
                        Lihat Lamaran Saya
                    </x-ui.button>
                @else
                    <button 
                        type="button" 
                        @click="applyModalOpen = true"
                        class="px-5 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary/90 text-white font-semibold text-xs shadow-sm shadow-brand-primary/25 transition-all flex items-center justify-center gap-2"
                    >
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Lamar Posisi Ini</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. Smart Match Score Analysis (Formula KAK Paket 15) -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-white/5">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                        Analisis Kecocokan Rekomendasi
                    </h2>
                    <span class="px-2 py-0.5 rounded-full bg-brand-primary/10 dark:bg-brand-primary/20 text-brand-primary text-[10px] font-bold">
                        Formula KAK 15
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Total Skor = (0.60 x K) + (0.15 x J) + (0.10 x T) + (0.10 x P) + (0.05 x L)
                </p>
            </div>

            <!-- Big Percentage Badge -->
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">Skor Akhir:</span>
                <span class="px-3.5 py-1.5 rounded-xl text-base font-bold {{ $match['total'] >= 80 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200' : ($match['total'] >= 60 ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200' : 'bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-slate-300') }} flex items-center gap-1.5">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    <span>{{ $match['percentage'] }}</span>
                </span>
            </div>
        </div>

        <!-- 5 Component Metric Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <!-- K (60%) -->
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Keahlian (60%)</span>
                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $match['components']['k'] }} / 100</p>
                <div class="w-full bg-slate-200 dark:bg-neutral-700 h-1 rounded-full overflow-hidden">
                    <div class="bg-brand-primary h-1 rounded-full" style="width: {{ $match['components']['k'] }}%"></div>
                </div>
            </div>

            <!-- J (15%) -->
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Jurusan (15%)</span>
                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $match['components']['j'] }} / 100</p>
                <div class="w-full bg-slate-200 dark:bg-neutral-700 h-1 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-1 rounded-full" style="width: {{ $match['components']['j'] }}%"></div>
                </div>
            </div>

            <!-- T (10%) -->
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Kemahiran (10%)</span>
                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $match['components']['t'] }} / 100</p>
                <div class="w-full bg-slate-200 dark:bg-neutral-700 h-1 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-1 rounded-full" style="width: {{ $match['components']['t'] }}%"></div>
                </div>
            </div>

            <!-- P (10%) -->
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Portofolio (10%)</span>
                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $match['components']['p'] }} / 100</p>
                <div class="w-full bg-slate-200 dark:bg-neutral-700 h-1 rounded-full overflow-hidden">
                    <div class="bg-purple-500 h-1 rounded-full" style="width: {{ $match['components']['p'] }}%"></div>
                </div>
            </div>

            <!-- L (5%) -->
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-neutral-800/40 border border-slate-100 dark:border-white/5 space-y-1 col-span-2 sm:col-span-1">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Lokasi (5%)</span>
                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $match['components']['l'] }} / 100</p>
                <div class="w-full bg-slate-200 dark:bg-neutral-700 h-1 rounded-full overflow-hidden">
                    <div class="bg-amber-500 h-1 rounded-full" style="width: {{ $match['components']['l'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Skill Comparison Details -->
        <div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Matched Skills -->
            <div class="space-y-2">
                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                    Keahlian yang Anda Kuasai ({{ count($match['matched_skills']) }})
                </span>
                <div class="flex flex-wrap items-center gap-1.5">
                    @forelse ($match['matched_skills'] as $skillName)
                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/70 dark:border-emerald-800/40">
                            {{ $skillName }}
                        </span>
                    @empty
                        <span class="text-xs text-slate-400">Belum ada keahlian profil yang cocok dengan lowongan ini.</span>
                    @endforelse
                </div>
            </div>

            <!-- Unmatched Skills -->
            <div class="space-y-2">
                <span class="text-xs font-semibold text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                    <i data-lucide="circle-dashed" class="w-3.5 h-3.5"></i>
                    Keahlian Tambahan yang Dibutuhkan ({{ count($match['unmatched_skills']) }})
                </span>
                <div class="flex flex-wrap items-center gap-1.5">
                    @forelse ($match['unmatched_skills'] as $skillName)
                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-white/10">
                            {{ $skillName }}
                        </span>
                    @empty
                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Seluruh keahlian lowongan ini telah Anda kuasai!</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Job Description & Company Info (2 Cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Full Description (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-6 shadow-sm space-y-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-white/5">
                        Deskripsi & Rincian Tugas
                    </h2>
                    <div class="mt-4 text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line font-sans">
                        {{ $job->deskripsi }}
                    </div>
                </div>

                <!-- Required Competencies List -->
                @if ($job->skills->isNotEmpty())
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                            Daftar Kualifikasi Kompetensi
                        </h3>
                        <div class="divide-y divide-slate-100 dark:divide-white/5 border border-slate-100 dark:border-white/5 rounded-xl overflow-hidden">
                            @foreach ($job->skills as $s)
                                <div class="p-3 flex items-center justify-between gap-3 text-xs bg-slate-50/50 dark:bg-neutral-800/30">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-brand-primary"></i>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $s->nama }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if ($s->pivot->wajib)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200">
                                                Wajib
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-slate-300">
                                                Nilai Tambah
                                            </span>
                                        @endif
                                        <span class="text-slate-400 text-[11px]">Bobot: {{ $s->pivot->bobot ?? 1 }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Company Profile & Recruitment Overview (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Company Overview Card -->
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="pb-3 border-b border-slate-100 dark:border-white/5">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        Profil Mitra Perusahaan
                    </h3>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Nama Perusahaan</span>
                        <span class="font-bold text-slate-800 dark:text-slate-100">{{ $job->company->nama ?? 'Mitra DUDI' }}</span>
                    </div>

                    @if ($job->company?->bidang)
                        <div>
                            <span class="text-slate-400 block text-[11px]">Bidang Industri</span>
                            <span class="font-medium text-slate-700 dark:text-slate-200">{{ $job->company->bidang }}</span>
                        </div>
                    @endif

                    <div>
                        <span class="text-slate-400 block text-[11px]">Lokasi Kantor</span>
                        <span class="font-medium text-slate-700 dark:text-slate-200">{{ $job->company->kota ?? $job->lokasi }}</span>
                    </div>

                    @if ($job->company?->website)
                        <div>
                            <span class="text-slate-400 block text-[11px]">Website Resmi</span>
                            <a href="{{ $job->company->website }}" target="_blank" rel="noopener noreferrer" class="font-medium text-brand-primary hover:underline flex items-center gap-1 mt-0.5">
                                <span>{{ $job->company->website }}</span>
                                <i data-lucide="external-link" class="w-3 h-3"></i>
                            </a>
                        </div>
                    @endif

                    @if ($job->company?->deskripsi)
                        <div class="pt-2">
                            <span class="text-slate-400 block text-[11px] mb-1">Tentang Mitra</span>
                            <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-[11px]">
                                {{ $job->company->deskripsi }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Application Safety Notice -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-neutral-800/50 border border-slate-200/60 dark:border-white/5 space-y-2 text-xs text-slate-600 dark:text-slate-400">
                <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white">
                    <i data-lucide="shield-check" class="w-4 h-4 text-brand-primary"></i>
                    <span>Jaminan Keamanan BKK</span>
                </div>
                <p class="text-[11px] leading-relaxed">
                    Seluruh kemitraan industri di Career School SMKN 1 Cirebon telah melalui proses verifikasi dan pemantauan resmi Bursa Kerja Khusus (BKK). Tidak ada pungutan biaya apapun.
                </p>
            </div>
        </div>
    </div>

    <!-- 4. Application Submission Modal (Alpine.js) -->
    <div 
        x-show="applyModalOpen" 
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        style="display: none;"
    >
        <div 
            @click.away="applyModalOpen = false"
            x-show="applyModalOpen"
            x-transition:enter="transition-all ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition-all ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl w-full max-w-xl shadow-xl overflow-hidden flex flex-col"
        >
            <!-- Modal Header -->
            <div class="p-5 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        Konfirmasi Pengajuan Lamaran
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $job->judul }} &bull; {{ $job->company->nama ?? 'Mitra Industri' }}
                    </p>
                </div>
                <button 
                    type="button" 
                    @click="applyModalOpen = false" 
                    class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-neutral-800 flex items-center justify-center transition-colors"
                >
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Modal Form Body -->
            <form method="POST" action="{{ route('student.jobs.apply', $job) }}" class="p-5 space-y-4">
                @csrf

                <!-- Applicant Summary Box -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-neutral-800/50 border border-slate-200/60 dark:border-white/5 space-y-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Berkas Talenta yang Dikirimkan</span>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-500 text-[11px]">Nama Lengkap:</span>
                            <p class="font-bold text-slate-800 dark:text-slate-100">{{ $student->user->name }}</p>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[11px]">Kelas & Jurusan:</span>
                            <p class="font-bold text-slate-800 dark:text-slate-100">{{ $student->schoolClass?->nama ?? 'PPLG' }}</p>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[11px]">Keahlian Disertakan:</span>
                            <p class="font-semibold text-brand-primary">{{ $student->skills->count() }} Keahlian ({{ $match['components']['k'] }}% Sesuai)</p>
                        </div>
                        <div>
                            <span class="text-slate-500 text-[11px]">Portofolio Terlampir:</span>
                            <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $student->portfolios->count() }} Proyek Digital</p>
                        </div>
                    </div>
                </div>

                <!-- Cover Note / Motivation -->
                <div class="space-y-1.5">
                    <label for="pesan" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                        Surat Pengantar / Catatan untuk HRD (Opsional)
                    </label>
                    <textarea 
                        name="pesan" 
                        id="pesan" 
                        rows="4" 
                        maxlength="1500"
                        placeholder="Tuliskan motivasi, pengalaman proyek relevan, atau komitmen Anda dalam mengikuti program ini..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 placeholder-slate-400 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-all outline-none resize-none"
                    ></textarea>
                    <span class="text-[11px] text-slate-400 block">
                        HRD dan penguji industri dapat membaca portofolio dan catatan ini langsung pada dasbor seleksi mitra.
                    </span>
                </div>

                <!-- Modal Actions -->
                <div class="pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-end gap-2.5">
                    <x-ui.button variant="secondary" size="md" type="button" @click="applyModalOpen = false">
                        Batal
                    </x-ui.button>
                    <x-ui.button variant="primary" size="md" type="submit">
                        <i data-lucide="send" class="w-4 h-4 mr-1.5"></i>
                        Kirim Lamaran Sekarang
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
