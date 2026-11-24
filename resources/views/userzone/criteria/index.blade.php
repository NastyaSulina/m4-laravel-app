<x-base-layout :title="'Criteria — '.$selection->title" class="bg-[#F8F3E9]!">
    <div class="max-w-2xl text-[#43263D]">
        <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary" class="mb-6 gap-2 rounded-none! border-b border-current no-underline!"><span>←</span> Back to selection</x-ui.button>

        <h1 class="mb-2 font-serif text-3xl font-semibold leading-tight">Criteria</h1>
        <p class="mb-6 break-words text-sm leading-relaxed text-[#43263D]/75">{{ $selection->title }}</p>

        <ul class="my-6 max-w-2xl divide-y divide-[#D8C9EA]">
            @forelse ($criteria as $criterion)
                <li class="flex flex-wrap items-center gap-4 py-3">
                    <span class="min-w-0 flex-1 break-words font-medium text-[#43263D]">{{ $criterion->title }}</span>
                    <span class="text-sm text-[#43263D]/60">{{ $criterion->is_required ? 'Required' : 'Optional' }}</span>
                    <x-ui.button :href="route('userzone.selections.criteria.edit', [$selection, $criterion])" variant="tertiary">Edit</x-ui.button>
                    <form action="{{ route('userzone.selections.criteria.destroy', [$selection, $criterion]) }}" method="post">
                        @method('delete')
                        @csrf
                        <x-ui.button type="submit" variant="tertiary" danger>Delete</x-ui.button>
                    </form>
                </li>
            @empty
                <li class="py-3 text-sm text-[#43263D]/60">No criteria yet.</li>
            @endforelse
        </ul>

        <h2 class="mb-4 text-2xl font-semibold text-[#43263D] font-serif leading-tight">Add criterion</h2>
        <form action="{{ route('userzone.selections.criteria.store', $selection) }}" method="post" class="flex flex-col gap-4">
            @csrf
            <x-form.textinput name="title" label="Title" minlength="3" maxlength="255" required />
            <x-form.checkbox name="is_required" label="Required" />
            <x-ui.button type="submit" class="self-start">Add criterion</x-ui.button>
        </form>
    </div>
</x-base-layout>
