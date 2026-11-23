@props(['status'])

@php
    $color = match ($status) {
        'active', 'selected' => 'border-transparent bg-[#DAE88C]',
        'considering' => 'border-transparent bg-[#D8C9EA]',
        'rejected' => 'border-red-700 bg-[#F8F3E9] text-red-700',
        default => 'border-[#43263D]/30',
    };
@endphp

<span {{ $attributes->class(['inline-flex shrink-0 items-center rounded-full border px-3 py-1.5 text-sm font-medium leading-tight', $color]) }}>{{ ucfirst($status) }}</span>
