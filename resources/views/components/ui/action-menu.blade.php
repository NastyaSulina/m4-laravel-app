@props(['editUrl', 'deleteUrl' => null, 'criteriaEditUrl' => null, 'label'])

<details {{ $attributes->class('relative shrink-0 text-ink') }} data-action-menu>
    <summary class="flex size-10 cursor-pointer list-none items-center justify-center rounded-xl bg-soft text-xl leading-none focus-visible:outline-2 focus-visible:outline-offset-4 [&::-webkit-details-marker]:hidden" aria-label="{{ $label }} actions">
        <span aria-hidden="true">⋯</span>
    </summary>
    <div @class(['absolute right-0 top-full z-20 mt-2 rounded-xl border border-line bg-surface p-2 shadow-sm', 'w-52' => $criteriaEditUrl, 'w-36' => ! $criteriaEditUrl])>
        <a href="{{ $editUrl }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-soft underline-offset-4 focus-visible:outline-2 focus-visible:outline-ink">Edit</a>
        @if ($criteriaEditUrl)
            <a href="{{ $criteriaEditUrl }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-soft underline-offset-4 focus-visible:outline-2 focus-visible:outline-ink">Check criteria</a>
        @endif
        @if ($deleteUrl)
            <form action="{{ $deleteUrl }}" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="block w-full cursor-pointer rounded-lg px-3 py-2 text-left text-sm text-red-700 hover:bg-soft underline-offset-4 focus-visible:outline-2 focus-visible:outline-red-700">Delete</button>
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
