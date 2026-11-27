@props([
    'name',
    'label',
    'checked' => false,
    'id' => $name,
])

<div class="flex flex-wrap items-center gap-2.5">
    <input type="hidden" name="{{ $name }}" value="0">

    <input
        type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1" @checked(old($name, $checked))
        {{ $attributes->class(['size-4 cursor-pointer rounded border-line text-ink focus:ring-ink/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ink']) }}
    >

    <label for="{{ $id }}" class="cursor-pointer align-middle text-sm text-ink">{{ $label }}</label>

    @error($name)
        <p class="w-full text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
