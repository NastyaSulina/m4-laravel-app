<x-base-layout :title="$selection->title" class="bg-[#F8F3E9]!">
    @php($isOwner = $selection->owner_id === auth()->id())

    <x-ui.button :href="route('userzone.selections.index',  ['filter' => $isOwner ? 'my' : 'shared'])" variant="tertiary" class="mb-6 gap-2 rounded-none! border-b border-current no-underline!"><span>←</span> Back to selections</x-ui.button>

    @if (! $isOwner)
        <div class="mb-6 rounded-2xl border border-[#D8C9EA] bg-[#D8C9EA]/30 p-4 text-[#43263D]">
            <p class="font-semibold">{{ $selection->owner->name }} shared this selection with you.</p>
            <p class="mt-1 text-sm">You can edit its contents. Only the owner can manage sharing.</p>
        </div>
    @else
        <p class="mb-3 text-sm leading-relaxed text-[#43263D]/75">Your selection</p>
    @endif

    <article class="flex flex-col gap-6 border-b border-[#D8C9EA] pb-8 text-[#43263D]">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex min-w-0 flex-1 flex-wrap items-center gap-3">
                <h1 class="break-words font-serif text-3xl font-semibold leading-tight">{{ $selection->title }}</h1>
                <x-ui.status-badge :status="$selection->status" />
            </div>
            <div class="flex flex-wrap items-center justify-end gap-4 self-end lg:self-auto">
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

        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 whitespace-nowrap text-base">
            <p>{{ ['short_stay' => 'Short stay', 'long_term' => 'Long-term rental'][$selection->search_type] }}</p>
            @if ($selection->start_date)
                <p class="border-l border-[#D8C9EA] pl-6">Check-in: {{ \Illuminate\Support\Carbon::parse($selection->start_date)->format('d.m.Y') }}</p>
            @endif
            @if ($selection->end_date)
                <p class="border-l border-[#D8C9EA] pl-6">Check-out: {{ \Illuminate\Support\Carbon::parse($selection->end_date)->format('d.m.Y') }}</p>
            @endif
        </div>
    </article>

    <section class="mt-8 grid grid-cols-[minmax(0,1fr)_auto] items-start gap-4 border-b border-[#D8C9EA] pb-6 text-[#43263D]">
        <h2 class="sr-only">Criteria</h2>

        @if ($selection->criteria->isNotEmpty())
            <ul class="flex min-w-0 flex-wrap gap-3">
                @foreach ($selection->criteria as $criterion)
                    <li class="max-w-full break-words rounded-2xl border border-[#D8C9EA] px-4 py-1 text-base">
                        {{ $criterion->title }}
                        @if ($criterion->is_required)
                            <span title="Required" aria-hidden="true">*</span>
                            <span class="sr-only">Required</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-[#43263D]/70">Add what matters to you in a place.</p>
        @endif

        <x-ui.button :href="route('userzone.selections.criteria.index', $selection)" variant="tertiary" class="shrink-0 text-sm!">Manage criteria</x-ui.button>
    </section>

    <div class="mb-6 mt-8 flex flex-wrap items-center justify-between gap-6 text-[#43263D]">
        <h2 class="font-serif text-2xl font-semibold leading-tight">Accommodations</h2>
        <x-ui.button :href="route('userzone.selections.accommodations.create', $selection)">+ New accommodation</x-ui.button>
    </div>

    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2 lg:grid-cols-3 text-[#43263D]">
        @forelse ($selection->accommodations as $accommodation)
            <a href="{{ route('userzone.selections.accommodations.show', [$selection, $accommodation]) }}" class="group flex min-w-0 flex-col gap-2 rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#43263D]">
                <div class="relative">
                    <img
                        src="{{ $accommodation->getFirstMediaUrl('images', 'thumbnail') }}" alt="{{ $accommodation->title }}"
                        width="600" height="400" loading="lazy"
                        class="aspect-[3/2] w-full rounded-2xl bg-[#D8C9EA]/30 object-cover"
                    >
                    <x-ui.status-badge :status="$accommodation->status" class="absolute left-3 top-3" />
                </div>
                <h3 class="truncate font-serif text-xl font-semibold leading-6 group-hover:underline" title="{{ $accommodation->title }}">{{ $accommodation->title }}</h3>
                @if ($accommodation->note)
                    <p class="line-clamp-2 break-words text-sm leading-relaxed text-[#43263D]/75">{{ $accommodation->note }}</p>
                @endif
            </a>
        @empty
            <p class="col-span-full rounded-xl border border-dashed border-[#D8C9EA] p-8 text-center">
                No accommodations yet.
            </p>
        @endforelse
    </div>
</x-base-layout>
