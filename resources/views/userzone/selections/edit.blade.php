<x-base-layout title="Edit selection">
    <h1>Edit selection</h1>

    <form action="{{ route('userzone.selections.update', $selection) }}" method="post">
        @method('PUT')
        @csrf

        <x-form.textinput name="title" label="Title" :value="$selection->title" minlength="3" maxlength="255" required />
        <x-form.select
            name="search_type" label="Search type" :value="$selection->search_type"
            :options="['' => 'Choose search type', 'short_stay' => 'Short stay', 'long_term' => 'Long-term rental']"
            required
        />
        <x-form.textinput name="start_date" label="Start date" type="date" :value="$selection->start_date" />
        <x-form.textinput name="end_date" label="End date" type="date" :value="$selection->end_date" />

        <button type="submit">Update selection</button>
    </form>

    <a href="{{ route('userzone.selections.show', $selection) }}">Back to selection</a>
</x-base-layout>
