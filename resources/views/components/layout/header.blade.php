<header class="border-b border-slate-200 bg-white">
    <nav class="mx-auto flex max-w-5xl gap-6 px-4 py-5 sm:px-6">
        @foreach ($menu as $label => $link)
            <a
                href="{{ $link }}"
                class="rounded-sm font-medium text-slate-600 hover:text-teal-700
                       focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
            >
                {{ $label }}
            </a>
        @endforeach
    </nav>
</header>
