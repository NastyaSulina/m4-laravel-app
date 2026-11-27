@props([
    'name',
    'label',
    'options',
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

    <div class="relative">
        <select
            id="{{ $id }}" name="{{ $name }}" @required($required)
            {{ $attributes->class([
                'w-full appearance-none rounded-xl border bg-surface bg-none py-2.5 pl-3.5 pr-10 text-base text-ink focus:border-ink focus:ring-2 focus:ring-ink/10',
                'border-red-500' => $errors->has($name),
                'border-line' => ! $errors->has($name),
            ]) }}
        >
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected(old($name, $value) == $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>
        <svg class="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </div>

    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
