<x-base-layout class="[&>main]:flex [&>main]:flex-col [&>main]:justify-center">
    <section class="mx-auto w-full max-w-2xl text-ink">
        <h1 class="max-w-3xl text-3xl font-semibold leading-tight tracking-tight">All your possible stays,<br class="hidden sm:block"> in one place.</h1>
        <p class="mt-4 max-w-xl text-base leading-relaxed text-muted">Keep accommodation links together, organise them into selections, and choose with the people you trust.</p>

        @guest
            <div class="mt-8 flex flex-wrap items-center gap-3">
                <x-ui.button :href="route('login')">Sign in</x-ui.button>
                <x-ui.button :href="route('register')" variant="tertiary">Create an account</x-ui.button>
            </div>
        @else
            <div class="mt-8 rounded-2xl bg-surface p-5 sm:p-6">
                @if ($latestSelection)
                    <p class="mb-2 text-sm font-medium text-muted">Continue planning</p>
                    <h2 class="break-words text-xl font-semibold">{{ $latestSelection['title'] }}</h2>
                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <x-ui.button :href="route('userzone.selections.show', $latestSelection['id'])">Open selection →</x-ui.button>
                        <x-ui.button :href="route('userzone.selections.create')" variant="secondary">+ New selection</x-ui.button>
                    </div>
                @else
                    <h2 class="text-xl font-semibold">Start a new selection</h2>
                    <p class="mt-2 text-muted">Planning a trip or looking for your next home? Give your search a place to start.</p>
                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <x-ui.button :href="route('userzone.selections.create')">+ New selection</x-ui.button>
                    </div>
                @endif
            </div>
        @endguest
    </section>

</x-base-layout>
