<x-base-layout title="Edit selection" class="bg-[#F8F3E9]!">
    <div class="max-w-2xl text-[#43263D]">
        <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary" class="mb-6 gap-2 rounded-none! border-b border-current no-underline!"><span>←</span> Back to selection</x-ui.button>

        <h1 class="mb-6 font-serif text-3xl font-semibold leading-tight">Edit selection</h1>

        <form action="{{ route('userzone.selections.update', $selection) }}" method="post" class="flex flex-col gap-4">
            @method('PUT')
            @csrf

            <x-form.textinput name="title" label="Title" :value="$selection->title" minlength="3" maxlength="255" required />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
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
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-form.textinput name="start_date" label="Check-in date" type="date" :value="$selection->start_date" />
                <x-form.textinput name="end_date" label="Check-out date" type="date" :value="$selection->end_date" />
            </div>

            <div class="flex flex-wrap items-center gap-4 pt-2">
                <x-ui.button type="submit" class="self-start">Update selection</x-ui.button>
                <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary">Cancel</x-ui.button>
            </div>
        </form>
    </div>
</x-base-layout>
