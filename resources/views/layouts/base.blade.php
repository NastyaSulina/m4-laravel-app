<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="[scrollbar-gutter:stable]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? "{$title} | Staylist" : "Staylist" }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.1/dist/cdn.min.js"></script>
    @endif
</head>
<body {{ $attributes->merge(['class' => 'flex min-h-screen flex-col bg-[#F8F3E9] font-sans text-slate-700 antialiased']) }}>
    <x-layout.header />
    <main class="mx-auto w-full max-w-5xl flex-1 px-4 py-10 sm:px-6">
        {{ $slot }}
    </main>
    <x-layout.footer />
</body>
</html>
