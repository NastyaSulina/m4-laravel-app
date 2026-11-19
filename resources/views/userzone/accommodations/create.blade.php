<x-base-layout title="Create accommodation">
    <h1>Create accommodation</h1>

    <form action="{{ route('userzone.selections.accommodations.store', $selection) }}" method="post">
        @csrf

        <x-form.textinput name="title" label="Title" minlength="3" maxlength="255" required />
        <x-form.textinput name="source_url" label="Source URL" type="url" maxlength="2048" required />
        <x-form.textinput name="address" label="Address" maxlength="255" required />
        <x-form.select
            name="status" label="Status" value="considering"
            :options="['considering' => 'Considering', 'shortlisted' => 'Shortlisted', 'selected' => 'Selected', 'rejected' => 'Rejected', 'unavailable' => 'Unavailable']"
            required
        />
        <x-form.select
            name="property_type" label="Property type"
            :options="['' => 'Choose property type', 'apartment' => 'Apartment', 'house' => 'House', 'room' => 'Room', 'hotel' => 'Hotel']"
        />
        <x-form.textarea name="note" label="Note" rows="5" maxlength="5000" />

        <x-ui.button type="submit">Add accommodation</x-ui.button>
    </form>

    <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary">Back to selection</x-ui.button>
</x-base-layout>
