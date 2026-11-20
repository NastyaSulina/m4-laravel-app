<x-base-layout title="Selections" class="bg-[#F8F3E9]!">
    <div class="mb-8 flex items-center justify-between gap-6 text-[#43263D]">
        <h1 class="font-serif text-5xl font-semibold leading-tight">My selections</h1>
        <x-ui.button :href="route('userzone.selections.create')">+ New selection</x-ui.button>
    </div>

    <div class="flex flex-col gap-5 text-[#43263D]">
        @forelse ($selections as $selection)
            <article class="flex items-center justify-between gap-6 rounded-2xl border border-[#D8C9EA] bg-white/30 p-6">
                <div class="min-w-0 flex-1">
                    <h2 class="mb-3 break-words font-serif text-2xl font-semibold">{{ $selection->title }}</h2>
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 whitespace-nowrap text-lg">
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
                <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary" class="shrink-0 text-lg! no-underline! hover:underline!">View selection →</x-ui.button>
            </article>
        @empty
            <p class="rounded-2xl border border-dashed border-[#D8C9EA] p-8 text-center">No selections yet.</p>
        @endforelse
    </div>
</x-base-layout>
