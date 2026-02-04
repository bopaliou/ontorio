@props([
    'variant' => 'text', // circle, text, rect, table-row
    'width' => 'w-full',
    'height' => 'h-4',
    'class' => ''
])

@php
    $baseClasses = "animate-pulse bg-gray-200 dark:bg-gray-700 rounded transition-all duration-300";

    $variantClasses = [
        'circle' => 'rounded-full',
        'text' => 'rounded h-3',
        'rect' => 'rounded-xl',
        'table-row' => 'rounded h-12 w-full mb-2',
    ][$variant] ?? '';
@endphp

<div class="{{ $baseClasses }} {{ $variantClasses }} {{ $width }} {{ $height }} {{ $class }}" aria-hidden="true"></div>
