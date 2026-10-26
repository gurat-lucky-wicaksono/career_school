@extends('layouts.teacher')

@section('title', 'Validasi Keahlian Siswa Vokasi')

@section('content')
<div class="space-y-6" x-data="{
    activeModal: false,
    selectedSkill: null,
    actionUrl: '',
    selectedLevel: 3,
    teacherNote: '',
    openValidateModal(skill) {
        this.selectedSkill = skill;
        this.actionUrl = '{{ url('/teacher/skills') }}/' + skill.id + '/validate';
        this.selectedLevel = skill.tingkat;
        this.teacherNote = 'Kompetensi telah teruji dan memenuhi kriteria kejuruan SMKN 1 Cirebon.';
        this.activeModal = true;
    }
}">
    <!-- 1. Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                Validasi Keahlian Siswa Vokasi
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Verifikasi klaim kompetensi teknis siswa untuk menyuplai data akurat pada algoritma job matching KAK 15 (bobot 60%).
            </p>
        </div>
    </div>

    <!-- 2. Status Tabs & Filters -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-4 shadow-sm space-y-4">
        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 custom-scrollbar text-xs">
            @php
                $currentStatus = request('status', 'pending');
            @endphp
            <a 
                href="{{ route('teacher.skills.index', array_merge(request()->query(), ['status' => 'pending'])) }}" 
                class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentStatus === 'pending' ? 'bg-brand-primary text-white font-semibold shadow-sm shadow-brand-primary/25' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-800' }}"
            >
                <span>Menunggu Validasi</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus === 'pending' ? 'bg-white/20 text-white' : ($statusCounts['pending'] > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-slate-200/70 dark:bg-neutral-700 text-slate-700 dark:text-slate-300') }}">
                    {{ $statusCounts['pending'] }}
                </span>
            </a>

            <a 
                href="{{ route('teacher.skills.index', array_merge(request()->query(), ['status' => 'validated'])) }}" 
                class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentStatus === 'validated' ? 'bg-brand-primary text-white font-semibold shadow-sm shadow-brand-primary/25' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-800' }}"
            >
                <span>Telah Tervalidasi</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus === 'validated' ? 'bg-white/20 text-white' : 'bg-slate-200/70 dark:bg-neutral-700 text-slate-700 dark:text-slate-300' }}">
                    {{ $statusCounts['validated'] }}
                </span>
            </a>

            <a 
                href="{{ route('teacher.skills.index', array_merge(request()->query(), ['status' => 'all'])) }}" 
                class="px-3.5 py-2 rounded-xl font-medium whitespace-nowrap transition-all flex items-center gap-2 {{ $currentStatus === 'all' ? 'bg-brand-primary text-white font-semibold shadow-sm shadow-brand-primary/25' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-800' }}"
            >
                <span>Semua Data</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200/70 dark:bg-neutral-700 text-slate-700 dark:text-slate-300' }}">
                    {{ $statusCounts['all'] }}
                </span>
            </a>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('teacher.skills.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 pt-2 border-t border-slate-100 dark:border-white/5 items-center">
            <input type="hidden" name="status" value="{{ $currentStatus }}">

            <!-- Search Input (4 Cols) -->
            <div class="lg:col-span-4 relative">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Cari siswa atau NISN..." 
                    class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                >
            </div>

            <!-- Class Filter (3 Cols) -->
            <div class="lg:col-span-3">
                <select 
                    name="class_id" 
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                >
                    <option value="">Semua Rombel / Kelas</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Skill Filter (3 Cols) -->
            <div class="lg:col-span-3">
                <select 
                    name="skill_id" 
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                >
                    <option value="">Semua Standar Keahlian</option>
                    @foreach ($skills as $s)
                        <option value="{{ $s->id }}" {{ request('skill_id') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Actions (2 Cols) -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <x-ui.button variant="secondary" size="md" type="submit" class="w-full justify-center">
                    Filter
                </x-ui.button>
                @if (request()->hasAny(['q', 'class_id', 'skill_id']))
                    <a href="{{ route('teacher.skills.index', ['status' => $currentStatus]) }}" class="p-2 rounded-xl border border-slate-200 dark:border-white/10 hover:bg-slate-100 dark:hover:bg-neutral-800 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 3. Table List of Student Skills -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('teacher.skills.bulk-validate') }}" id="bulk-form">
            @csrf

            <!-- Bulk Action Toolbar (Only for pending tab) -->
            @if ($currentStatus === 'pending' && $studentSkills->isNotEmpty())
                <div class="p-3.5 bg-slate-50/80 dark:bg-neutral-800/50 border-b border-slate-100 dark:border-white/5 flex items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <input 
                            type="checkbox" 
                            id="select-all" 
                            onclick="document.querySelectorAll('.skill-checkbox').forEach(cb => cb.checked = this.checked)"
                            class="rounded border-slate-300 text-brand-primary focus:ring-brand-primary/20"
                        >
                        <label for="select-all" class="font-medium text-slate-700 dark:text-slate-300 cursor-pointer">
                            Pilih Semua di Halaman Ini
                        </label>
                    </div>

                    <button 
                        type="submit" 
                        onclick="return confirm('Apakah Anda yakin ingin memvalidasi seluruh keahlian yang dipilih?')"
                        class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm transition-colors flex items-center gap-1.5"
                    >
                        <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                        <span>Sahkan Sekaligus</span>
                    </button>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 dark:bg-neutral-800/60 border-b border-slate-100 dark:border-white/5 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider text-[10px]">
                        <tr>
                            @if ($currentStatus === 'pending')
                                <th class="py-3 px-4 w-10"></th>
                            @endif
                            <th class="py-3 px-4">Siswa Vokasi</th>
                            <th class="py-3 px-4">Rombel / Kelas</th>
                            <th class="py-3 px-4">Keahlian Diklaim</th>
                            <th class="py-3 px-4">Tingkat Kemahiran</th>
                            <th class="py-3 px-4">Status & Catatan Pengesahan</th>
                            <th class="py-3 px-4 text-right">Aksi Guru</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        @forelse ($studentSkills as $item)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-neutral-800/40 transition-colors">
                                @if ($currentStatus === 'pending')
                                    <td class="py-3.5 px-4">
                                        <input 
                                            type="checkbox" 
                                            name="skill_ids[]" 
                                            value="{{ $item->id }}" 
                                            class="skill-checkbox rounded border-slate-300 text-brand-primary focus:ring-brand-primary/20"
                                        >
                                    </td>
                                @endif

                                <!-- Siswa -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-brand-primary/10 text-brand-primary font-bold text-xs flex items-center justify-center flex-shrink-0">
                                            {{ strtoupper(substr($item->student->user->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 dark:text-white block">
                                                {{ $item->student->user->name }}
                                            </span>
                                            <span class="text-[11px] text-slate-400 font-mono">
                                                NISN: {{ $item->student->nisn }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Kelas -->
                                <td class="py-3.5 px-4 font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $item->student->schoolClass?->nama ?? 'PPLG' }}
                                </td>

                                <!-- Keahlian -->
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">
                                        {{ $item->skill->nama }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        {{ $item->skill->kategori ?? 'Vokasi' }}
                                    </span>
                                </td>

                                <!-- Kemahiran Progress -->
                                <td class="py-3.5 px-4">
                                    <div class="space-y-1 min-w-[120px]">
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="font-bold text-slate-800 dark:text-slate-200">Level {{ $item->tingkat }}/5</span>
                                            <span class="text-slate-400 text-[10px]">{{ ($item->tingkat / 5) * 100 }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-200 dark:bg-neutral-700 h-1.5 rounded-full overflow-hidden">
                                            <div class="bg-brand-primary h-1.5 rounded-full" style="width: {{ ($item->tingkat / 5) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status & Endorsement -->
                                <td class="py-3.5 px-4">
                                    @if ($item->tervalidasi)
                                        <div class="space-y-0.5">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/60">
                                                <i data-lucide="check" class="w-3 h-3"></i>
                                                <span>Tervalidasi Resmi</span>
                                            </span>
                                            @if ($item->endorsements->isNotEmpty())
                                                <p class="text-[10px] text-slate-400 italic">
                                                    "{{ Str::limit($item->endorsements->first()->catatan, 40) }}"
                                                </p>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/60">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            <span>Menunggu Validasi</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button 
                                            type="button" 
                                            @click="openValidateModal({
                                                id: {{ $item->id }},
                                                student_name: '{{ addslashes($item->student->user->name) }}',
                                                skill_name: '{{ addslashes($item->skill->nama) }}',
                                                tingkat: {{ $item->tingkat }}
                                            })"
                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm transition-colors flex items-center gap-1"
                                            title="Sahkan & Sesuaikan Level"
                                        >
                                            <i data-lucide="badge-check" class="w-3.5 h-3.5"></i>
                                            <span>{{ $item->tervalidasi ? 'Sesuaikan' : 'Sahkan' }}</span>
                                        </button>

                                        @if ($item->tervalidasi)
                                            <form method="POST" action="{{ route('teacher.skills.reject', $item) }}" onsubmit="return confirm('Batalkan pengesahan keahlian ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors" 
                                                    title="Batalkan Pengesahan"
                                                >
                                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-neutral-800 flex items-center justify-center mx-auto mb-2 text-slate-400">
                                        <i data-lucide="inbox" class="w-6 h-6"></i>
                                    </div>
                                    <p class="font-semibold text-slate-700 dark:text-slate-300">Tidak ada data keahlian pada filter ini.</p>
                                    <p class="text-[11px] mt-0.5">Semua data siswa telah sesuai kriteria penelusuran Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    <!-- 4. Pagination -->
    @if ($studentSkills->hasPages())
        <div class="pt-4 flex justify-center">
            {{ $studentSkills->links() }}
        </div>
    @endif

    <!-- 5. Single Skill Validation Modal (Alpine.js) -->
    <div 
        x-show="activeModal" 
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
            @click.away="activeModal = false"
            x-show="activeModal"
            class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl w-full max-w-lg shadow-xl overflow-hidden flex flex-col"
        >
            <div class="p-5 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                        Sahkan & Beri Catatan Guru
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="selectedSkill ? selectedSkill.student_name + ' &bull; ' + selectedSkill.skill_name : ''"></p>
                </div>
                <button 
                    type="button" 
                    @click="activeModal = false" 
                    class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center transition-colors"
                >
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form :action="actionUrl" method="POST" class="p-5 space-y-4">
                @csrf

                <!-- Rating Level Selection (1-5) -->
                <div class="space-y-1.5">
                    <label for="modal-tingkat" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                        Penyesuaian Tingkat Kemahiran Kompetensi Guru
                    </label>
                    <select 
                        name="tingkat" 
                        id="modal-tingkat" 
                        x-model="selectedLevel"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all"
                    >
                        <option value="1">Level 1 - Pemula (Teori & Konsep Dasar)</option>
                        <option value="2">Level 2 - Menengah Awal (Mampu Menjalankan Praktikum Terarah)</option>
                        <option value="3">Level 3 - Terampil (Mandiri Mengembangkan Fitur)</option>
                        <option value="4">Level 4 - Mahir (Mendalam, Pemecahan Masalah & Optimasi)</option>
                        <option value="5">Level 5 - Sangat Mahir (Standar Profesional Industri)</option>
                    </select>
                </div>

                <!-- Catatan Evaluasi Guru -->
                <div class="space-y-1.5">
                    <label for="modal-catatan" class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                        Catatan Pengesahan Guru (Endorsement Note)
                    </label>
                    <textarea 
                        name="catatan" 
                        id="modal-catatan" 
                        rows="3" 
                        maxlength="500"
                        x-model="teacherNote"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-800/60 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition-all resize-none"
                    ></textarea>
                    <span class="text-[11px] text-slate-400 block">
                        Catatan ini akan tersimpan pada rekam jejak sertifikasi internal sekolah dan terbaca oleh HRD mitra industri.
                    </span>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-end gap-2.5">
                    <x-ui.button variant="secondary" size="md" type="button" @click="activeModal = false">
                        Batal
                    </x-ui.button>
                    <x-ui.button variant="primary" size="md" type="submit">
                        <i data-lucide="check" class="w-4 h-4 mr-1.5"></i>
                        Simpan & Sahkan
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
