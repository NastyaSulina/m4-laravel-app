@props([
    'name',
    'label',
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

    <input
        type="file" name="{{ $name }}" id="{{ $id }}" @required($required)
        {{ $attributes->class([
            'w-full min-w-0 cursor-pointer rounded-xl border bg-surface px-3.5 py-2.5 text-base text-ink focus:border-ink focus:ring-2 focus:ring-ink/10',
            'file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-soft file:px-4 file:py-1 file:text-sm file:font-medium file:text-ink hover:file:bg-line/70',
            'border-red-500' => $errors->has($name),
            'border-line' => ! $errors->has($name),
        ]) }}
    >

    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
