<x-base-layout :title="$accommodation->title">
    <x-ui.button
        :href="route('userzone.selections.show', $accommodation->selection_id)"
        variant="tertiary" class="mb-6 gap-2"
    >← Back to selection</x-ui.button>

    @if ($selection->owner_id !== auth()->id())
        <div class="mb-6 rounded-2xl bg-soft p-5 text-ink">
            <p class="font-medium">{{ $selection->owner->name }} shared this selection with you.</p>
            <p class="mt-1 break-words text-sm text-muted">You can edit its contents. Only the owner can manage sharing.</p>
        </div>
    @endif

    <article class="flex flex-col gap-6 text-ink">
        <div class="flex items-start justify-between gap-4">
            <div class="flex min-w-0 flex-1 flex-col gap-3">
                <div class="flex min-w-0 flex-wrap items-center gap-3">
                    <h1 class="min-w-0 max-w-full break-words text-3xl font-semibold leading-tight tracking-tight">{{ $accommodation->title }}</h1>
                    <x-ui.status-badge :status="$accommodation->status" />
                </div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-3 text-sm text-muted">
                    <p>Property type: {{ ucfirst($accommodation->property_type) }}</p>
                    <p>Added by <strong class="font-medium text-ink">{{ $accommodation->user->name }}</strong></p>
                </div>
            </div>
            <x-ui.action-menu
                :criteria-edit-url="route('userzone.selections.accommodations.criteria.edit', [$selection, $accommodation])"
                :edit-url="route('userzone.selections.accommodations.edit', [$selection, $accommodation])"
                :delete-url="($selection->owner_id === auth()->id() || $accommodation->user_id === auth()->id()) ? route('userzone.selections.accommodations.destroy', [$selection, $accommodation]) : null"
                label="accommodation"
            />
        </div>

        <div class="grid grid-cols-1 items-start gap-6 md:grid-cols-2">
            <div class="flex min-w-0 flex-col gap-5 rounded-2xl bg-surface p-5 sm:p-6">
                <img
                    src="{{ $accommodation->getFirstMediaUrl('images') }}" alt="{{ $accommodation->title }}"
                    class="aspect-[4/3] w-full rounded-xl bg-soft object-cover"
                >
                <p class="break-words leading-relaxed"><span class="mb-1 block text-sm font-medium text-muted">Address</span>{{ $accommodation->address }}</p>
                <p class="break-all">
                    <span class="mb-1 block text-sm font-medium text-muted">Source</span>
                    <a href="{{ $accommodation->source_url }}" class="text-sm font-medium underline decoration-line underline-offset-4 hover:decoration-ink">{{ parse_url($accommodation->source_url, PHP_URL_HOST) }} ↗</a>
                </p>
            </div>
            <div class="flex min-w-0 flex-col gap-6">
                <section aria-labelledby="criteria-heading" class="rounded-2xl bg-surface p-5 sm:p-6">
                    <h2 id="criteria-heading" class="text-xl font-semibold">Criteria</h2>
                    @if ($selection->criteria->isEmpty())
                        <p class="mt-2 text-sm leading-relaxed text-muted">Add what matters to this selection, such as parking or a quiet room, to compare this place with your other options.</p>
                        <x-ui.button :href="route('userzone.selections.criteria.index', $selection)" class="mt-5">Add criteria to selection</x-ui.button>
                    @else
                        <p class="mb-5 mt-2 text-sm leading-relaxed text-muted">How this place matches your selection criteria.</p>
                        <ul class="flex flex-col gap-3 text-sm">
                            @foreach ($selection->criteria as $criterion)
                                @php($isMet = $accommodation->criteria->firstWhere('id', $criterion->id)?->pivot->is_met)
                                <li class="flex items-center justify-between gap-3">
                                    <span class="min-w-0 break-words">{{ $criterion->title }}</span>
                                    <span class="shrink-0">
                                        @if ($isMet === null)
                                            <span class="inline-flex rounded-full bg-canvas px-3 py-1 text-muted">Not checked</span>
                                        @elseif ($isMet)
                                            <span class="inline-flex rounded-full bg-success-soft px-3 py-1 font-medium text-success">Met</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-red-50 px-3 py-1 font-medium text-red-700">Not met</span>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
                @if ($accommodation->note)
                    <div class="rounded-2xl bg-soft p-5 sm:p-6">
                        <h2 class="mb-3 text-xl font-semibold">Note</h2>
                        <p class="whitespace-pre-line break-words text-base leading-relaxed">{{ $accommodation->note }}</p>
                    </div>
                @endif
            </div>
        </div>
    </article>
</x-base-layout>
