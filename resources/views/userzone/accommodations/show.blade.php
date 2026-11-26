<x-base-layout :title="$accommodation->title" class="bg-[#F8F3E9]!">
    <x-ui.button
        :href="route('userzone.selections.show', $accommodation->selection_id)"
        variant="tertiary" class="mb-6 gap-2 rounded-none! border-b border-current no-underline!"
    ><span>←</span> Back to selection</x-ui.button>

    @if ($selection->owner_id !== auth()->id())
        <div class="mb-6 rounded-2xl border border-[#D8C9EA] bg-[#D8C9EA]/30 p-4 text-[#43263D]">
            <p class="font-semibold">{{ $selection->owner->name }} shared this selection with you.</p>
            <p class="mt-1 break-words text-sm">You can edit its contents. Only the owner can manage sharing.</p>
        </div>
    @endif

    <article class="flex flex-col gap-8 text-[#43263D]">
        <div class="flex items-start justify-between gap-4">
            <div class="flex min-w-0 flex-1 flex-wrap items-center gap-3">
                <h1 class="break-words font-serif text-3xl font-semibold leading-tight">{{ $accommodation->title }}</h1>
                <x-ui.status-badge :status="$accommodation->status" />
            </div>
            <x-ui.action-menu
                :criteria-edit-url="route('userzone.selections.accommodations.criteria.edit', [$selection, $accommodation])"
                :edit-url="route('userzone.selections.accommodations.edit', [$selection, $accommodation])"
                :delete-url="($selection->owner_id === auth()->id() || $accommodation->user_id === auth()->id()) ? route('userzone.selections.accommodations.destroy', [$selection, $accommodation]) : null"
                label="accommodation"
            />
        </div>

        <div class="flex flex-wrap items-center gap-6 border-b border-[#D8C9EA] pb-6 text-base">
            <p>Property type: {{ ucfirst($accommodation->property_type) }}</p>
            <p class="border-l border-[#D8C9EA] pl-6">Added by <strong>{{ $accommodation->user->name }}</strong></p>
        </div>

        <div class="grid grid-cols-1 items-start sm:grid-cols-3 gap-8">
            <img
                src="{{ $accommodation->getFirstMediaUrl('images') }}" alt="{{ $accommodation->title }}"
                class="aspect-square w-full rounded-xl object-cover"
            >
            <div class="sm:col-span-2 flex min-w-0 flex-col gap-6 text-base">
                <p class="break-words"><span class="block font-serif text-xl font-semibold">Address: </span>{{ $accommodation->address }}</p>
                <p class="break-all">
                    <span class="block font-serif text-xl font-semibold">Source</span>
                    <a href="{{ $accommodation->source_url }}" class="underline underline-offset-4">{{ parse_url($accommodation->source_url, PHP_URL_HOST) }} ↗</a>
                </p>
                <section aria-labelledby="criteria-heading">
                    <h2 id="criteria-heading" class="mb-2 font-serif text-xl font-semibold">Criteria</h2>
                    <p class="mb-3 text-sm text-[#43263D]/75">How this place matches your selection criteria.</p>
                    @if ($selection->criteria->isEmpty())
                        <p class="text-sm text-[#43263D]/75">No criteria added yet.</p>
                    @else
                        <ul class="flex flex-col gap-2 text-sm">
                            @foreach ($selection->criteria as $criterion)
                                @php($isMet = $accommodation->criteria->firstWhere('id', $criterion->id)?->pivot->is_met)
                                <li class="flex items-start justify-between gap-3">
                                    <span class="min-w-0 break-words">{{ $criterion->title }}</span>
                                    <span class="shrink-0">
                                        @if ($isMet === null)
                                            <span class="text-[#43263D]/75">Not checked</span>
                                        @elseif ($isMet)
                                            <span aria-hidden="true" class="font-semibold text-green-700">✓</span>
                                            <span class="sr-only">Met</span>
                                        @else
                                            <span aria-hidden="true" class="font-semibold text-red-700">✕</span>
                                            <span class="sr-only">Not met</span>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
                @if ($accommodation->note)
                    <p><span class="mb-2 block font-serif text-xl font-semibold">Note: </span><span class="block whitespace-pre-line break-words rounded-xl bg-[#D8C9EA]/50 px-4 py-3">{{ $accommodation->note }}</span></p>
                @endif
            </div>
        </div>

    </article>
</x-base-layout>
