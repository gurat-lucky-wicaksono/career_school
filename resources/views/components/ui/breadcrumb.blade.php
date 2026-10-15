@props([
    'items' => [],
])

@php
    $filteredItems = collect($items)->filter(function ($item) {
        return strtolower(trim($item['label'] ?? '')) !== 'dashboard';
    })->values();
@endphp

<nav aria-label="Breadcrumb" class="flex items-center text-xs sm:text-sm font-sans">
    <ol class="flex items-center gap-1.5 sm:gap-2 text-slate-500 dark:text-slate-400">
        <li class="inline-flex items-center">
            <a href="{{ route('admin.dashboard') ?? url('/admin') }}" class="inline-flex items-center gap-1.5 hover:text-slate-900 dark:hover:text-white transition-colors">
                <i data-lucide="layout-grid" class="w-3.5 h-3.5 text-slate-400"></i>
                <span class="hidden sm:inline font-medium">Dashboard</span>
            </a>
        </li>

        @foreach ($filteredItems as $item)
            <li class="inline-flex items-center gap-1.5 sm:gap-2">
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-300 dark:text-neutral-600 flex-shrink-0"></i>

                @if (!empty($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}" class="hover:text-slate-900 dark:hover:text-white font-medium transition-colors">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="font-semibold text-slate-900 dark:text-white" aria-current="page">
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
