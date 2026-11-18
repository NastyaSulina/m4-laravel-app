<x-base-layout :title="'Criteria — '.$selection->title">
    <h1 class="mb-2 text-3xl font-semibold tracking-tight text-slate-900">Criteria</h1>
    <p class="mb-4 break-words text-slate-600">{{ $selection->title }}</p>
    <a href="{{ route('userzone.selections.show', $selection) }}" class="font-medium text-teal-700 underline-offset-4 hover:underline">
        Back to selection
    </a>

    <ul class="my-8 max-w-2xl divide-y divide-slate-200">
        @forelse ($criteria as $criterion)
            <li class="flex flex-wrap items-center gap-4 py-3">
                <span class="min-w-0 flex-1 break-words font-medium text-slate-900">{{ $criterion->title }}</span>
                <span class="text-sm text-slate-500">{{ $criterion->is_required ? 'Required' : 'Optional' }}</span>
                <a href="{{ route('userzone.selections.criteria.edit', [$selection, $criterion]) }}" class="text-sm font-medium text-teal-700 hover:underline">Edit</a>
                <form action="{{ route('userzone.selections.criteria.destroy', [$selection, $criterion]) }}" method="post">
                    @method('delete')
                    @csrf
                    <button type="submit" class="cursor-pointer text-sm text-red-600 hover:underline">Delete</button>
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
        <button
            type="submit"
            class="self-start cursor-pointer rounded-lg bg-teal-700 px-4 py-2 font-medium text-white hover:bg-teal-800
                   focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
        >
            Add criterion
        </button>
    </form>
</x-base-layout>
