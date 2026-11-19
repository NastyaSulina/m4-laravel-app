<x-base-layout :title="$selection->title">
    <article class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-6 sm:p-8">
        <p class="text-sm text-slate-500">Added by {{ $selection->owner->name }}</p>
        <h1 class="break-words text-3xl font-semibold tracking-tight text-slate-900">{{ $selection->title }}</h1>
        <x-ui.button :href="route('userzone.selections.edit', $selection)" variant="secondary" class="self-start">Edit</x-ui.button>
        <form
            action="{{ route('userzone.selections.destroy', $selection) }}" method="post"
        >
            @method('DELETE')
            @csrf
            <x-ui.button type="submit" variant="tertiary" danger>Delete selection</x-ui.button>
        </form>

        <p>Search type: {{ ['short_stay' => 'Short stay', 'long_term' => 'Long-term rental'][$selection->search_type] }}</p>
        <p>Status: {{ ucfirst($selection->status) }}</p>
        @if ($selection->start_date)
            <p>Start date: {{ $selection->start_date }}</p>
        @endif
        @if ($selection->end_date)
            <p>End date: {{ $selection->end_date }}</p>
        @endif

        <x-ui.button :href="route('userzone.selections.index')" variant="tertiary" class="mt-4 self-start">Back to selections</x-ui.button>
    </article>

    <h2 class="mb-3 mt-8 text-lg font-semibold text-slate-900">Criteria</h2>
    <x-ui.button :href="route('userzone.selections.criteria.index', $selection)" variant="secondary" class="mb-4">Manage criteria</x-ui.button>

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

    <x-ui.button :href="route('userzone.selections.accommodations.create', $selection)" class="mb-5">New accommodation</x-ui.button>

    <div class="grid gap-5 sm:grid-cols-2">
        @forelse ($selection->accommodations as $accommodation)
            <article class="flex min-w-0 flex-col gap-3 rounded-xl border border-slate-200 bg-white p-6">
                <img
                    src="{{ $accommodation->getFirstMediaUrl('images', 'thumbnail') }}" alt="{{ $accommodation->title }}"
                    width="128" height="96" loading="lazy"
                    class="h-24 w-32 rounded-lg border border-slate-200 bg-slate-50 object-cover"
                >
                <h3 class="break-words text-xl font-semibold text-slate-900">{{ $accommodation->title }}</h3>
                <p>Address: {{ $accommodation->address }}</p>
                <p>Status: {{ ucfirst($accommodation->status) }}</p>
                <p>Property type: {{ ucfirst($accommodation->property_type) }}</p>
                <x-ui.button
                    :href="route('userzone.selections.accommodations.show', [$accommodation->selection_id, $accommodation])"
                    variant="tertiary" class="mt-auto self-start pt-3"
                >View accommodation</x-ui.button>
            </article>
        @empty
            <p class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-slate-500 sm:col-span-2">
                No accommodations yet.
            </p>
        @endforelse
    </div>
</x-base-layout>
