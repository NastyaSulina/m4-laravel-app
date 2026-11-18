<x-base-layout title="Selections">
    <h1 class="mb-6 text-3xl font-semibold tracking-tight text-slate-900">Selections</h1>

    <a
        href="{{ route('userzone.selections.create') }}"
        class="mb-6 inline-block rounded-lg bg-teal-700 px-4 py-2 font-medium text-white hover:bg-teal-800
               focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
    >
        New selection
    </a>

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
            <a
                href="{{ route('userzone.selections.show', $selection) }}"
                class="mt-auto self-start rounded-sm pt-3 font-medium text-teal-700 underline-offset-4 hover:underline
                       focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
            >
                View selection &rarr;
            </a>
        </div>
    @empty
        <p class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-slate-500 sm:col-span-2">No selections yet.</p>
    @endforelse
    </div>
</x-base-layout>
