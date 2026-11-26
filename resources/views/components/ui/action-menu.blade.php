@props(['editUrl', 'deleteUrl' => null, 'label'])

<details {{ $attributes->class('relative shrink-0 text-[#43263D]') }} data-action-menu>
    <summary class="flex size-10 cursor-pointer list-none items-center justify-center rounded-xl bg-[#D8C9EA]/50 text-2xl leading-none focus-visible:outline-2 focus-visible:outline-offset-4 [&::-webkit-details-marker]:hidden" aria-label="{{ $label }} actions">
        <span aria-hidden="true">⋯</span>
    </summary>
    <div class="absolute right-0 top-full z-20 mt-2 w-36 rounded-xl border border-[#D8C9EA] bg-[#F8F3E9] p-2 shadow-sm">
        <a href="{{ $editUrl }}" class="block rounded-lg px-3 py-2 text-base hover:underline underline-offset-4 focus-visible:outline-2 focus-visible:outline-[#43263D]">Edit</a>
        @if ($deleteUrl)
            <form action="{{ $deleteUrl }}" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="block w-full cursor-pointer rounded-lg px-3 py-2 text-left text-base text-red-700 hover:underline underline-offset-4 focus-visible:outline-2 focus-visible:outline-red-700">Delete</button>
            </form>
        @endif
    </div>
</details>

@once
    <script>
        document.addEventListener('click', (event) => {
            document.querySelectorAll('[data-action-menu][open]').forEach((menu) => {
                if (!menu.contains(event.target)) menu.open = false;
            });
        });
        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') return;
            document.querySelectorAll('[data-action-menu][open]').forEach((menu) => {
                menu.open = false;
                menu.querySelector('summary').focus();
            });
        });
    </script>
@endonce
