<x-base-layout class="min-h-dvh! [&>main]:flex [&>main]:flex-col [&>main]:justify-center [&>main]:py-[clamp(1rem,4vh,3rem)]">
    <section class="text-[#43263D]">
        <h1 class="max-w-3xl font-serif text-4xl font-semibold leading-tight sm:text-5xl">All your possible stays,<br class="hidden sm:block"> in one place.</h1>
        <p class="mt-5 max-w-2xl text-lg leading-relaxed text-[#43263D]/75">Keep accommodation links together, organise them into selections, and choose with the people you trust.</p>

        @guest
            <div class="mt-8 flex flex-wrap items-center gap-5">
                <x-ui.button :href="route('login')">Sign in</x-ui.button>
                <x-ui.button :href="route('register')" variant="tertiary">Create an account</x-ui.button>
            </div>
        @else
            <div class="mt-8 rounded-2xl border border-[#D8C9EA] bg-white/30 p-6">
                @if ($latestSelection)
                    <p class="mb-2 text-sm font-medium text-[#43263D]/70">Continue planning</p>
                    <h2 class="break-words font-serif text-2xl font-semibold">{{ $latestSelection['title'] }}</h2>
                    <div class="mt-5 flex flex-wrap items-center gap-5">
                        <x-ui.button :href="route('userzone.selections.show', $latestSelection['id'])">Open selection →</x-ui.button>
                        <x-ui.button :href="route('userzone.selections.create')" variant="secondary">+ New selection</x-ui.button>
                    </div>
                @else
                    <h2 class="font-serif text-2xl font-semibold">Start a new selection</h2>
                    <p class="mt-2 text-[#43263D]/75">Planning a trip or looking for your next home? Give your search a place to start.</p>
                    <div class="mt-5 flex flex-wrap items-center gap-5">
                        <x-ui.button :href="route('userzone.selections.create')">+ New selection</x-ui.button>
                    </div>
                @endif
            </div>
        @endguest
    </section>

</x-base-layout>
