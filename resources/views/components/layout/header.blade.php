<header>
    <nav>
        @foreach ($menu as $label => $link)
            <a href="{{ $link }}">{{ $label }}</a>
        @endforeach
    </nav>
    <hr>
</header>
