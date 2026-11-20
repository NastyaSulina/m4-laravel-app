<x-base-layout :title="$selection->title" class="bg-[#F8F3E9]!">
    <article class="flex flex-col gap-6 border-b border-[#D8C9EA] pb-8 text-[#43263D]">
        <div class="flex items-center justify-between gap-4">
            <h1 class="flex-1 font-serif text-5xl font-semibold leading-tight">{{ $selection->title }}</h1>
            <div class="flex items-center gap-4">
                <x-ui.button :href="route('userzone.selections.edit', $selection)" variant="secondary">Edit</x-ui.button>
                <form action="{{ route('userzone.selections.destroy', $selection) }}" method="post">
                    @method('DELETE')
                    @csrf
                    <x-ui.button type="submit" variant="tertiary" danger>Delete selection</x-ui.button>
                </form>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 whitespace-nowrap text-lg">
            <p>{{ ['short_stay' => 'Short stay', 'long_term' => 'Long-term rental'][$selection->search_type] }}</p>
            @if ($selection->start_date)
                <p class="border-l border-[#D8C9EA] pl-6">From: {{ $selection->start_date }}</p>
            @endif
            @if ($selection->end_date)
                <p class="border-l border-[#D8C9EA] pl-6">To: {{ $selection->end_date }}</p>
            @endif
            <p class="border-l border-[#D8C9EA] pl-6">Status: {{ ucfirst($selection->status) }}</p>
            <p class="border-l border-[#D8C9EA] pl-6">Added by <strong>{{ $selection->owner->name }}</strong></p>
        </div>

        <x-ui.button :href="route('userzone.selections.index')" variant="tertiary" class="self-start">Back to selections</x-ui.button>
    </article>

    <section class="mt-8 flex items-center gap-6 border-b border-[#D8C9EA] pb-6 text-[#43263D]">
        <h2 class="text-xl font-semibold">Criteria</h2>

        @if ($selection->criteria->isNotEmpty())
            <ul class="flex gap-3">
                @foreach ($selection->criteria as $criterion)
                    <li class="rounded-full border border-[#D8C9EA] px-5 py-1 text-lg">
                        {{ $criterion->title }}
                        @if ($criterion->is_required)
                            <span title="Required" aria-hidden="true">*</span>
                            <span class="sr-only">Required</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        <x-ui.button :href="route('userzone.selections.criteria.index', $selection)" variant="tertiary" class="ml-auto shrink-0">Manage criteria</x-ui.button>
    </section>

    <div class="mb-6 mt-8 flex items-center justify-between gap-6 text-[#43263D]">
        <h2 class="font-serif text-4xl font-semibold">Accommodations</h2>
        <x-ui.button :href="route('userzone.selections.accommodations.create', $selection)">+ New accommodation</x-ui.button>
    </div>

    <div class="grid grid-cols-3 gap-6 text-[#43263D]">
        @forelse ($selection->accommodations as $accommodation)
            <article class="flex min-w-0 flex-col gap-3">
                <img
                    src="{{ $accommodation->getFirstMediaUrl('images', 'thumbnail') }}" alt="{{ $accommodation->title }}"
                    width="600" height="400" loading="lazy"
                    class="aspect-[3/2] w-full rounded-2xl bg-[#D8C9EA]/30 object-cover"
                >
                <h3 class="break-words text-xl font-semibold">{{ $accommodation->title }}</h3>
                <p>Address: {{ $accommodation->address }}</p>
                <p>Status: {{ ucfirst($accommodation->status) }}</p>
                <p>Property type: {{ ucfirst($accommodation->property_type) }}</p>
                <x-ui.button
                    :href="route('userzone.selections.accommodations.show', [$accommodation->selection_id, $accommodation])"
                    variant="tertiary" class="self-start"
                >View accommodation</x-ui.button>
            </article>
        @empty
            <p class="col-span-3 rounded-xl border border-dashed border-[#D8C9EA] p-8 text-center">
                No accommodations yet.
            </p>
        @endforelse
    </div>
</x-base-layout>
