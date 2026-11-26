<x-base-layout :title="'Criteria — '.$selection->title" class="bg-[#F8F3E9]!">
    <div class="max-w-2xl text-[#43263D]">
        <header class="mb-10">
            <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary" class="mb-6 max-w-full gap-2 break-words rounded-none! border-b border-current no-underline!">{{ $selection->title }}</x-ui.button>
            <h1 class="mb-4 font-serif text-3xl font-semibold leading-tight">What matters for your stay?</h1>
            <p class="max-w-xl text-base leading-relaxed text-[#43263D]/75">Criteria describe what matters for your stay, such as a quiet room, parking or breakfast. They’re optional and can be added or changed anytime.</p>
        </header>

        <section aria-labelledby="saved-criteria-heading" class="mb-10">
            <h2 id="saved-criteria-heading" class="mb-4 font-serif text-2xl font-semibold leading-tight">Your criteria</h2>
            <ul class="divide-y divide-[#D8C9EA] border-y border-[#D8C9EA]">
                @forelse ($criteria as $criterion)
                    <li class="flex flex-wrap items-center gap-4 py-4">
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
                    <li class="py-5 text-sm leading-relaxed text-[#43263D]/65">No criteria added yet.</li>
                @endforelse
            </ul>
        </section>

        <section aria-labelledby="add-criterion-heading">
            <h2 id="add-criterion-heading" class="mb-4 text-2xl font-semibold text-[#43263D] font-serif leading-tight">Add a criterion</h2>
            <form action="{{ route('userzone.selections.criteria.store', $selection) }}" method="post" class="flex flex-col gap-4">
                @csrf
                <x-form.textinput name="title" label="What are you looking for?" placeholder="e.g. European breakfast, Wi-Fi, kitchen" minlength="3" maxlength="255" required />
                <x-form.checkbox name="is_required" label="Required for this stay" />
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <x-ui.button type="submit">Add criterion</x-ui.button>
                    <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary">
                        Skip it for now
                    </x-ui.button>
                </div>
            </form>
        </section>
    </div>
</x-base-layout>
