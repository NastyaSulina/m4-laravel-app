<x-base-layout title="Selections" class="bg-[#F8F3E9]!">
    <div class="mb-8 flex items-center justify-between gap-6 text-[#43263D]">
        <h1 class="font-serif text-3xl font-semibold leading-tight">Selections</h1>
        <x-ui.button :href="route('userzone.selections.create')">+ New selection</x-ui.button>
    </div>

    <div class="mb-6 flex flex-wrap gap-x-6 gap-y-2 text-sm text-[#43263D]">
        @foreach (['my' => 'My selections', 'shared' => 'Shared with me'] as $value => $label)
            <a href="{{ route('userzone.selections.index', ['filter' => $value]) }}"
                @class([
                    'border-b-2 py-2 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#43263D]',
                    'border-[#43263D] font-semibold' => $filter === $value,
                    'border-transparent text-[#43263D]/70 hover:border-[#D8C9EA] hover:text-[#43263D]' => $filter !== $value,
                ])>
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="flex flex-col gap-5 text-[#43263D]">
        @forelse ($selections as $selection)
            @php($isOwner = $selection->owner_id === auth()->id())
            <article class="flex items-center justify-between gap-6 rounded-2xl border border-[#D8C9EA] bg-white/30 p-6">
                <div class="min-w-0 flex-1">
                    <div class="mb-3 flex flex-wrap items-center gap-x-4 gap-y-2">
                        <h2 class="break-words font-serif text-xl font-semibold leading-tight">{{ $selection->title }}</h2>
                        @if (! $isOwner)
                            <p class="rounded-full bg-[#D8C9EA] px-3 py-1 text-sm font-medium text-[#43263D]">Owner: {{ $selection->owner->name }}</p>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 whitespace-nowrap text-base">
                        <p>{{ ['short_stay' => 'Short stay', 'long_term' => 'Long-term rental'][$selection->search_type] }}</p>
                        @if ($selection->start_date)
                            <p class="border-l border-[#D8C9EA] pl-5">From: {{ $selection->start_date }}</p>
                        @endif
                        @if ($selection->end_date)
                            <p class="border-l border-[#D8C9EA] pl-5">To: {{ $selection->end_date }}</p>
                        @endif
                        <p class="border-l border-[#D8C9EA] pl-5">Status: {{ ucfirst($selection->status) }}</p>
                    </div>
                </div>
                <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary" class="shrink-0 no-underline! hover:underline!">View selection →</x-ui.button>
            </article>
        @empty
            <p class="rounded-2xl border border-dashed border-[#D8C9EA] p-8 text-center">No selections yet.</p>
        @endforelse
    </div>
</x-base-layout>
