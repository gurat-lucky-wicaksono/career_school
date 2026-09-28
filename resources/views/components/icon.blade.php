@props(['name', 'class' => 'w-5 h-5'])

<i data-lucide="{{ $name }}" {{ $attributes->merge(['class' => 'inline-block ' . $class]) }}></i>
