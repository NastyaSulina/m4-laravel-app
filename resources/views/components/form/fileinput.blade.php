@props([
    'name',
    'label',
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

    <input
        type="file" name="{{ $name }}" id="{{ $id }}" @required($required)
        {{ $attributes }}
    >

    @error($name)
        <p>{{ $message }}</p>
    @enderror
</div>
