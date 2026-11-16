<x-base-layout :title="$accommodation->title">
    <article class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-6 sm:p-8">
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

        <a
            href="{{ route('selections.show', $accommodation->selection_id) }}"
            class="mt-4 self-start rounded-sm font-medium text-teal-700 underline-offset-4 hover:underline
                   focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
        >
            &larr; Back to selection
        </a>
    </article>
</x-base-layout>
