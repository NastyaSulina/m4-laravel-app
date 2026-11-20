<header class="border-b border-[#D8C9EA] bg-[#F8F3E9] text-[#43263D]">
    <nav class="flex flex-wrap items-center justify-between gap-4 px-4 py-2 sm:px-10">
        <a href="{{ route('welcome') }}" class="shrink-0 rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#43263D]">
            <img src="{{ asset('img/staylist-logo.png') }}" alt="Staylist" class="h-auto w-36 sm:w-48">
        </a>

        <div class="flex min-w-0 flex-wrap items-center gap-x-6 gap-y-3 sm:gap-x-10">
            @foreach ($menu as $label => $link)
                <a
                    href="{{ $link }}"
                    class="rounded-sm text-base first:font-semibold hover:underline underline-offset-4 sm:text-xl
                           focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#43263D]"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>
</header>
