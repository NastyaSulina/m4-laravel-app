<x-base-layout title="Selections">
    <div class="mb-8 flex flex-wrap items-center justify-between gap-6 text-ink">
        <h1 class="text-3xl font-semibold leading-tight tracking-tight">Selections</h1>
        <x-ui.button :href="route('userzone.selections.create')">+ New selection</x-ui.button>
    </div>

    <div class="mb-6 inline-flex flex-wrap gap-1 rounded-xl bg-soft p-1 text-sm text-ink">
        @foreach (['my' => 'My selections', 'shared' => 'Shared with me'] as $value => $label)
            <a href="{{ route('userzone.selections.index', ['filter' => $value]) }}"
                @class([
                    'rounded-lg px-4 py-2 transition-colors focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink',
                    'bg-surface font-medium shadow-xs' => $filter === $value,
                    'text-muted hover:bg-surface/60 hover:text-ink' => $filter !== $value,
                ])>
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="flex flex-col gap-5 text-ink">
        @forelse ($selections as $selection)
            @php($isOwner = $selection->owner_id === auth()->id())
            <a href="{{ route('userzone.selections.show', $selection) }}" @class([
                'group flex flex-wrap items-center justify-between gap-6 rounded-2xl transition-colors hover:bg-soft focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink p-5 sm:p-6',
                'bg-surface' => $isOwner,
                'bg-soft/60' => ! $isOwner,
            ])>
                <div class="min-w-0 flex-1">
                    <div class="mb-3 flex flex-wrap items-center gap-x-4 gap-y-2">
                        <h2 class="min-w-0 max-w-full break-words text-xl font-semibold leading-tight group-hover:underline underline-offset-4">{{ $selection->title }}</h2>
                        <x-ui.status-badge :status="$selection->status" />
                    </div>
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-muted">
                        @if (! $isOwner)
                            <p>Shared by <span class="font-medium text-ink">{{ $selection->owner->name }}</span></p>
                        @endif
                        <p>{{ ['short_stay' => 'Short stay', 'long_term' => 'Long-term rental'][$selection->search_type] }}</p>
                        @if ($selection->start_date)
                            <p>Check-in: {{ \Illuminate\Support\Carbon::parse($selection->start_date)->format('d.m.Y') }}</p>
                        @endif
                        @if ($selection->end_date)
                            <p>Check-out: {{ \Illuminate\Support\Carbon::parse($selection->end_date)->format('d.m.Y') }}</p>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <section class="rounded-2xl bg-surface p-5 sm:p-6">
                @if ($filter === 'shared')
                    <h2 class="text-xl font-semibold">No shared selections yet</h2>
                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-muted">When someone shares a selection with your account, it will appear here so you can compare places together.</p>
                    <x-ui.button :href="route('userzone.selections.index', ['filter' => 'my'])" variant="secondary" class="mt-5">Browse my selections</x-ui.button>
                @else
                    <h2 class="text-xl font-semibold">No selections yet.</h2>
                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-muted">Start with a trip or a place you want to call home. A selection keeps your accommodation options together.</p>
                    <x-ui.button :href="route('userzone.selections.create')" class="mt-5">+ New selection</x-ui.button>
                @endif
            </section>
        @endforelse
    </div>
</x-base-layout>
