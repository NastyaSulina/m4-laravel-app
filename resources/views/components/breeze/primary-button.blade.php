<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex cursor-pointer items-center justify-center rounded-xl bg-ink px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-ink/90 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink']) }}>
    {{ $slot }}
</button>
