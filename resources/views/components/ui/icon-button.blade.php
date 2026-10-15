@props([
    'type' => 'button',
    'variant' => 'default',
    'size' => 'md',
    'label' => 'Tindakan',
    'icon' => null,
    'href' => null,
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-sans transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none select-none flex-shrink-0';

    $sizes = [
        'sm' => 'w-8 h-8 rounded-lg text-xs',
        'md' => 'w-9 h-9 rounded-xl text-sm',
        'lg' => 'w-11 h-11 rounded-xl text-base',
    ];

    $iconSizes = [
        'sm' => 'w-3.5 h-3.5',
        'md' => 'w-4 h-4',
        'lg' => 'w-5 h-5',
    ];

    $variants = [
        'default' => 'bg-surface-card border border-slate-200/90 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-neutral-800 shadow-sm focus:ring-slate-300',
        'subtle' => 'bg-slate-100/80 dark:bg-neutral-800/80 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/80 dark:hover:bg-neutral-700/80 focus:ring-slate-300',
        'primary' => 'bg-brand-primary text-neutral-900 hover:brightness-105 active:scale-[0.97] focus:ring-brand-primary/50 shadow-sm',
        'danger' => 'bg-surface-card border border-slate-200 dark:border-white/10 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 hover:border-red-300 dark:hover:border-red-800 focus:ring-red-400',
        'ghost' => 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-neutral-800 focus:ring-slate-300',
    ];

    $classes = $baseClasses . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['default']);
    $iconClass = $iconSizes[$size] ?? $iconSizes['md'];
@endphp

@if ($href)
    <a href="{{ $href }}" aria-label="{{ $label }}" title="{{ $label }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i data-lucide="{{ $icon }}" class="{{ $iconClass }}"></i>
        @else
            {{ $slot }}
        @endif
    </a>
@else
    <button type="{{ $type }}" aria-label="{{ $label }}" title="{{ $label }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i data-lucide="{{ $icon }}" class="{{ $iconClass }}"></i>
        @else
            {{ $slot }}
        @endif
    </button>
@endif
