@props([
    'title',
    'subtitle' => null,
    'searchPlaceholder' => 'Cari data...',
    'search' => null,
    'total' => null,
    'items' => null,
    'headers' => [], // array of ['key' => 'name', 'label' => 'Nama', 'sortable' => true]
    'currentSort' => null,
    'currentDirection' => 'asc',
])

<div class="space-y-4">
    <!-- Top Action Toolbar (Title, Search, Filter, Add Action) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Title & Subtitle / Selected Info -->
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white font-sans tracking-tight">
                {{ $title }}
            </h1>
            @if ($subtitle)
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        <!-- Controls: Search, Filter, Add Button -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Search Input Form with Auto-Reset on Empty -->
            <form method="GET" action="{{ $searchAction ?? request()->url() }}" data-table-search class="relative flex-1 sm:w-64">
                @foreach (request()->except(['search', 'page']) as $paramKey => $paramVal)
                    @if (is_array($paramVal))
                        @foreach ($paramVal as $subKey => $subVal)
                            <input type="hidden" name="{{ $paramKey }}[{{ $subKey }}]" value="{{ $subVal }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $paramKey }}" value="{{ $paramVal }}">
                    @endif
                @endforeach
                <i data-lucide="search" class="w-4 h-4 text-text-secondary/70 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input 
                    type="text" 
                    name="search"
                    value="{{ request('search', $search) }}"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full h-11 pl-10 pr-9 text-xs sm:text-sm rounded-full bg-surface-bg border border-border-default hover:border-text-secondary/70 focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/20 text-text-primary placeholder:text-text-secondary/50 transition-all outline-none shadow-xs font-normal"
                    onkeydown="if(event.key === 'Enter' && !this.value.trim()){ event.preventDefault(); this.disabled = true; this.form.submit(); }"
                    oninput="if(!this.value.trim() && new URLSearchParams(window.location.search).has('search')){ this.disabled = true; this.form.submit(); }"
                >
                @if (request('search'))
                    <a 
                        href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => 1]) }}" 
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-text-secondary hover:text-text-primary transition-colors"
                        title="Hapus Pencarian"
                    >
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </form>

            <!-- Filter Trigger Slot -->
            @isset($filter)
                {{ $filter }}
            @endisset

            <!-- Primary Add / Action Button Slot -->
            @isset($action)
                {{ $action }}
            @endisset
        </div>
    </div>

    <!-- Bulk Selection Banner (Conditional) -->
    @isset($bulk)
        <div class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-neutral-800 border border-slate-200 dark:border-white/10 flex items-center justify-between gap-3 text-xs">
            {{ $bulk }}
        </div>
    @endisset

    <!-- Main Table Container (Google Drive List View Aesthetic) -->
    <div class="bg-surface-card border border-slate-200/80 dark:border-white/10 rounded-2xl shadow-sm overflow-hidden transition-colors">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <!-- Table Header -->
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-white/10 bg-slate-50/50 dark:bg-neutral-900/30 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider select-none">
                        @isset($checkboxAll)
                            <th class="w-12 px-4 py-3.5 text-center">
                                {{ $checkboxAll }}
                            </th>
                        @endisset

                        @foreach ($headers as $header)
                            @php
                                $sortable = $header['sortable'] ?? false;
                                $key = $header['key'] ?? '';
                                $isSorted = $currentSort === $key;
                            @endphp

                            <th class="px-5 py-3.5 {{ $header['class'] ?? '' }}">
                                @if ($sortable)
                                    <a 
                                        href="{{ request()->fullUrlWithQuery(['sort' => $key, 'direction' => ($isSorted && $currentDirection === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}"
                                        class="inline-flex items-center gap-1.5 hover:text-slate-900 dark:hover:text-white transition-colors uppercase font-bold text-[11px]"
                                    >
                                        <span>{{ $header['label'] }}</span>
                                        <i data-lucide="{{ $isSorted ? ($currentDirection === 'desc' ? 'arrow-down' : 'arrow-up') : 'arrow-up-down' }}" class="w-3.5 h-3.5 {{ $isSorted ? 'text-brand-secondary dark:text-brand-primary' : 'text-slate-300 dark:text-neutral-600' }}"></i>
                                    </a>
                                @else
                                    <span>{{ $header['label'] }}</span>
                                @endif
                            </th>
                        @endforeach

                        @isset($actionHeader)
                            <th class="px-5 py-3.5 text-right w-28">
                                {{ $actionHeader }}
                            </th>
                        @else
                            <th class="px-5 py-3.5 text-right w-28">
                                Aksi
                            </th>
                        @endisset
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-xs sm:text-sm text-slate-700 dark:text-slate-200">
                    {{ $slot }}
                </tbody>
            </table>
        </div>

        <!-- Bottom Toolbar & Pagination (Per-page + Links) -->
        @if ($items && method_exists($items, 'hasPages') && ($items->hasPages() || $items->total() > 0))
            <div class="px-5 py-3.5 border-t border-slate-100 dark:border-white/5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-2">
                    <span>Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} data</span>
                </div>

                <div class="flex items-center">
                    {{ $items->links() }}
                </div>
            </div>
        @elseif(isset($pagination))
            <div class="px-5 py-3.5 border-t border-slate-100 dark:border-white/5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
                {{ $pagination }}
            </div>
        @endif
    </div>
</div>
