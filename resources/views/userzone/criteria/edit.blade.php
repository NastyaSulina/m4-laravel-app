<x-base-layout title="Edit criterion" class="bg-[#F8F3E9]!">
    <div class="mx-auto max-w-2xl text-[#43263D]">
        <h1 class="mb-6 font-serif text-3xl font-semibold">Edit criterion</h1>

        <form
            action="{{ route('userzone.selections.criteria.update', [$selection, $criterion]) }}" method="post"
            class="flex flex-col gap-4"
        >
            @method('PUT')
            @csrf

            <x-form.textinput name="title" label="Title" :value="$criterion->title" minlength="3" maxlength="255" required />
            <x-form.checkbox name="is_required" label="Required" :checked="$criterion->is_required" />

            <x-ui.button type="submit" class="self-start">Update criterion</x-ui.button>
        </form>

        <x-ui.button :href="route('userzone.selections.criteria.index', $selection)" variant="tertiary" class="mt-5">Back to criteria</x-ui.button>
    </div>
</x-base-layout>
