@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-sans transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none select-none';

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs rounded-lg',
        'md' => 'px-4 py-2.5 text-sm rounded-xl',
        'lg' => 'px-6 py-3 text-base rounded-xl font-semibold',
    ];

    $variants = [
        'primary' => 'bg-brand-primary text-neutral-900 font-semibold hover:brightness-105 active:scale-[0.99] focus:ring-brand-primary/50 shadow-sm',
        'secondary' => 'bg-surface-card border border-slate-200/90 dark:border-white/10 text-slate-800 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-neutral-800/80 font-medium focus:ring-slate-300 dark:focus:ring-neutral-700 shadow-sm',
        'danger' => 'bg-red-600 hover:bg-red-700 active:scale-[0.99] text-white font-semibold focus:ring-red-500 shadow-sm',
        'outline' => 'border border-slate-300 dark:border-white/20 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-neutral-800 font-medium focus:ring-slate-400',
        'ghost' => 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-neutral-800 font-medium focus:ring-slate-300',
    ];

    $classes = $baseClasses . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
