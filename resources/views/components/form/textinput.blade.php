@props([
    'name',
    'label',
    'value' => '',
    'id' => $name,
])

<div>
    <label for="{{ $id }}">{{ $label }}</label>
    <input type="text" id="{{ $id }}" name="{{ $name }}" value="{{ old($name, $value) }}" {{ $attributes }}>
    @error($name)
        <p>{{ $message }}</p>
    @enderror
</div>
