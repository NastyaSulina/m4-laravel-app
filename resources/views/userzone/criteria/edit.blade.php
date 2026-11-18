<x-base-layout title="Edit criterion">
    <h1>Edit criterion</h1>

    <form action="{{ route('userzone.selections.criteria.update', [$selection, $criterion]) }}" method="post">
        @method('PUT')
        @csrf

        <x-form.textinput name="title" label="Title" :value="$criterion->title" minlength="3" maxlength="255" required />
        <x-form.checkbox name="is_required" label="Required" :checked="$criterion->is_required" />

        <button type="submit">Update criterion</button>
    </form>

    <a href="{{ route('userzone.selections.show', $selection) }}">Back to selection</a>
</x-base-layout>
