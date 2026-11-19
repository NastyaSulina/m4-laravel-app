@props([
    'name',
    'label',
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

    <input
        type="file" name="{{ $name }}" id="{{ $id }}" @required($required)
        {{ $attributes->class([
            'w-full min-w-0 cursor-pointer rounded-md border bg-white px-3 py-2 text-sm text-slate-900 focus:outline-2 focus:outline-teal-700',
            'file:mr-3 file:cursor-pointer file:rounded-lg file:border file:border-teal-700 file:bg-white file:px-4 file:py-2 file:text-sm file:font-medium file:text-teal-700 hover:file:bg-teal-50',
            'border-red-500' => $errors->has($name),
            'border-slate-300' => ! $errors->has($name),
        ]) }}
    >

    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
