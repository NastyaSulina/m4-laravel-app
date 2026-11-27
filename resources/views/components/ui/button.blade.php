@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'danger' => false,
])

@php
    $attributes = $attributes->class([
        'inline-flex items-center justify-center cursor-pointer rounded-xl text-sm font-medium transition-colors',
        'focus-visible:outline-2 focus-visible:outline-offset-4',
        $danger ? 'focus-visible:outline-red-700' : 'focus-visible:outline-ink',
        'px-4 py-2.5 text-white' => $variant === 'primary',
        ($danger ? 'bg-red-700 hover:bg-red-800' : 'bg-ink hover:bg-ink/90') => $variant === 'primary',
        'px-4 py-2.5' => $variant === 'secondary',
        ($danger ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-soft text-ink hover:bg-line/70') => $variant === 'secondary',
        'underline-offset-4 hover:underline' => $variant === 'tertiary',
        ($danger ? 'text-red-700 hover:text-red-700' : 'text-ink hover:text-muted') => $variant === 'tertiary',
    ]);
@endphp

@if ($href !== null)
    <a href="{{ $href }}" {{ $attributes }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes }}>{{ $slot }}</button>
@endif
