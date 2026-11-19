<x-base-layout title="Edit accommodation">
    <h1 class="mb-6 text-3xl font-semibold tracking-tight text-slate-900">Edit accommodation</h1>

    <form action="{{ route('userzone.selections.accommodations.update', [$selection, $accommodation]) }}" method="post" enctype="multipart/form-data" class="flex max-w-lg flex-col gap-5">
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
        <img
            src="{{ $accommodation->getFirstMediaUrl('images', 'thumbnail') }}" alt="{{ $accommodation->title }}"
            width="128" height="96" class="h-24 w-32 rounded-lg border border-slate-200 bg-slate-50 object-cover"
        >
        <x-form.fileinput name="image" label="Image" accept="image/*" />

        <x-ui.button type="submit" class="self-start">Update accommodation</x-ui.button>
    </form>

    <x-ui.button :href="route('userzone.selections.accommodations.show', [$selection, $accommodation])" variant="tertiary" class="mt-5">Back to accommodation</x-ui.button>
</x-base-layout>
