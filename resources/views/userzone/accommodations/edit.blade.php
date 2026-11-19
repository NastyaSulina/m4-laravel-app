<x-base-layout title="Edit accommodation">
    <h1>Edit accommodation</h1>

    <form action="{{ route('userzone.selections.accommodations.update', [$selection, $accommodation]) }}" method="post" enctype="multipart/form-data">
        @method('PUT')
        @csrf

        <x-form.textinput name="title" :value="$accommodation->title" label="Title" minlength="3" maxlength="255" required />
        <x-form.textinput name="source_url" :value="$accommodation->source_url" label="Source URL" type="url" maxlength="2048" required />
        <x-form.textinput name="address" :value="$accommodation->address" label="Address" maxlength="255" required />
        <x-form.select
            name="status" label="Status" :value="$accommodation->status"
            :options="['considering' => 'Considering', 'shortlisted' => 'Shortlisted', 'selected' => 'Selected', 'rejected' => 'Rejected', 'unavailable' => 'Unavailable']"
            required
        />
        <x-form.select
            name="property_type" :value="$accommodation->property_type" label="Property type"
            :options="['' => 'Choose property type', 'apartment' => 'Apartment', 'house' => 'House', 'room' => 'Room', 'hotel' => 'Hotel']"
        />
        <x-form.textarea name="note" :value="$accommodation->note" label="Note" rows="5" maxlength="5000" />
        <x-form.fileinput name="image" label="Image" accept="image/*" />

        <x-ui.button type="submit">Update accommodation</x-ui.button>
    </form>

    <x-ui.button :href="route('userzone.selections.accommodations.show', [$selection, $accommodation])" variant="tertiary">Back to accommodation</x-ui.button>
</x-base-layout>
