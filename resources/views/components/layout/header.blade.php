<header class="border-b border-[#D8C9EA] bg-[#F8F3E9] text-[#43263D]">
    <nav aria-label="Main navigation" class="grid grid-cols-[1fr_auto] items-center gap-x-4 gap-y-2 px-4 py-2 sm:grid-cols-[1fr_auto_1fr] sm:px-10">
        <a href="{{ route('welcome') }}" class="col-start-1 row-start-1 justify-self-start rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#43263D]">
            <img src="{{ asset('img/staylist-logo.webp') }}" alt="{{ config('app.name') }}" width="432" height="144" class="h-auto w-28 sm:w-36">
        </a>

        @auth
            <a href="{{ route('userzone.selections.index') }}"
               @if (request()->routeIs('userzone.selections.*')) aria-current="page" @endif
               class="col-span-2 row-start-2 justify-self-center rounded-lg px-3 py-2 text-sm font-medium hover:bg-[#D8C9EA]/30 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#43263D] sm:col-span-1 sm:col-start-2 sm:row-start-1 sm:text-base">
                Selections
            </a>
        @endauth

        <div class="col-start-2 row-start-1 flex min-w-0 flex-wrap items-center justify-end gap-x-4 gap-y-2 sm:col-start-3 sm:gap-x-6">
            @foreach ($menu as $label => $link)
                <a
                    href="{{ $link }}"
                    class="rounded-sm text-sm hover:underline underline-offset-4
                           focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#43263D]"
                >
                    {{ $label }}
                </a>
            @endforeach

            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="cursor-pointer rounded-sm text-sm hover:underline underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#43263D]">
                        Log out
                    </button>
                </form>
            @endauth
        </div>
    </nav>
</header>
