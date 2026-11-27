<x-base-layout :title="'Share selection — '.$selection->title">
    <div class="max-w-2xl text-ink">
        <x-ui.button :href="route('userzone.selections.show', $selection)" variant="tertiary" class="mb-6 gap-2">← Back to selection</x-ui.button>

        <h1 class="mb-3 break-words text-3xl font-semibold leading-tight tracking-tight">Share {{ $selection->title }}</h1>
        <p class="text-sm text-muted">People you add can edit this selection.</p>

        <ul class="mb-8 mt-5 flex flex-col gap-3">
            @forelse ($users as $user)
                <li class="flex flex-wrap items-center gap-4 rounded-2xl bg-surface p-5">
                    <div class="min-w-0 flex-1 break-words">
                        <p class="font-medium">{{ $user->name }}</p>
                        <p class="mt-1 text-sm text-muted">{{ $user->email }}</p>
                    </div>
                    <form action="{{ route('userzone.selections.users.destroy', [$selection, $user]) }}" method="post">
                        @csrf
                        @method('DELETE')
                        <x-ui.button type="submit" variant="tertiary" danger>Remove access</x-ui.button>
                    </form>
                </li>
            @empty
                <li class="rounded-2xl bg-surface p-5 text-sm text-muted">Only you have access to this selection.</li>
            @endforelse
        </ul>

        <section class="rounded-2xl bg-surface p-5 sm:p-6">
            <p class="mb-5 text-sm text-muted">Enter the email of someone who already has a Staylist account.</p>
            <form action="{{ route('userzone.selections.users.store', $selection) }}" method="post" class="flex flex-col gap-5">
                @csrf
                <x-form.textinput name="email" type="email" label="Email" maxlength="255" required />
                <x-ui.button type="submit" class="self-start">Share selection</x-ui.button>
            </form>
        </section>
    </div>
</x-base-layout>
