<x-base-layout :title="$accommodation->title">
    <article class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-6 sm:p-8">
        <p class="text-sm text-slate-500">Added by {{ $accommodation->user->name }}</p>
        <h1 class="break-words text-3xl font-semibold tracking-tight text-slate-900">{{ $accommodation->title }}</h1>

        <p>Address: {{ $accommodation->address }}</p>
        <p>Status: {{ $accommodation->status }}</p>
        <p class="break-all">Source: {{ $accommodation->source_url }}</p>
        @if ($accommodation->property_type)
            <p>Property type: {{ $accommodation->property_type }}</p>
        @endif
        @if ($accommodation->note)
            <p class="whitespace-pre-line break-words">Note: {{ $accommodation->note }}</p>
        @endif

        <x-ui.button
            :href="route('userzone.selections.show', $accommodation->selection_id)"
            variant="tertiary" class="mt-4 self-start"
        >Back to selection</x-ui.button>
    </article>
</x-base-layout>
