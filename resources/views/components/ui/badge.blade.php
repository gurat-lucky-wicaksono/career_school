@props([
    'variant' => 'neutral',
    'dot' => true,
])

@php
    $variants = [
        'success' => [
            'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60',
            'dot' => 'bg-emerald-500',
        ],
        'warning' => [
            'badge' => 'bg-amber-50 text-amber-700 border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60',
            'dot' => 'bg-amber-500',
        ],
        'danger' => [
            'badge' => 'bg-rose-50 text-rose-700 border-rose-200/80 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60',
            'dot' => 'bg-rose-500',
        ],
        'info' => [
            'badge' => 'bg-sky-50 text-sky-700 border-sky-200/80 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800/60',
            'dot' => 'bg-sky-500',
        ],
        'neutral' => [
            'badge' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-neutral-800 dark:text-slate-300 dark:border-white/10',
            'dot' => 'bg-slate-400',
        ],
    ];

    $config = $variants[$variant] ?? $variants['neutral'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border ' . $config['badge']]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }}"></span>
    @endif
    {{ $slot }}
</span>
