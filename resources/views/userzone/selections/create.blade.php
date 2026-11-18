<x-base-layout title="Create selection">
    <h1>Create selection</h1>

    <form action="{{ route('userzone.selections.store') }}" method="post">
        @csrf

        <x-form.textinput name="title" label="Title" minlength="3" maxlength="255" required />
        <x-form.select
            name="search_type" label="Search type"
            :options="['' => 'Choose search type', 'short_stay' => 'Short stay', 'long_term' => 'Long-term rental']"
            required
        />
        <x-form.textinput name="start_date" label="Start date" type="date" />
        <x-form.textinput name="end_date" label="End date" type="date" />

        <button type="submit">Add selection</button>
    </form>

    <a href="{{ route('userzone.selections.index') }}">Back to selections</a>
</x-base-layout>
