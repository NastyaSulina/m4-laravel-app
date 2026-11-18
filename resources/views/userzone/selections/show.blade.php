<x-base-layout :title="$selection->title">
    <article class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-6 sm:p-8">
        <p class="text-sm text-slate-500">Added by {{ $selection->owner->name }}</p>
        <h1 class="break-words text-3xl font-semibold tracking-tight text-slate-900">{{ $selection->title }}</h1>
        <a href="{{ route('userzone.selections.edit', $selection) }}">Edit</a>
        <form
            action="{{ route('userzone.selections.destroy', $selection) }}" method="post"
        >
            @method('DELETE')
            @csrf
            <button type="submit" class="cursor-pointer text-sm text-red-600 hover:underline">Delete selection</button>
        </form>

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

    <h2 class="mb-3 mt-8 text-lg font-semibold text-slate-900">Criteria</h2>
    <a
        href="{{ route('userzone.selections.criteria.index', $selection) }}"
        class="mb-4 inline-block rounded-sm text-sm font-medium text-teal-700 underline-offset-4 hover:underline
               focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
    >
        Manage criteria
    </a>

    @if ($selection->criteria->isNotEmpty())
        <ul class="flex flex-wrap gap-2">
            @foreach ($selection->criteria as $criterion)
                <li
                    class="rounded-full border px-4 py-2 text-sm
                           {{ $criterion->is_required ? 'border-teal-200 bg-teal-50 text-teal-800' : 'border-slate-200 bg-slate-100 text-slate-600' }}"
                >
                    {{ $criterion->title }}
                    @if ($criterion->is_required)
                        <span title="Required" aria-hidden="true">*</span>
                        <span class="sr-only">Required</span>
                    @endif
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
                    href="{{ route('userzone.selections.accommodations.show', [$accommodation->selection_id, $accommodation]) }}"
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
