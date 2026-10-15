@props([
    'title' => 'Belum Ada Data Tersedia',
    'description' => 'Data belum ditambahkan ke dalam sistem atau tidak ditemukan berdasarkan kata kunci pencarian.',
    'icon' => 'inbox',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-12 px-4 text-center']) }}>
    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-neutral-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-3.5 border border-slate-200/80 dark:border-white/5">
        <i data-lucide="{{ $icon }}" class="w-7 h-7"></i>
    </div>

    <h4 class="text-base font-semibold text-slate-800 dark:text-slate-200 font-sans">
        {{ $title }}
    </h4>

    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mt-1 leading-relaxed">
        {{ $description }}
    </p>

    @if ($slot->isNotEmpty())
        <div class="mt-5">
            {{ $slot }}
        </div>
    @endif
</div>
