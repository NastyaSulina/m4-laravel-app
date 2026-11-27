<x-base-layout title="Check criteria">
    <div class="max-w-2xl text-ink">
        <x-ui.button :href="route('userzone.selections.accommodations.show', [$selection, $accommodation])" variant="tertiary" class="mb-6 gap-2">← Back to accommodation</x-ui.button>

        <h1 class="mb-2 text-3xl font-semibold leading-tight tracking-tight">Check criteria</h1>
        <p class="mb-6 break-words text-sm text-muted">{{ $accommodation->title }}</p>

        @if ($errors->any())
            <p role="alert" class="mb-6 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">Some criteria or answers are invalid. Reload the page and try again.</p>
        @endif

        @if ($selection->criteria->isEmpty())
            <section class="rounded-2xl bg-surface p-5 sm:p-6">
                <h2 class="text-xl font-semibold">No criteria added yet.</h2>
                <p class="mt-2 text-sm leading-relaxed text-muted">Add what matters to this selection, such as parking or a quiet room. Then return here to check how this place matches.</p>
                <x-ui.button :href="route('userzone.selections.criteria.index', $selection)" class="mt-5">Add criteria to selection</x-ui.button>
            </section>
        @else
            <form action="{{ route('userzone.selections.accommodations.criteria.update', [$selection, $accommodation]) }}" method="post" class="flex flex-col gap-6 rounded-2xl bg-surface p-5 sm:p-6">
                @csrf
                @method('PUT')
                @php($answers = $accommodation->criteria->pluck('pivot.is_met', 'id'))
                @foreach ($selection->criteria as $index => $criterion)
                    @php($answer = old('criteria.'.$index.'.is_met', $answers->get($criterion->id)) ?? '')
                    <div class="grid items-center gap-3 sm:grid-cols-[minmax(0,1fr)_minmax(0,1.3fr)] sm:gap-6">
                        <label for="criterion-{{ $criterion->id }}" class="min-w-0 break-words text-sm font-medium">
                            {{ $criterion->title }}
                        </label>

                        <input type="hidden" name="criteria[{{ $index }}][id]" value="{{ $criterion->id }}">
                        <select id="criterion-{{ $criterion->id }}" name="criteria[{{ $index }}][is_met]" class="w-full rounded-xl border border-line bg-surface py-2.5 pl-3.5 pr-10 text-base text-ink focus:border-ink focus:ring-2 focus:ring-ink/10">
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
