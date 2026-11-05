@props([
    'title',
    'value',
    'icon' => 'bar-chart-2',
    'trend' => null,
    'trendUp' => true,
])

<div {{ $attributes->merge(['class' => 'bg-surface-card border border-border-subtle rounded-card p-5 shadow-xs transition-all hover:border-brand-primary/40 flex flex-col justify-between group']) }}>
    <!-- Header: Title & Minimalist Uniform Icon -->
    <div class="flex items-center justify-between gap-3">
        <span class="text-xs font-medium text-text-secondary truncate">
            {{ $title }}
        </span>
        <div class="text-text-secondary group-hover:text-text-primary transition-colors flex-shrink-0">
            <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
        </div>
    </div>

    <!-- Body: Bold Metric Number & Compact iOS-Style Pill Badge -->
    <div class="mt-4 flex items-baseline justify-between gap-2">
        <h4 class="text-2xl sm:text-3xl font-bold text-text-primary font-heading tracking-tight">
            {{ $value }}
        </h4>

        @if ($trend)
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold flex-shrink-0 {{ $trendUp ? 'bg-state-success-bg text-state-success-text border border-emerald-500/30' : 'bg-state-danger-bg text-state-danger-text border border-rose-500/30' }}">
                @if ($trendUp)
                    <i data-lucide="trending-up" class="w-3 h-3"></i>
                @else
                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                @endif
                <span>{{ $trend }}</span>
            </span>
        @endif
    </div>
</div>
