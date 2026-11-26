<x-base-layout title="Check criteria" class="bg-[#F8F3E9]!">
    <div class="max-w-2xl text-[#43263D]">
        <x-ui.button :href="route('userzone.selections.accommodations.show', [$selection, $accommodation])" variant="tertiary" class="mb-6 gap-2 rounded-none! border-b border-current no-underline!">← Back to accommodation</x-ui.button>

        <h1 class="mb-2 font-serif text-3xl font-semibold leading-tight">Check criteria</h1>
        <p class="mb-6 break-words text-sm text-[#43263D]/75">{{ $accommodation->title }}</p>

        @if ($errors->any())
            <p role="alert" class="mb-6 text-sm text-red-700">Some criteria or answers are invalid. Reload the page and try again.</p>
        @endif

        @if ($selection->criteria->isEmpty())
            <p class="mb-4 text-sm">No criteria added yet.</p>
            <x-ui.button :href="route('userzone.selections.criteria.index', $selection)" variant="tertiary">Add criteria to selection</x-ui.button>
        @else
            <form action="{{ route('userzone.selections.accommodations.criteria.update', [$selection, $accommodation]) }}" method="post" class="flex flex-col gap-4">
                @csrf
                @method('PUT')
                @foreach ($selection->criteria as $index => $criterion)
                    @php($answer = old('criteria.'.$index.'.is_met', $accommodation->criteria->firstWhere('id', $criterion->id)?->pivot->is_met) ?? '')
                    <div class="grid items-center gap-2 border-b border-[#D8C9EA] pb-4 sm:grid-cols-2 sm:gap-6">
                        <label for="criterion-{{ $criterion->id }}" class="min-w-0 break-words font-medium">
                            {{ $criterion->title }}
                        </label>

                        <input type="hidden" name="criteria[{{ $index }}][id]" value="{{ $criterion->id }}">
                        <select id="criterion-{{ $criterion->id }}" name="criteria[{{ $index }}][is_met]" class="w-full rounded-md border border-[#D8C9EA] bg-white px-3 py-2 text-base focus:outline-2 focus:outline-[#43263D]">
                            @foreach (['' => 'Not checked', '1' => 'Meets requirement', '0' => 'Does not meet requirement'] as $value => $label)
                                <option value="{{ $value }}" @selected(in_array($answer, [$value, (string) $value], true))>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach

                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <x-ui.button type="submit">Save changes</x-ui.button>
                    <x-ui.button :href="route('userzone.selections.accommodations.show', [$selection, $accommodation])" variant="tertiary">Cancel</x-ui.button>
                </div>
            </form>
        @endif
    </div>
</x-base-layout>
