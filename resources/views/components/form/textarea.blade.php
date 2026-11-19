@props([
    'name',
    'label',
    'value' => '',
    'id' => $name,
    'required' => false,
])

<div class="flex flex-col gap-2">
    <label for="{{ $id }}" class="text-sm font-medium text-slate-700">
        {{ $label }}
        @if ($required)
            <span class="text-red-600" title="Required" aria-hidden="true">*</span>
        @else
            <span class="font-normal text-slate-500">(optional)</span>
        @endif
    </label>

    <textarea
        id="{{ $id }}" name="{{ $name }}" @required($required)
        {{ $attributes->class([
            'w-full resize-y rounded-md border bg-white px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:outline-2 focus:outline-teal-700',
            'border-red-500' => $errors->has($name),
            'border-slate-300' => ! $errors->has($name),
        ]) }}
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
