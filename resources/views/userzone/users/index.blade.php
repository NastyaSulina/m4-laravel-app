<x-base-layout :title="'Shared with — '.$selection->title" class="bg-[#F8F3E9]!">
    <div class="mx-auto max-w-2xl text-[#43263D]">
        <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary" class="mb-6">← Back to selection</x-ui.button>

        <h1 class="mb-2 font-serif text-3xl font-semibold">Shared with</h1>
        <p class="mb-4 break-words">{{ $selection->title }}</p>

        <ul class="my-6 divide-y divide-[#D8C9EA]">
            @forelse ($users as $user)
                <li class="flex flex-wrap items-center gap-4 py-3">
                    <span class="min-w-0 flex-1 break-words">{{ $user->name }} — {{ $user->email }}</span>
                    <form action="{{ route('userzone.selections.users.destroy', [$selection, $user]) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <x-ui.button type="submit" variant="tertiary" danger>Remove access</x-ui.button>
                    </form>
                </li>
            @empty
                <li class="py-3">Only you have access to this selection.</li>
            @endforelse
        </ul>

        <form action="{{ route('userzone.selections.users.store', $selection) }}" method="post" class="flex flex-col gap-4">
            @csrf
            <x-form.textinput name="email" type="email" label="Email" maxlength="255" required />
            <x-ui.button type="submit" class="self-start">Add user</x-ui.button>
        </form>
    </div>
</x-base-layout>
