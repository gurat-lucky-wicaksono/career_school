@props(['name', 'class' => 'w-full h-auto text-neutral-900 dark:text-white'])

@php
    $path = public_path("images/illustrations/{$name}.svg");
    $svg = file_exists($path) ? file_get_contents($path) : '';
    // Inject class into root <svg> tag
    if ($svg && $class) {
        $svg = preg_replace('/^<svg /', '<svg class="' . e($class) . '" ', $svg, 1);
    }
@endphp

{!! $svg !!}
