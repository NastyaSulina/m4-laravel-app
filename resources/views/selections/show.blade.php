<x-base-layout :title="$selection->title">
    <article class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-6 sm:p-8">
        <h1 class="break-words text-3xl font-semibold tracking-tight text-slate-900">{{ $selection->title }}</h1>

        <p>Search type: {{ $selection->search_type }}</p>
        <p>Status: {{ $selection->status }}</p>
        @if ($selection->start_date)
            <p>Start date: {{ $selection->start_date }}</p>
        @endif
        @if ($selection->end_date)
            <p>End date: {{ $selection->end_date }}</p>
        @endif

        <a
            href="{{ route('selections.index') }}"
            class="mt-4 self-start rounded-sm font-medium text-teal-700 underline-offset-4 hover:underline
                   focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
        >
            &larr; Back to selections
        </a>
    </article>

    <h2 class="mb-5 mt-10 text-2xl font-semibold text-slate-900">Accommodations</h2>

    <div class="grid gap-5 sm:grid-cols-2">
        @forelse ($selection->accommodations as $accommodation)
            <article class="flex min-w-0 flex-col gap-3 rounded-xl border border-slate-200 bg-white p-6">
                <h3 class="break-words text-xl font-semibold text-slate-900">{{ $accommodation->title }}</h3>
                <p>Address: {{ $accommodation->address }}</p>
                <p>Status: {{ $accommodation->status }}</p>
                @if ($accommodation->property_type)
                    <p>Property type: {{ $accommodation->property_type }}</p>
                @endif
                <a
                    href="{{ route('accommodations.show', $accommodation) }}"
                    class="mt-auto self-start rounded-sm pt-3 font-medium text-teal-700 underline-offset-4 hover:underline
                           focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
                >
                    View accommodation &rarr;
                </a>
            </article>
        @empty
            <p class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-slate-500 sm:col-span-2">
                No accommodations yet.
            </p>
        @endforelse
    </div>
</x-base-layout>
