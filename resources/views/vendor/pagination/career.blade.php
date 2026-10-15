@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1.5 font-sans">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-xl border border-slate-200/80 dark:border-white/10 bg-surface-card text-slate-400 dark:text-neutral-600 opacity-50 cursor-not-allowed select-none">
                <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                <span>Sebelumnya</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-xl border border-slate-200/80 dark:border-white/10 bg-surface-card text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-neutral-800 transition-colors shadow-sm select-none">
                <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                <span>Sebelumnya</span>
            </a>
        @endif

        {{-- Pagination Elements --}}
        <div class="hidden sm:flex items-center gap-1">
            @foreach ($elements as $element)
                {{-- Three Dots Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true" class="px-2 py-1.5 text-xs font-semibold text-slate-400 dark:text-slate-500">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="w-8 h-8 flex items-center justify-center text-xs font-bold rounded-xl bg-brand-primary text-neutral-900 shadow-sm select-none">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center text-xs font-semibold rounded-xl border border-slate-200/80 dark:border-white/10 bg-surface-card text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-neutral-800 transition-colors shadow-sm select-none">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-xl border border-slate-200/80 dark:border-white/10 bg-surface-card text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-neutral-800 transition-colors shadow-sm select-none">
                <span>Berikutnya</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </a>
        @else
            <span aria-disabled="true" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-xl border border-slate-200/80 dark:border-white/10 bg-surface-card text-slate-400 dark:text-neutral-600 opacity-50 cursor-not-allowed select-none">
                <span>Berikutnya</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
            </span>
        @endif
    </nav>
@endif
