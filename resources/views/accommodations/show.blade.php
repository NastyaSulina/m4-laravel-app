<x-base-layout :title="$accommodation->title">
    <article>
        <h1>{{ $accommodation->title }}</h1>

        <p>Address: {{ $accommodation->address }}</p>
        <p>Status: {{ $accommodation->status }}</p>
        <p>Source: {{ $accommodation->source_url }}</p>
        @if ($accommodation->property_type)
            <p>Property type: {{ $accommodation->property_type }}</p>
        @endif
        @if ($accommodation->note)
            <p>Note: {{ $accommodation->note }}</p>
        @endif

        <a
            href="{{ route('selections.show', $accommodation->selection_id) }}"
        >
            &larr; Back to selection
        </a>
    </article>
</x-base-layout>
