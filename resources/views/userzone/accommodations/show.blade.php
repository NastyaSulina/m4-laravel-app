<x-base-layout :title="$accommodation->title">
    <article class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-6 sm:p-8">
        <x-ui.button
            :href="route('userzone.selections.show', $accommodation->selection_id)"
            variant="tertiary" class="self-start"
        >Back to selection</x-ui.button>

        <div class="flex items-center justify-between gap-4">
            <h1 class="break-words text-3xl font-semibold tracking-tight text-slate-900">{{ $accommodation->title }}</h1>
            <x-ui.button
                :href="route('userzone.selections.accommodations.edit', [$accommodation->selection_id, $accommodation])"
                variant="secondary" class="self-start"
            >Edit</x-ui.button>
        </div>

        <div class="flex items-center gap-4">
            <p>Property type: {{ ucfirst($accommodation->property_type) }}</p>
            <p>Status: {{ ucfirst($accommodation->status) }}</p>
            <p class="text-sm text-slate-500">Added by {{ $accommodation->user->name }}</p>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <img
                src="{{ $accommodation->getFirstMediaUrl('images') }}" alt="{{ $accommodation->title }}"
                class="h-auto max-h-96 w-full rounded-lg border border-slate-200 bg-slate-50 object-contain"
            >
            <div class="col-span-2 flex flex-col gap-4">
                <p>Address: {{ $accommodation->address }}</p>
                <p class="break-all">Source: {{ $accommodation->source_url }}</p>
                @if ($accommodation->note)
                    <p class="whitespace-pre-line break-words">Note: {{ $accommodation->note }}</p>
                @endif
            </div>
        </div>

        <form action="{{ route('userzone.selections.accommodations.destroy', [$accommodation->selection_id, $accommodation]) }}" method="post">
            @method('DELETE')
            @csrf
            <x-ui.button type="submit" variant="tertiary" danger>Delete accommodation</x-ui.button>
        </form>
    </article>
</x-base-layout>
