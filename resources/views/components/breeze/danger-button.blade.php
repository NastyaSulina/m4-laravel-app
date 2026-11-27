<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex cursor-pointer items-center justify-center rounded-xl bg-red-700 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-red-800 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-red-700']) }}>
    {{ $slot }}
</button>
