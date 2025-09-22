@props([
    'variant' => 'default',
])

@php
    $baseClasses = 'rounded-lg shadow-md overflow-hidden transition-all duration-300';

    $variants = [
        'default' => 'bg-white hover:shadow-lg',
        'primary' => 'bg-blue-50 border border-blue-200 hover:shadow-lg',
        'secondary' => 'bg-gray-50 border border-gray-200 hover:shadow-lg',
    ];

    $classes = $baseClasses . ' ' . $variants[$variant];
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
