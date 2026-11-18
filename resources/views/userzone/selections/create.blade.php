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

        <button
            type="submit"
            class="self-start cursor-pointer rounded-lg bg-teal-700 px-4 py-2 font-medium text-white hover:bg-teal-800
                   focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
        >
            Add selection
        </button>
    </form>

    <a
        href="{{ route('userzone.selections.index') }}"
        class="mt-5 inline-block rounded-sm font-medium text-teal-700 underline-offset-4 hover:underline
               focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
    >
        Back to selections
    </a>
</x-base-layout>
