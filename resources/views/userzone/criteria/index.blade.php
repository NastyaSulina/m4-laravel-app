<x-base-layout :title="'Criteria — '.$selection->title">
    <h1>Criteria</h1>
    <p>{{ $selection->title }}</p>
    <a href="{{ route('userzone.selections.show', $selection) }}">
        Back to selection
    </a>

    <ul>
        @forelse ($criteria as $criterion)
            <li>
                <span>{{ $criterion->title }}</span>
                <span>{{ $criterion->is_required ? 'Required' : 'Optional' }}</span>
                <a href="{{ route('userzone.selections.criteria.edit', [$selection, $criterion]) }}">Edit</a>
                <form action="{{ route('userzone.selections.criteria.destroy', [$selection, $criterion]) }}" method="post">
                    @method('delete')
                    @csrf
                    <button type="submit">Delete</button>
                </form>
            </li>
        @empty
            <li>No criteria yet.</li>
        @endforelse
    </ul>

    <h2>Add criterion</h2>
    <form action="{{ route('userzone.selections.criteria.store', $selection) }}" method="post">
        @csrf
        <x-form.textinput name="title" label="Title" minlength="3" maxlength="255" required />
        <x-form.checkbox name="is_required" label="Required" />
        <button type="submit">
            Add criterion
        </button>
    </form>
</x-base-layout>
