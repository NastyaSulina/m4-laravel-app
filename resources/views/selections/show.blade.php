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
</x-base-layout>
