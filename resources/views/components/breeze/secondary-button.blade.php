<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex cursor-pointer items-center justify-center rounded-xl bg-soft px-4 py-2.5 text-sm font-medium text-ink transition-colors hover:bg-line focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-ink disabled:opacity-50']) }}>
    {{ $slot }}
</button>
