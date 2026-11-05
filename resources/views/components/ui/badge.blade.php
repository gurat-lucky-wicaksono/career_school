@props([
    'variant' => 'neutral',
    'dot' => true,
])

@php
    $variants = [
        'success' => [
            'badge' => 'bg-state-success-bg text-state-success-text border-emerald-500/30',
            'dot' => 'bg-emerald-500',
        ],
        'warning' => [
            'badge' => 'bg-state-warning-bg text-state-warning-text border-amber-500/30',
            'dot' => 'bg-amber-500',
        ],
        'danger' => [
            'badge' => 'bg-state-danger-bg text-state-danger-text border-rose-500/30',
            'dot' => 'bg-rose-500',
        ],
        'info' => [
            'badge' => 'bg-state-info-bg text-state-info-text border-sky-500/30',
            'dot' => 'bg-sky-500',
        ],
        'neutral' => [
            'badge' => 'bg-surface-bg text-text-secondary border-border-subtle',
            'dot' => 'bg-neutral-400',
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
