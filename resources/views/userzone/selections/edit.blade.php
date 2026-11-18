<x-base-layout title="Edit selection">
    <h1 class="mb-6 text-3xl font-semibold tracking-tight text-slate-900">Edit selection</h1>

    <form action="{{ route('userzone.selections.update', $selection) }}" method="post" class="flex max-w-lg flex-col gap-5">
        @method('PUT')
        @csrf

        <x-form.textinput name="title" label="Title" :value="$selection->title" minlength="3" maxlength="255" required />
        <x-form.select
            name="search_type" label="Search type" :value="$selection->search_type"
            :options="['' => 'Choose search type', 'short_stay' => 'Short stay', 'long_term' => 'Long-term rental']"
            required
        />
        <x-form.textinput name="start_date" label="Start date" type="date" :value="$selection->start_date" />
        <x-form.textinput name="end_date" label="End date" type="date" :value="$selection->end_date" />

        <x-ui.button type="submit" class="self-start">Update selection</x-ui.button>
    </form>

    <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary" class="mt-5">Back to selection</x-ui.button>
</x-base-layout>
