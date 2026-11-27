<x-base-layout :title="$selection->title">
    @php($isOwner = $selection->owner_id === auth()->id())

    <x-ui.button :href="route('userzone.selections.index',  ['filter' => $isOwner ? 'my' : 'shared'])" variant="tertiary" class="mb-6 gap-2 no-underline hover:underline">← Back to selections</x-ui.button>

    @if (! $isOwner)
        <div class="mb-6 rounded-2xl bg-soft p-4 text-ink">
            <p class="font-semibold">{{ $selection->owner->name }} shared this selection with you.</p>
            <p class="mt-1 text-sm">You can edit its contents. Only the owner can manage sharing.</p>
        </div>
    @else
        <p class="mb-3 text-sm leading-relaxed text-muted">Your selection</p>
    @endif

    <article class="flex flex-col gap-4 pb-2 text-ink">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex min-w-0 flex-1 flex-wrap items-center gap-3">
                <h1 class="min-w-0 max-w-full break-words text-3xl font-semibold leading-tight tracking-tight">{{ $selection->title }}</h1>
                <x-ui.status-badge :status="$selection->status" />
            </div>
            <div class="flex flex-wrap items-center justify-end gap-3 self-start lg:self-auto">
                @if ($isOwner)
                    <x-ui.button :href="route('userzone.selections.users.index', $selection)" variant="secondary" class="gap-2">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <circle cx="18" cy="5" r="3" /><circle cx="6" cy="12" r="3" /><circle cx="18" cy="19" r="3" />
                            <path d="m8.6 10.5 6.8-4m-6.8 7 6.8 4" />
                        </svg>
                        Share selection
                    </x-ui.button>
                @endif
                <x-ui.action-menu
                    :edit-url="route('userzone.selections.edit', $selection)"
                    :delete-url="$isOwner ? route('userzone.selections.destroy', $selection) : null"
                    label="selection"
                />
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-muted">
            <p>{{ ['short_stay' => 'Short stay', 'long_term' => 'Long-term rental'][$selection->search_type] }}</p>
            @if ($selection->start_date)
                <p>Check-in: {{ \Illuminate\Support\Carbon::parse($selection->start_date)->format('d.m.Y') }}</p>
            @endif
            @if ($selection->end_date)
                <p>Check-out: {{ \Illuminate\Support\Carbon::parse($selection->end_date)->format('d.m.Y') }}</p>
            @endif
        </div>
    </article>

    <div>
        <section class="mt-6 flex flex-col gap-2.5 rounded-2xl bg-soft/70 p-3 sm:px-4 text-ink">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-sm font-semibold">Filter by criteria</h2>
                <x-ui.button :href="route('userzone.selections.criteria.index', $selection)" variant="secondary" class="bg-surface! px-3! py-1.5! ring-1 ring-line/70">Manage criteria</x-ui.button>
            </div>

            @if ($selection->criteria->isNotEmpty())
                <form method="get" action="{{ route('userzone.selections.show', $selection) }}" class="flex min-w-0 flex-col gap-3">
                    <fieldset class="min-w-0">
                        <div class="flex flex-wrap gap-2">
                            @foreach ($selection->criteria as $criterion)
                                <label class="flex max-w-full cursor-pointer items-center gap-2.5 rounded-xl bg-surface px-3 py-2 text-base font-medium transition-colors hover:bg-line/50 has-checked:bg-ink has-checked:text-white has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-ink">
                                    <input type="checkbox" name="criteria[]" value="{{ $criterion->id }}" @checked(in_array($criterion->id, $selectedCriteria, true)) onchange="this.form.requestSubmit()" class="order-last size-4.5 shrink-0 cursor-pointer rounded-sm border-muted/50 bg-transparent text-ink focus:ring-0 focus:ring-offset-0 checked:border-white/70 checked:bg-ink">
                                    <span class="min-w-0 break-words">{{ $criterion->title }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </form>
            @else
                <p class="text-sm text-muted">Add what matters to you in a place.</p>
            @endif

        </section>

        <div class="mb-6 mt-8 flex flex-wrap items-center justify-between gap-6 text-ink">
            <h2 class="text-xl font-semibold leading-tight">Accommodations</h2>
            <x-ui.button :href="route('userzone.selections.accommodations.create', $selection)">+ New accommodation</x-ui.button>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 text-ink">
            @forelse ($accommodations as $accommodation)
                <a href="{{ route('userzone.selections.accommodations.show', [$selection, $accommodation]) }}" class="group flex min-w-0 flex-col gap-3 rounded-2xl bg-surface p-3 transition-colors hover:bg-soft/50 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink">
                    <div class="relative">
                        <img
                            src="{{ $accommodation->getFirstMediaUrl('images', 'thumbnail') }}" alt="{{ $accommodation->title }}"
                            width="600" height="400" loading="lazy"
                            class="aspect-[3/2] w-full rounded-xl bg-soft object-cover"
                        >
                        <x-ui.status-badge :status="$accommodation->status" class="absolute left-3 top-3" />
                    </div>
                    <h3 class="line-clamp-2 break-words text-xl font-semibold leading-6 group-hover:underline" title="{{ $accommodation->title }}">{{ $accommodation->title }}</h3>
                    @if ($accommodation->note)
                        <p class="line-clamp-2 break-words text-sm leading-relaxed text-muted">{{ $accommodation->note }}</p>
                    @endif
                    @if ($selection->criteria->isNotEmpty())
                        <ul class="flex flex-col gap-2 rounded-xl bg-canvas/70 p-3 text-sm mt-1">
                            @php($answers = $accommodation->criteria->pluck('pivot.is_met', 'id'))
                            @foreach ($selection->criteria as $criterion)
                                @php($isMet = $answers->get($criterion->id))
                                <li class="flex items-start justify-between gap-3">
                                    <span class="min-w-0 break-words">{{ $criterion->title }}</span>
                                    <span class="shrink-0">
                                        @if ($isMet === null)
                                            <span class="text-muted">Not checked</span>
                                        @elseif ($isMet)
                                            <span aria-hidden="true" class="font-semibold text-success">✓</span>
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
                </a>
            @empty
                @if ($selectedCriteria !== [])
                    <div role="status" class="col-span-full rounded-2xl bg-surface p-5 sm:p-6">
                        <p class="font-medium">No accommodations match the selected criteria.</p>
                        <p class="mt-2 text-sm text-muted">Uncheck a filter above to see more places.</p>
                    </div>
                @else
                    <div class="col-span-full rounded-2xl bg-surface p-5 sm:p-6">
                        <h3 class="text-xl font-semibold">No accommodations yet</h3>
                        <p class="mt-2 max-w-lg text-sm leading-relaxed text-muted">Found a place you like? Add its link to keep the details here and compare it with your next find.</p>
                        <x-ui.button :href="route('userzone.selections.accommodations.create', $selection)" class="mt-4">Add your first accommodation</x-ui.button>
                    </div>
                @endif
            @endforelse
        </div>
    </div>
</x-base-layout>
