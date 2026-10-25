@extends('layouts.student')

@section('title', 'Karya & Portofolio Siswa')

@section('content')
<div class="space-y-6">
    <!-- 1. Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Karya & Portofolio Siswa
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Tampilkan hasil karya proyek nyata, aplikasi, dan sertifikat untuk meningkatkan skor kecocokan industri (bobot 10%).
            </p>
        </div>

        <x-ui.button variant="primary" size="md" href="{{ route('student.portfolios.create') }}">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Tambah Karya Baru
        </x-ui.button>
    </div>

    <!-- 2. Portfolios Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($portfolios as $portfolio)
            <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm hover:border-slate-300 dark:hover:border-white/20 transition-all flex flex-col justify-between group">
                <div class="space-y-3">
                    <!-- Top Category & Date -->
                    <div class="flex items-center justify-between gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-brand-primary/10 text-brand-primary dark:bg-brand-primary/20">
                            {{ $portfolio->kategori ?? 'Proyek Vokasi' }}
                        </span>
                        <span class="text-[11px] text-slate-400">
                            {{ $portfolio->created_at->translatedFormat('d M Y') }}
                        </span>
                    </div>

                    <!-- Title & Description -->
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-brand-primary transition-colors">
                            {{ $portfolio->judul }}
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1.5 line-clamp-3 leading-relaxed">
                            {{ $portfolio->deskripsi ?? 'Tidak ada deskripsi rinci untuk proyek ini.' }}
                        </p>
                    </div>

                    <!-- External Links / Attachments -->
                    <div class="flex flex-wrap items-center gap-2 pt-2">
                        @if ($portfolio->tautan)
                            <a 
                                href="{{ $portfolio->tautan }}" 
                                target="_blank" 
                                rel="noopener noreferrer" 
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 hover:bg-slate-200/70 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-200 transition-colors"
                            >
                                <i data-lucide="external-link" class="w-3.5 h-3.5 text-brand-primary"></i>
                                <span>Demo / Repositori</span>
                            </a>
                        @endif

                        @if ($portfolio->lampiran)
                            <a 
                                href="{{ asset('storage/' . $portfolio->lampiran) }}" 
                                target="_blank" 
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 hover:bg-slate-200/70 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-200 transition-colors"
                            >
                                <i data-lucide="paperclip" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>Berkas Lampiran</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-5 pt-3.5 border-t border-slate-100 dark:border-white/5 flex items-center justify-between gap-2">
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                        <span>Publik & Aktif</span>
                    </span>

                    <div class="flex items-center gap-1.5">
                        <a 
                            href="{{ route('student.portfolios.edit', $portfolio) }}" 
                            class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-neutral-800 transition-colors" 
                            title="Edit Portofolio"
                        >
                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                        </a>

                        <form method="POST" action="{{ route('student.portfolios.destroy', $portfolio) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus portofolio ini?');">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="submit" 
                                class="p-2 rounded-xl text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors" 
                                title="Hapus Portofolio"
                            >
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-neutral-800 flex items-center justify-center mx-auto text-slate-400">
                    <i data-lucide="folder-kanban" class="w-7 h-7"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white mt-4">
                    Belum Ada Portofolio Terdaftar
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                    Unggah proyek perangkat lunak, desain UI/UX, atau hasil praktik kejuruan Anda untuk memperkuat profil dan menarik minat mitra industri.
                </p>
                <div class="mt-4">
                    <x-ui.button variant="primary" size="sm" href="{{ route('student.portfolios.create') }}">
                        <i data-lucide="plus" class="w-4 h-4 mr-1.5"></i>
                        Tambah Karya Pertama
                    </x-ui.button>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
