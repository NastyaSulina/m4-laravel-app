<footer class="border-t border-[#D8C9EA] bg-[#F8F3E9] text-sm text-[#43263D]">
    <div class="mx-auto grid max-w-5xl gap-6 px-4 py-6 sm:grid-cols-[2fr_1fr_1.5fr] sm:px-6">
        <div>
            <p class="font-bold">Staylist</p>
            <p class="mt-2 max-w-xs leading-relaxed text-[#43263D]/75">
                Find your favourites. Compare your options.
                Choose your next stay together.
            </p>
        </div>

        <nav aria-label="Footer">
            <h2 class="font-bold">Legal</h2>
            <ul class="mt-2 flex flex-col gap-2">
                <li>
                    <a href="{{ route('privacy-policy') }}" class="rounded-sm underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#43263D]">Privacy Policy</a>
                </li>
            </ul>
        </nav>

        <p class="text-xs leading-relaxed text-[#43263D]/75 sm:self-center sm:text-right">
            &copy; {{ date('Y') }} Staylist. All rights reserved.
        </p>
    </div>
</footer>
