<x-base-layout title="Edit accommodation" class="bg-[#F8F3E9]!">
    <div class="mx-auto max-w-2xl text-[#43263D]">
        <h1 class="mb-6 font-serif text-3xl font-semibold">Edit accommodation</h1>

        <form action="{{ route('userzone.selections.accommodations.update', [$selection, $accommodation]) }}" method="post" enctype="multipart/form-data" class="flex flex-col gap-4">
            @method('PUT')
            @csrf

            <x-form.textinput name="title" :value="$accommodation->title" label="Title" minlength="3" maxlength="255" required />
            <x-form.textinput name="source_url" :value="$accommodation->source_url" label="Source URL" type="url" maxlength="2048" required />
            <x-form.textinput name="address" :value="$accommodation->address" label="Address" maxlength="255" required />
            <div class="grid grid-cols-2 gap-4">
                <x-form.select
                    name="status" label="Status" :value="$accommodation->status"
                    :options="['considering' => 'Considering', 'shortlisted' => 'Shortlisted', 'selected' => 'Selected', 'rejected' => 'Rejected', 'unavailable' => 'Unavailable']"
                    required
                />
                <x-form.select
                    name="property_type" :value="$accommodation->property_type" label="Property type"
                    :options="['' => 'Choose property type', 'apartment' => 'Apartment', 'house' => 'House', 'room' => 'Room', 'hotel' => 'Hotel']"
                    required
                />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <x-form.textarea name="note" :value="$accommodation->note" label="Note" rows="3" maxlength="5000" />
                <div class="flex min-w-0 flex-col gap-2">
                    <img
                        src="{{ $accommodation->getFirstMediaUrl('images', 'thumbnail') }}" alt="{{ $accommodation->title }}"
                        width="128" height="96" class="h-16 w-20 rounded-lg border border-[#D8C9EA] bg-[#D8C9EA]/20 object-cover"
                    >
                    <x-form.fileinput name="image" label="Image" accept="image/*" />
                </div>
            </div>

            <x-ui.button type="submit" class="self-start">Update accommodation</x-ui.button>
        </form>

        <x-ui.button :href="route('userzone.selections.accommodations.show', [$selection, $accommodation])" variant="tertiary" class="mt-5">Back to accommodation</x-ui.button>
    </div>
</x-base-layout>
