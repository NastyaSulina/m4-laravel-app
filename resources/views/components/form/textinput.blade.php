@props([
    'name',
    'label',
    'value' => '',
    'id' => $name,
])

<div class="flex flex-col gap-2">
    <label for="{{ $id }}" class="text-sm font-medium text-slate-700">{{ $label }}</label>
    <input
        type="text" id="{{ $id }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        {{ $attributes->class([
            'w-full rounded-md border bg-white px-3 py-2 text-slate-900 placeholder:text-slate-400 focus:outline-2 focus:outline-teal-700',
            'border-red-500' => $errors->has($name),
            'border-slate-300' => ! $errors->has($name),
        ]) }}
    >
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
