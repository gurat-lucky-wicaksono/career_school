@props([
    'title',
    'value',
    'icon' => 'bar-chart-2',
    'trend' => null,
    'trendUp' => true,
])

<div {{ $attributes->merge(['class' => 'bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl p-5 shadow-sm transition-all hover:border-slate-300 dark:hover:border-white/20 flex flex-col justify-between group']) }}>
    <!-- Header: Title & Minimalist Uniform Icon (No colored box background) -->
    <div class="flex items-center justify-between gap-3">
        <span class="text-xs font-medium text-slate-500 dark:text-slate-400 truncate">
            {{ $title }}
        </span>
        <div class="text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors flex-shrink-0">
            <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
        </div>
    </div>

    <!-- Body: Bold Metric Number & Compact iOS-Style Pill Badge -->
    <div class="mt-4 flex items-baseline justify-between gap-2">
        <h4 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
            {{ $value }}
        </h4>

        @if ($trend)
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold flex-shrink-0 {{ $trendUp ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40' : 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/40' }}">
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
