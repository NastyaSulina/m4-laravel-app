<x-base-layout title="Welcome">
    <h1 class="mb-6 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Welcome</h1>

    <a
        href="{{ route('userzone.selections.index') }}"
        class="inline-block rounded-lg bg-teal-700 px-5 py-3 font-medium text-white hover:bg-teal-800
               focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-teal-700"
    >
        Selections
    </a>
</x-base-layout>
