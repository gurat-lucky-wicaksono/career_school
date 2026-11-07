@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-sans whitespace-nowrap shrink-0 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-brand-primary/40 disabled:opacity-50 disabled:pointer-events-none select-none active:scale-[0.96]';

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs rounded-btn',
        'md' => 'px-4 py-2 text-sm rounded-btn',
        'lg' => 'px-6 py-2.5 text-base rounded-btn font-semibold',
    ];

    $variants = [
        'primary' => 'bg-brand-primary text-text-on-brand-primary font-semibold hover:brightness-105 shadow-xs',
        'secondary' => 'bg-surface-card border border-border-default/70 text-text-primary hover:border-text-primary font-medium shadow-xs',
        'danger' => 'bg-state-danger-bg text-state-danger-text border border-rose-500/30 hover:bg-rose-600 hover:text-white font-semibold shadow-xs',
        'outline' => 'border border-border-default text-text-primary hover:bg-surface-bg font-medium',
        'ghost' => 'text-text-secondary hover:text-text-primary hover:bg-surface-bg font-medium',
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
