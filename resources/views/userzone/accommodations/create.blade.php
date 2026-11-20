<x-base-layout title="Create accommodation" class="bg-[#F8F3E9]!">
    <div class="mx-auto max-w-2xl text-[#43263D]">
        <h1 class="mb-6 font-serif text-3xl font-semibold">Create accommodation</h1>

        <form action="{{ route('userzone.selections.accommodations.store', $selection) }}" method="post" enctype="multipart/form-data" class="flex flex-col gap-4">
            @csrf

            <x-form.textinput name="title" label="Title" minlength="3" maxlength="255" required />
            <x-form.textinput name="source_url" label="Source URL" type="url" maxlength="2048" required />
            <x-form.textinput name="address" label="Address" maxlength="255" required />
            <div class="grid grid-cols-2 gap-4">
                <x-form.select
                    name="status" label="Status" value="considering"
                    :options="['considering' => 'Considering', 'shortlisted' => 'Shortlisted', 'selected' => 'Selected', 'rejected' => 'Rejected', 'unavailable' => 'Unavailable']"
                    required
                />
                <x-form.select
                    name="property_type" label="Property type"
                    :options="['' => 'Choose property type', 'apartment' => 'Apartment', 'house' => 'House', 'room' => 'Room', 'hotel' => 'Hotel']"
                    required
                />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <x-form.textarea name="note" label="Note" rows="3" maxlength="5000" />
                <div class="flex min-w-0 flex-col gap-2">
                    <x-form.fileinput name="image" label="Image" accept="image/*" />
                </div>
            </div>

            <x-ui.button type="submit" class="self-start">Add accommodation</x-ui.button>
        </form>

        <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary" class="mt-5">Back to selection</x-ui.button>
    </div>
</x-base-layout>
