@props(['status'])

@php
    $color = match ($status) {
        'active', 'selected' => 'bg-success-soft text-success',
        'considering' => 'bg-soft text-ink',
        'rejected' => 'bg-red-50 text-red-700',
        'archived' => 'text-muted ring-1 ring-inset ring-ink/20',
        default => 'bg-soft text-muted',
    };
@endphp

<span {{ $attributes->class(['inline-flex shrink-0 items-center rounded-full px-3 py-1 text-sm font-medium leading-tight', $color]) }}>{{ ucfirst($status) }}</span>
