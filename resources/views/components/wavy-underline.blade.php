<span {{ $attributes->merge(['class' => 'relative inline-block whitespace-nowrap']) }}>
    <span class="relative z-10">{{ $slot }}</span>
    <svg class="absolute -bottom-2 left-0 w-full h-3 sm:h-3.5 text-brand-primary pointer-events-none" viewBox="0 0 120 16" preserveAspectRatio="none" fill="none">
        <path d="M2 10 Q 15 2, 30 10 T 60 10 T 90 10 T 118 10" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</span>
