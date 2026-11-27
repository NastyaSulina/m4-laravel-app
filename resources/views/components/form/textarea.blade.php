@props([
    'name',
    'label',
    'value' => '',
    'id' => $name,
    'required' => false,
])

<div class="flex flex-col gap-2">
    <label for="{{ $id }}" class="text-sm font-medium text-ink">
        {{ $label }}
        @if ($required)
            <span class="text-red-600" title="Required" aria-hidden="true">*</span>
        @else
            <span class="font-normal text-muted">(optional)</span>
        @endif
    </label>

    <textarea
        id="{{ $id }}" name="{{ $name }}" @required($required)
        {{ $attributes->class([
            'w-full resize-y rounded-xl border bg-surface px-3.5 py-2.5 text-base text-ink placeholder:text-muted focus:border-ink focus:ring-2 focus:ring-ink/10',
            'border-red-500' => $errors->has($name),
            'border-line' => ! $errors->has($name),
        ]) }}
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
