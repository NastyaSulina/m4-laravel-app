<x-base-layout title="Edit criterion">
    <h1>Edit criterion</h1>

    <form action="{{ route('userzone.selections.criteria.update', [$selection, $criterion]) }}" method="post">
        @method('PUT')
        @csrf

        <label for="title">Title</label>
        <input id="title" name="title" type="text" value="{{ old('title', $criterion->title) }}" maxlength="255" required>
        @error('title')
            <p>{{ $message }}</p>
        @enderror

        <input type="hidden" name="is_required" value="0">
        <label>
            <input type="checkbox" name="is_required" value="1" @checked(old('is_required', $criterion->is_required))>
            Required
        </label>
        @error('is_required')
            <p>{{ $message }}</p>
        @enderror

        <button type="submit">Update criterion</button>
    </form>

    <a href="{{ route('userzone.selections.show', $selection) }}">Back to selection</a>
</x-base-layout>
