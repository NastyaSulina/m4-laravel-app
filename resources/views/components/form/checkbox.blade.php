@props([
    'name',
    'label',
    'checked' => false,
    'id' => $name,
])

<div>
    <input type="hidden" name="{{ $name }}" value="0">

    <input
        type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1" @checked(old($name, $checked))
        {{ $attributes->class(['size-4 cursor-pointer align-middle accent-[#43263D] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#43263D]']) }}
    >

    <label for="{{ $id }}" class="cursor-pointer align-middle text-sm text-[#43263D]">{{ $label }}</label>

    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
