<x-base-layout title="Selections">
    <h1 class="mb-6 text-3xl font-semibold tracking-tight text-slate-900">Selections</h1>

    <x-ui.button :href="route('userzone.selections.create')" class="mb-6">New selection</x-ui.button>

    <div class="grid gap-5 sm:grid-cols-2">
    @forelse ($selections as $selection)
        <div class="flex min-w-0 flex-col gap-3 rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="break-words text-xl font-semibold text-slate-900">{{ $selection->title }}</h2>
            <p>Search type: {{ $selection->search_type }}</p>
            <p>Status: {{ $selection->status }}</p>
            @if ($selection->start_date)
                <p>Start date: {{ $selection->start_date }}</p>
            @endif
            @if ($selection->end_date)
                <p>End date: {{ $selection->end_date }}</p>
            @endif
            <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary" class="mt-auto self-start pt-3">View selection</x-ui.button>
        </div>
    @empty
        <p class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-slate-500 sm:col-span-2">No selections yet.</p>
    @endforelse
    </div>
</x-base-layout>
