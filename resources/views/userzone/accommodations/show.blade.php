<x-base-layout :title="$accommodation->title" class="bg-[#F8F3E9]!">
    <x-ui.button
        :href="route('userzone.selections.show', $accommodation->selection_id)"
        variant="tertiary" class="mb-6 gap-2 rounded-none! border-b border-current no-underline!"
    ><span>←</span> Back to selection</x-ui.button>

    <article class="flex flex-col gap-8 text-[#43263D]">
        <div class="flex items-center justify-between gap-4">
            <h1 class="break-words font-serif text-3xl font-semibold leading-tight">{{ $accommodation->title }}</h1>
            <x-ui.button
                :href="route('userzone.selections.accommodations.edit', [$accommodation->selection_id, $accommodation])"
                variant="secondary" class="shrink-0"
            >Edit</x-ui.button>
        </div>

        <div class="flex items-center gap-6 border-b border-[#D8C9EA] pb-6 text-base">
            <p class="font-semibold">Property type: {{ ucfirst($accommodation->property_type) }}</p>
            <p class="border-l border-[#D8C9EA] pl-6">Status: {{ ucfirst($accommodation->status) }}</p>
            <p class="border-l border-[#D8C9EA] pl-6">Added by <strong>{{ $accommodation->user->name }}</strong></p>
        </div>

        <div class="grid grid-cols-3 items-start gap-8">
            <img
                src="{{ $accommodation->getFirstMediaUrl('images') }}" alt="{{ $accommodation->title }}"
                class="aspect-square w-full rounded-xl object-cover"
            >
            <div class="col-span-2 flex min-w-0 flex-col gap-6 text-base">
                <p class="break-words"><span class="block font-serif text-xl font-semibold">Address: </span>{{ $accommodation->address }}</p>
                <p class="break-all">
                    <span class="block font-serif text-xl font-semibold">Source</span>
                    <a href="{{ $accommodation->source_url }}" class="underline underline-offset-4">{{ parse_url($accommodation->source_url, PHP_URL_HOST) }} ↗</a>
                </p>
                @if ($accommodation->note)
                    <p><span class="mb-2 block font-serif text-xl font-semibold">Note: </span><span class="block whitespace-pre-line break-words rounded-xl bg-[#D8C9EA]/50 px-4 py-3">{{ $accommodation->note }}</span></p>
                @endif
            </div>
        </div>

        <form action="{{ route('userzone.selections.accommodations.destroy', [$accommodation->selection_id, $accommodation]) }}" method="post" class="border-t border-[#D8C9EA] pt-6">
            @method('DELETE')
            @csrf
            <x-ui.button type="submit" variant="tertiary" danger>Delete accommodation</x-ui.button>
        </form>
    </article>
</x-base-layout>
