<header class="bg-chrome text-ink">
    <nav aria-label="Main navigation" class="grid grid-cols-[1fr_auto] items-center gap-x-4 gap-y-1 mx-auto max-w-5xl px-5 py-3 sm:grid-cols-[1fr_auto_1fr] sm:px-8">
        <a href="{{ route('welcome') }}" class="col-start-1 row-start-1 justify-self-start rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink">
            <img src="{{ asset('img/staylist-logo.webp') }}" alt="{{ config('app.name') }}" width="432" height="144" class="h-auto w-28 sm:w-32">
        </a>

        @auth
            <a href="{{ route('userzone.selections.index') }}"
               @if (request()->routeIs('userzone.selections.*')) aria-current="page" @endif
               class="col-span-2 row-start-2 justify-self-center py-2 text-sm font-medium underline-offset-8 hover:underline aria-[current=page]:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink sm:col-span-1 sm:col-start-2 sm:row-start-1">
                Selections
            </a>
        @endauth

        <div class="col-start-2 row-start-1 flex min-w-0 flex-wrap items-center justify-end gap-x-4 gap-y-2 sm:col-start-3 sm:gap-x-6">
            @foreach ($menu as $label => $link)
                <a
                    href="{{ $link }}"
                    class="rounded-sm text-sm hover:underline underline-offset-4
                           focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink"
                >
                    {{ $label }}
                </a>
            @endforeach

            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="cursor-pointer rounded-sm text-sm hover:underline underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink">
                        Log out
                    </button>
                </form>
            @endauth
        </div>
    </nav>
</header>
