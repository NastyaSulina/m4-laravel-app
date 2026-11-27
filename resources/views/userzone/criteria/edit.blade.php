<x-base-layout title="Edit criterion">
    <div class="max-w-2xl text-ink">
        <x-ui.button :href="route('userzone.selections.criteria.index', $selection)" variant="tertiary" class="mb-6 gap-2">← Back to criteria</x-ui.button>

        <h1 class="mb-6 text-3xl font-semibold leading-tight tracking-tight">Edit criterion</h1>

        <form
            action="{{ route('userzone.selections.criteria.update', [$selection, $criterion]) }}" method="post"
            class="flex flex-col gap-6 rounded-2xl bg-surface p-5 sm:p-6"
        >
            @method('PUT')
            @csrf

            <x-form.textinput name="title" label="Title" :value="$criterion->title" minlength="3" maxlength="255" required />

            <x-ui.button type="submit" class="self-start">Update criterion</x-ui.button>
        </form>
    </div>
</x-base-layout>
