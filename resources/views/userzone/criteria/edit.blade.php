<x-base-layout title="Edit criterion">
    <h1 class="mb-6 text-3xl font-semibold tracking-tight text-slate-900">Edit criterion</h1>

    <form
        action="{{ route('userzone.selections.criteria.update', [$selection, $criterion]) }}" method="post"
        class="flex max-w-lg flex-col gap-5"
    >
        @method('PUT')
        @csrf

        <x-form.textinput name="title" label="Title" :value="$criterion->title" minlength="3" maxlength="255" required />
        <x-form.checkbox name="is_required" label="Required" :checked="$criterion->is_required" />

        <button
            type="submit"
            class="self-start cursor-pointer rounded-lg bg-teal-700 px-4 py-2 font-medium text-white hover:bg-teal-800
                   focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
        >
            Update criterion
        </button>
    </form>

    <a
        href="{{ route('userzone.selections.criteria.index', $selection) }}"
        class="mt-5 inline-block rounded-sm font-medium text-teal-700 underline-offset-4 hover:underline
               focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
    >
        Back to criteria
    </a>
</x-base-layout>
