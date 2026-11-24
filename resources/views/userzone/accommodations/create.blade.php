<x-base-layout title="Create accommodation" class="bg-[#F8F3E9]!">
    <div class="max-w-2xl text-[#43263D]">
        <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary" class="mb-6 gap-2 rounded-none! border-b border-current no-underline!"><span>←</span> Back to selection</x-ui.button>

        <h1 class="mb-2 font-serif text-3xl font-semibold leading-tight">Create accommodation</h1>
        <p class="mb-6 break-words text-sm leading-relaxed text-[#43263D]/75">In {{ $selection->title }}</p>

        <form action="{{ route('userzone.selections.accommodations.store', $selection) }}" method="post" enctype="multipart/form-data" class="flex flex-col gap-4">
            @csrf

            <x-form.textinput name="title" label="Title" minlength="3" maxlength="255" required />
            <x-form.textinput name="source_url" label="Source URL" type="url" maxlength="2048" required />
            <x-form.textinput name="address" label="Address" maxlength="255" required />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-form.select
                    name="status" label="Status" value="considering"
                    :options="['considering' => 'Considering', 'selected' => 'Selected', 'rejected' => 'Rejected']"
                    required
                />
                <x-form.select
                    name="property_type" label="Property type"
                    :options="['' => 'Choose property type', 'apartment' => 'Apartment', 'house' => 'House', 'room' => 'Room', 'hotel' => 'Hotel']"
                    required
                />
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-form.textarea name="note" label="Note" rows="3" maxlength="5000" />
                <div class="flex min-w-0 flex-col gap-2">
                    <x-form.fileinput name="image" label="Image" accept="image/*" />
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4 pt-2">
                <x-ui.button type="submit" class="self-start">Add accommodation</x-ui.button>
                <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary">Cancel</x-ui.button>
            </div>
        </form>
    </div>
</x-base-layout>
