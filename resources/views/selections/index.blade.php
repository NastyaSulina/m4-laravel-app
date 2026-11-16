<x-base-layout title="Selections">
    <h1>Selections</h1>

    @forelse ($selections as $selection)
        <div>
            <h2>{{ $selection->title }}</h2>
            <p>Search type: {{ $selection->search_type }}</p>
            <p>Status: {{ $selection->status }}</p>
            @if ($selection->start_date)
                <p>Start date: {{ $selection->start_date }}</p>
            @endif
            @if ($selection->end_date)
                <p>End date: {{ $selection->end_date }}</p>
            @endif
            <a href="{{ route('selections.show', $selection) }}">View selection</a>
        </div>
    @empty
        <p>No selections yet.</p>
    @endforelse
</x-base-layout>
