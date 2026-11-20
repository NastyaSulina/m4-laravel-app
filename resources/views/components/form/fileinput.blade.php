@props([
    'name',
    'label',
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
        type="file" name="{{ $name }}" id="{{ $id }}" @required($required)
        {{ $attributes->class([
            'w-full min-w-0 cursor-pointer rounded-md border bg-white px-3 py-2 text-sm text-[#43263D] focus:outline-2 focus:outline-[#43263D]',
            'file:mr-3 file:cursor-pointer file:rounded-lg file:border file:border-[#D8C9EA] file:bg-white file:px-4 file:py-1 file:text-sm file:font-medium file:text-[#43263D] hover:file:bg-[#D8C9EA]/30',
            'border-red-500' => $errors->has($name),
            'border-[#D8C9EA]' => ! $errors->has($name),
        ]) }}
    >

    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
