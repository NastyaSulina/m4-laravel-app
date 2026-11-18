<x-base-layout title="Create selection">
    <h1 class="mb-6 text-3xl font-semibold tracking-tight text-slate-900">Create selection</h1>

    <form action="{{ route('userzone.selections.store') }}" method="post" class="flex max-w-lg flex-col gap-5">
        @csrf

        <x-form.textinput name="title" label="Title" minlength="3" maxlength="255" required />
        <x-form.select
            name="search_type" label="Search type"
            :options="['' => 'Choose search type', 'short_stay' => 'Short stay', 'long_term' => 'Long-term rental']"
            required
        />
        <x-form.textinput name="start_date" label="Start date" type="date" />
        <x-form.textinput name="end_date" label="End date" type="date" />

        <x-ui.button type="submit" class="self-start">Add selection</x-ui.button>
    </form>

    <x-ui.button :href="route('userzone.selections.index')" variant="tertiary" class="mt-5">Back to selections</x-ui.button>
</x-base-layout>
