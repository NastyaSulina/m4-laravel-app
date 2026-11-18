@props([
    'name',
    'label',
    'options',
    'value' => '',
    'id' => $name,
])

<div>
    <label for="{{ $id }}">{{ $label }}</label>
    <select id="{{ $id }}" name="{{ $name }}" {{ $attributes }}>
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected(old($name, $value) == $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @error($name)
        <p>{{ $message }}</p>
    @enderror
</div>
