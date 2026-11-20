<x-base-layout title="Create selection" class="bg-[#F8F3E9]!">
    <div class="mx-auto max-w-2xl text-[#43263D]">
        <h1 class="mb-6 font-serif text-3xl font-semibold">Create selection</h1>

        <form action="{{ route('userzone.selections.store') }}" method="post" class="flex flex-col gap-4">
            @csrf

            <x-form.textinput name="title" label="Title" minlength="3" maxlength="255" required />
            <x-form.select
                name="search_type" label="Search type"
                :options="['' => 'Choose search type', 'short_stay' => 'Short stay', 'long_term' => 'Long-term rental']"
                required
            />
            <div class="grid grid-cols-2 gap-4">
                <x-form.textinput name="start_date" label="Start date" type="date" />
                <x-form.textinput name="end_date" label="End date" type="date" />
            </div>

            <x-ui.button type="submit" class="self-start">Add selection</x-ui.button>
        </form>

        <x-ui.button :href="route('userzone.selections.index')" variant="tertiary" class="mt-5">Back to selections</x-ui.button>
    </div>
</x-base-layout>
