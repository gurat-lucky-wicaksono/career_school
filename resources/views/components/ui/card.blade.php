@props([
    'title' => null,
    'subtitle' => null,
    'padding' => null,
])

<div {{ $attributes->merge(['class' => 'bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm transition-colors duration-150 relative p-6 sm:p-7']) }}>
    @if ($title || isset($action))
        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                @if ($title)
                    <h3 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white font-sans tracking-tight">
                        {{ $title }}
                    </h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

            @isset($action)
                <div class="flex items-center gap-2">
                    {{ $action }}
                </div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding ?? '' }}">
        {{ $slot }}
    </div>
</div>
