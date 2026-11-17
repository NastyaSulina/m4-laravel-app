<x-base-layout :title="$selection->title">
    <article class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-6 sm:p-8">
        <p class="text-sm text-slate-500">Added by {{ $selection->owner->name }}</p>
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
            href="{{ route('userzone.selections.index') }}"
            class="mt-4 self-start rounded-sm font-medium text-teal-700 underline-offset-4 hover:underline
                   focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
        >
            &larr; Back to selections
        </a>
    </article>

    @if ($selection->criteria->isNotEmpty())
        <h2 class="mb-3 mt-8 text-lg font-semibold text-slate-900">Criteria</h2>

        <ul class="flex flex-wrap gap-2">
            @foreach ($selection->criteria as $criterion)
                <li
                    class="flex items-center gap-2 rounded-full border px-4 py-2 text-sm
                           {{ $criterion->is_required ? 'border-teal-200 bg-teal-50 text-teal-800' : 'border-slate-200 bg-slate-100 text-slate-600' }}"
                >
                    {{ $criterion->title }} — {{ $criterion->is_required ? 'Required' : 'Optional' }}
                    <form action="{{ route('userzone.criteria.destroy', $criterion) }}" method="post">
                        @method('delete')
                        @csrf
                        <button
                            type="submit"
                            aria-label="Delete criterion: {{ $criterion->title }}"
                            class="flex size-6 cursor-pointer items-center justify-center rounded-full text-lg leading-none
                                   hover:bg-red-100 hover:text-red-700 focus-visible:outline-2 focus-visible:outline-offset-2"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif

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
                    href="{{ route('userzone.accommodations.show', $accommodation) }}"
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
