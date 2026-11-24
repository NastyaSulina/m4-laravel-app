<x-base-layout title="Create selection" class="bg-[#F8F3E9]!">
    <div class="max-w-2xl text-[#43263D]">
        <x-ui.button :href="route('userzone.selections.index')" variant="tertiary" class="mb-6 gap-2 rounded-none! border-b border-current no-underline!"><span>←</span> Back to selections</x-ui.button>

        <h1 class="mb-6 font-serif text-3xl font-semibold leading-tight">Create selection</h1>

        <form action="{{ route('userzone.selections.store') }}" method="post" class="flex flex-col gap-4">
            @csrf

            <x-form.textinput name="title" label="Title" minlength="3" maxlength="255" required />
            <x-form.select
                name="search_type" label="Search type"
                :options="['' => 'Choose search type', 'short_stay' => 'Short stay', 'long_term' => 'Long-term rental']"
                required
            />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-form.textinput name="start_date" label="Check-in date" type="date" />
                <x-form.textinput name="end_date" label="Check-out date" type="date" />
            </div>

            <div class="flex flex-wrap items-center gap-4 pt-2">
                <x-ui.button type="submit" class="self-start">Add selection</x-ui.button>
                <x-ui.button :href="route('userzone.selections.index')" variant="tertiary">Cancel</x-ui.button>
            </div>
        </form>
    </div>
</x-base-layout>
