<footer class="bg-chrome/60 text-sm text-ink">
    <div class="mx-auto grid max-w-5xl gap-6 px-5 py-8 sm:grid-cols-[2fr_1.5fr_1.5fr] sm:px-8">
        <div>
            <a href="{{ route('welcome') }}" class="rounded-sm font-bold underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink">{{ config('app.name') }}</a>
            <p class="mt-2 max-w-xs leading-relaxed text-muted">
                Find your favourites. Compare your options.
                Choose your next stay together.
            </p>
        </div>

        <nav aria-label="Footer" class="flex flex-wrap gap-6">
            @foreach ($menu as $group => $links)
                <div>
                    <h2 class="font-bold">{{ $group }}</h2>
                    <ul class="mt-2 flex flex-col gap-2">
                        @foreach ($links as $label => $link)
                            <li>
                                <a href="{{ $link }}" class="rounded-sm underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink">{{ $label }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        <p class="text-sm leading-relaxed text-muted sm:self-center sm:text-right">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</footer>
