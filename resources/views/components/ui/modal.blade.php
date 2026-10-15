@props([
    'id',
    'title' => null,
    'subtitle' => null,
    'size' => 'lg', // sm, md, lg, xl
    'hideHeader' => false,
    'overflow' => 'auto', // 'auto' | 'visible'
])

@php
    $sizeClasses = [
        'sm' => 'max-w-md',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        'full' => 'max-w-6xl',
    ][$size] ?? 'max-w-2xl';
@endphp

<div 
    id="{{ $id }}" 
    class="fixed inset-0 z-50 hidden overflow-y-auto"
    role="dialog" 
    aria-modal="true"
    aria-labelledby="{{ $id }}-title"
>
    <!-- Dark Backdrop with Smooth Blur -->
    <div 
        class="fixed inset-0 bg-neutral-950/75 backdrop-blur-sm transition-opacity duration-200"
        onclick="closeModal('{{ $id }}')"
    ></div>

    <!-- Modal Dialog Positioner -->
    <div 
        class="min-h-screen px-4 text-center flex items-center justify-center py-6 sm:py-10"
        onclick="if (event.target === this) closeModal('{{ $id }}')"
    >
        <!-- Modal Card Container -->
        <div 
            class="relative inline-block w-full {{ $sizeClasses }} bg-surface-card border border-slate-200/90 dark:border-white/10 rounded-2xl text-left shadow-2xl transform transition-all font-sans my-auto {{ $overflow === 'visible' ? 'overflow-visible' : '' }}"
        >
            @unless($hideHeader)
                <!-- Header Toolbar -->
                <div class="px-6 py-5 border-b border-slate-100 dark:border-white/5 flex items-center justify-between gap-4 bg-slate-50/50 dark:bg-neutral-900/40 rounded-t-2xl">
                    <div>
                        @if ($title)
                            <h3 id="{{ $id }}-title" class="text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                                {{ $title }}
                            </h3>
                        @endif
                        @if ($subtitle)
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ $subtitle }}
                            </p>
                        @endif
                    </div>

                    <button 
                        type="button" 
                        onclick="closeModal('{{ $id }}')"
                        class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-neutral-800 transition-colors focus:outline-none"
                        aria-label="Tutup Jendela"
                    >
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            @endunless

            <!-- Body Content -->
            <div class="p-6 {{ $overflow === 'visible' ? 'overflow-visible' : 'max-h-[calc(88vh-8rem)] overflow-y-auto custom-scrollbar' }} {{ !isset($footer) ? 'rounded-b-2xl' : '' }}">
                {{ $slot }}
            </div>

            @isset($footer)
                <!-- Footer Toolbar -->
                <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-neutral-900/40 flex items-center justify-end gap-3 rounded-b-2xl">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
