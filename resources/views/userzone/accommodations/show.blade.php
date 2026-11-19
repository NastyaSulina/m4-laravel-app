<x-base-layout :title="$accommodation->title">
    <article class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-6 sm:p-8">
        <p class="text-sm text-slate-500">Added by {{ $accommodation->user->name }}</p>
        <h1 class="break-words text-3xl font-semibold tracking-tight text-slate-900">{{ $accommodation->title }}</h1>

        <x-ui.button
            :href="route('userzone.selections.accommodations.edit', [$accommodation->selection_id, $accommodation])"
            variant="secondary" class="self-start"
        >Edit</x-ui.button>

        <img
            src="{{ $accommodation->getFirstMediaUrl('images') }}" alt="{{ $accommodation->title }}"
            class="h-auto max-h-96 w-full rounded-lg border border-slate-200 bg-slate-50 object-contain"
        >

        <p>Address: {{ $accommodation->address }}</p>
        <p>Status: {{ $accommodation->status }}</p>
        <p class="break-all">Source: {{ $accommodation->source_url }}</p>
        <p>Property type: {{ $accommodation->property_type }}</p>
        @if ($accommodation->note)
            <p class="whitespace-pre-line break-words">Note: {{ $accommodation->note }}</p>
        @endif

        <x-ui.button
            :href="route('userzone.selections.show', $accommodation->selection_id)"
            variant="tertiary" class="mt-4 self-start"
        >Back to selection</x-ui.button>
    </article>
</x-base-layout>
