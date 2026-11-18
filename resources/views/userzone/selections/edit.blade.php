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

        <button
            type="submit"
            class="self-start cursor-pointer rounded-lg bg-teal-700 px-4 py-2 font-medium text-white hover:bg-teal-800
                   focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
        >
            Update selection
        </button>
    </form>

    <a
        href="{{ route('userzone.selections.show', $selection) }}"
        class="mt-5 inline-block rounded-sm font-medium text-teal-700 underline-offset-4 hover:underline
               focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
    >
        Back to selection
    </a>
</x-base-layout>
