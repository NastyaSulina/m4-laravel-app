@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'danger' => false,
])

@php
    $attributes = $attributes->class([
        'inline-flex items-center justify-center cursor-pointer rounded-xl text-base font-medium transition-colors',
        'focus-visible:outline-2 focus-visible:outline-offset-4',
        $danger ? 'focus-visible:outline-red-700' : 'focus-visible:outline-[#43263D]',
        'border border-transparent px-4 py-2 text-white' => $variant === 'primary',
        ($danger ? 'bg-red-700 hover:bg-red-800' : 'bg-[#43263D] hover:bg-[#43263D]/90') => $variant === 'primary',
        'border px-4 py-2' => $variant === 'secondary',
        ($danger ? 'border-red-700 text-red-700 hover:bg-red-50' : 'border-[#D8C9EA] text-[#43263D] hover:bg-[#D8C9EA]/30') => $variant === 'secondary',
        'underline underline-offset-4' => $variant === 'tertiary',
        ($danger ? 'text-red-700 hover:text-red-800' : 'text-[#43263D] hover:text-[#43263D]/75') => $variant === 'tertiary',
    ]);
@endphp

@if ($href !== null)
    <a href="{{ $href }}" {{ $attributes }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes }}>{{ $slot }}</button>
@endif
