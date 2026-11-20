<x-base-layout title="Edit selection" class="bg-[#F8F3E9]!">
    <div class="mx-auto max-w-2xl text-[#43263D]">
        <h1 class="mb-6 font-serif text-3xl font-semibold">Edit selection</h1>

        <form action="{{ route('userzone.selections.update', $selection) }}" method="post" class="flex flex-col gap-4">
            @method('PUT')
            @csrf

            <x-form.textinput name="title" label="Title" :value="$selection->title" minlength="3" maxlength="255" required />
            <div class="grid grid-cols-2 gap-4">
                <x-form.select
                    name="search_type" label="Search type" :value="$selection->search_type"
                    :options="['' => 'Choose search type', 'short_stay' => 'Short stay', 'long_term' => 'Long-term rental']"
                    required
                />
                <x-form.select
                    name="status" label="Status" :value="$selection->status"
                    :options="['active' => 'Active', 'archived' => 'Archived']"
                    required
                />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <x-form.textinput name="start_date" label="Start date" type="date" :value="$selection->start_date" />
                <x-form.textinput name="end_date" label="End date" type="date" :value="$selection->end_date" />
            </div>

            <x-ui.button type="submit" class="self-start">Update selection</x-ui.button>
        </form>

        <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary" class="mt-5">Back to selection</x-ui.button>
    </div>
</x-base-layout>
