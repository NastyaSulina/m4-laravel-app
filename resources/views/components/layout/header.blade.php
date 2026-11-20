<header class="border-b border-slate-200 bg-white">
    <nav class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-4 px-4 py-2 sm:px-6">
        <a href="{{ route('welcome') }}" class="shrink-0 rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700">
            <img src="{{ asset('img/staylist-logo.png') }}" alt="Staylist" class="h-auto w-32">
        </a>

        <div class="flex min-w-0 flex-wrap items-center gap-x-6 gap-y-3">
            @foreach ($menu as $label => $link)
                <a
                    href="{{ $link }}"
                    class="rounded-sm font-medium text-slate-600 hover:text-teal-700
                           focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>
</header>
