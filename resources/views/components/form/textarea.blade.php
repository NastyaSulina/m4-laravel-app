@props([
    'name',
    'label',
    'value' => '',
    'id' => $name,
    'required' => false,
])

<div>
    <label for="{{ $id }}">
        {{ $label }}
        @if ($required)
            <span title="Required" aria-hidden="true">*</span>
        @else
            <span>(optional)</span>
        @endif
    </label>

    <textarea id="{{ $id }}" name="{{ $name }}" @required($required) {{ $attributes }}>{{ old($name, $value) }}</textarea>

    @error($name)
        <p>{{ $message }}</p>
    @enderror
</div>
