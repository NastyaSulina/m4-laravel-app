<x-base-layout :title="'Criteria — '.$selection->title">
    <div class="max-w-2xl text-ink">
        <header class="mb-8">
            <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary" class="mb-6 max-w-full gap-2 break-words no-underline hover:underline">{{ $selection->title }}</x-ui.button>
            <h1 class="mb-3 text-3xl font-semibold leading-tight tracking-tight">What matters for your stay?</h1>
            <p class="max-w-xl text-base leading-relaxed text-muted">Keep the things that make a place feel right: quiet nights, good Wi-Fi, or room for your pet. You can change these anytime.</p>
        </header>

        <section aria-labelledby="add-criterion-heading" class="rounded-2xl bg-soft/60 p-5 sm:p-6">
            <h2 id="add-criterion-heading" class="mb-4 text-xl font-semibold text-ink leading-tight">Add a criterion</h2>
            <form action="{{ route('userzone.selections.criteria.store', $selection) }}" method="post" class="flex flex-col gap-5">
                @csrf
                <x-form.textinput name="title" label="What are you looking for?" placeholder="e.g. European breakfast, Wi-Fi, kitchen" minlength="3" maxlength="255" required />
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <x-ui.button type="submit">Add criterion</x-ui.button>
                    <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary">
                        Skip it for now
                    </x-ui.button>
                </div>
            </form>
        </section>

        <section aria-labelledby="saved-criteria-heading" class="mt-8">
            <h2 id="saved-criteria-heading" class="mb-4 text-xl font-semibold leading-tight">Your criteria</h2>
            <ul class="flex flex-col gap-2">
                @forelse ($criteria as $criterion)
                    <li class="flex flex-wrap items-center gap-4 rounded-xl bg-surface px-4 py-3">
                        <span class="min-w-0 flex-1 break-words font-medium text-ink">{{ $criterion->title }}</span>
                        <x-ui.button :href="route('userzone.selections.criteria.edit', [$selection, $criterion])" variant="tertiary">Edit</x-ui.button>
                        <form action="{{ route('userzone.selections.criteria.destroy', [$selection, $criterion]) }}" method="post">
                            @method('delete')
                            @csrf
                            <x-ui.button type="submit" variant="tertiary" danger>Delete</x-ui.button>
                        </form>
                    </li>
                @empty
                    <li class="rounded-2xl bg-surface p-5 sm:p-6">
                        <p class="font-medium">No criteria added yet.</p>
                        <p class="mt-2 text-sm leading-relaxed text-muted">Use the form above to add your first criterion. It will be available for every accommodation in this selection.</p>
                    </li>
                @endforelse
            </ul>
        </section>
    </div>
</x-base-layout>
