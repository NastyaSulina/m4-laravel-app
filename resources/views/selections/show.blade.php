<x-base-layout>
    <h1>{{ $selection->title }}</h1>

    <p>Search type: {{ $selection->search_type }}</p>
    <p>Status: {{ $selection->status }}</p>
    @if ($selection->start_date)
        <p>Start date: {{ $selection->start_date }}</p>
    @endif
    @if ($selection->end_date)
        <p>End date: {{ $selection->end_date }}</p>
    @endif

    <a href="{{ route('selections.index') }}">Back to selections</a>

</x-base-layout>
