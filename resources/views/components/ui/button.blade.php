@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'danger' => false,
])

@php
    $attributes = $attributes->class([
        'inline-flex items-center justify-center cursor-pointer rounded-lg text-sm font-medium',
        'focus-visible:outline-2 focus-visible:outline-offset-4',
        $danger ? 'focus-visible:outline-red-700' : 'focus-visible:outline-teal-700',
        'border border-transparent px-4 py-2 text-white' => $variant === 'primary',
        ($danger ? 'bg-red-700 hover:bg-red-800' : 'bg-teal-700 hover:bg-teal-800') => $variant === 'primary',
        'border px-4 py-2' => $variant === 'secondary',
        ($danger ? 'border-red-700 text-red-700 hover:bg-red-50' : 'border-teal-700 text-teal-700 hover:bg-teal-50') => $variant === 'secondary',
        'underline underline-offset-4' => $variant === 'tertiary',
        ($danger ? 'text-red-700 hover:text-red-800' : 'text-teal-700 hover:text-teal-800') => $variant === 'tertiary',
    ]);
@endphp

@if ($href !== null)
    <a href="{{ $href }}" {{ $attributes }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes }}>{{ $slot }}</button>
@endif
