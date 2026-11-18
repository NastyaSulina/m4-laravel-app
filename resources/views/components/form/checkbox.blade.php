@props([
    'name',
    'label',
    'checked' => false,
    'id' => $name,
])

<div>
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1" @checked(old($name, $checked)) {{ $attributes }}>
    <label for="{{ $id }}">{{ $label }}</label>
    @error($name)
        <p>{{ $message }}</p>
    @enderror
</div>
