@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'id' => $name,
    'required' => false,
])

<div class="flex flex-col gap-1">
    <label for="{{ $id }}" class="text-sm font-medium text-[#43263D]">
        {{ $label }}
        @if ($required)
            <span class="text-red-600" title="Required" aria-hidden="true">*</span>
        @else
            <span class="font-normal text-[#43263D]/60">(optional)</span>
        @endif
    </label>

    <input
        type="{{ $type }}" id="{{ $id }}" name="{{ $name }}" value="{{ old($name, $value) }}" @required($required)
        {{ $attributes->class([
            'w-full rounded-md border bg-white px-3 py-2 text-sm text-[#43263D] placeholder:text-slate-400 focus:outline-2 focus:outline-[#43263D]',
            'border-red-500' => $errors->has($name),
            'border-[#D8C9EA]' => ! $errors->has($name),
        ]) }}
    >

    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
