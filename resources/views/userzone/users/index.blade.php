<x-base-layout :title="'Share selection — '.$selection->title" class="bg-[#F8F3E9]!">
    <div class="max-w-2xl text-[#43263D]">
        <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary" class="mb-6 gap-2 rounded-none! border-b border-current no-underline!">← Back to selection</x-ui.button>

        <h1 class="mb-6 break-words font-serif text-3xl font-semibold leading-tight">Share {{ $selection->title }}</h1>
        <p class="text-sm text-[#43263D]/75">People you add can edit this selection.</p>

        <ul class="mb-8 mt-4 divide-y divide-[#D8C9EA] border-y border-[#D8C9EA]">
            @forelse ($users as $user)
                <li class="flex flex-wrap items-center gap-4 py-3">
                    <div class="min-w-0 flex-1 break-words">
                        <p class="font-medium">{{ $user->name }}</p>
                        <p class="mt-1 text-sm text-[#43263D]/75">{{ $user->email }}</p>
                    </div>
                    <form action="{{ route('userzone.selections.users.destroy', [$selection, $user]) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <x-ui.button type="submit" variant="tertiary" danger>Remove access</x-ui.button>
                    </form>
                </li>
            @empty
                <li class="py-5 text-sm text-[#43263D]/75">Only you have access to this selection.</li>
            @endforelse
        </ul>

        <p class="mb-5 text-sm text-[#43263D]/75">Enter the email of someone who already has a Staylist account.</p>
        <form action="{{ route('userzone.selections.users.store', $selection) }}" method="post" class="flex flex-col gap-4">
            @csrf
            <x-form.textinput name="email" type="email" label="Email" maxlength="255" required />
            <x-ui.button type="submit" class="self-start">Share selection</x-ui.button>
        </form>
    </div>
</x-base-layout>
