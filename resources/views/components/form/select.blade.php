@props([
    'name',
    'label',
    'options',
    'value' => '',
    'id' => $name,
    'required' => false,
])

<div class="flex flex-col gap-1">
    <label for="{{ $id }}" class="text-sm font-medium text-[#43263D]">
        {{ $label }}
        @if ($required)
            <span class="text-red-600" title="Required" aria-hidden="true">*</span>
        @else
            <span class="font-normal text-[#43263D]/60">(optional)</span>
        @endif
    </label>

    <div class="relative">
        <select
            id="{{ $id }}" name="{{ $name }}" @required($required)
            {{ $attributes->class([
                'w-full appearance-none rounded-md border bg-white bg-none py-2 pl-3 pr-10 text-sm text-[#43263D] focus:outline-2 focus:outline-[#43263D]',
                'border-red-500' => $errors->has($name),
                'border-[#D8C9EA]' => ! $errors->has($name),
            ]) }}
        >
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected(old($name, $value) == $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>
        <svg class="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-[#43263D]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </div>

    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
