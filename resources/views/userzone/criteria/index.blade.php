<x-base-layout :title="'Criteria — '.$selection->title">
    <h1 class="mb-2 text-3xl font-semibold tracking-tight text-slate-900">Criteria</h1>
    <p class="mb-4 break-words text-slate-600">{{ $selection->title }}</p>
    <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary">Back to selection</x-ui.button>

    <ul class="my-8 max-w-2xl divide-y divide-slate-200">
        @forelse ($criteria as $criterion)
            <li class="flex flex-wrap items-center gap-4 py-3">
                <span class="min-w-0 flex-1 break-words font-medium text-slate-900">{{ $criterion->title }}</span>
                <span class="text-sm text-slate-500">{{ $criterion->is_required ? 'Required' : 'Optional' }}</span>
                <x-ui.button :href="route('userzone.selections.criteria.edit', [$selection, $criterion])" variant="tertiary">Edit</x-ui.button>
                <form action="{{ route('userzone.selections.criteria.destroy', [$selection, $criterion]) }}" method="post">
                    @method('delete')
                    @csrf
                    <x-ui.button type="submit" variant="tertiary" danger>Delete</x-ui.button>
                </form>
            </li>
        @empty
            <li class="py-3 text-sm text-slate-500">No criteria yet.</li>
        @endforelse
    </ul>

    <h2 class="mb-4 text-lg font-semibold text-slate-900">Add criterion</h2>
    <form action="{{ route('userzone.selections.criteria.store', $selection) }}" method="post" class="flex max-w-lg flex-col gap-5">
        @csrf
        <x-form.textinput name="title" label="Title" minlength="3" maxlength="255" required />
        <x-form.checkbox name="is_required" label="Required" />
        <x-ui.button type="submit" class="self-start">Add criterion</x-ui.button>
    </form>
</x-base-layout>
