<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="[scrollbar-gutter:stable]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-700 antialiased">
    <x-layout.header />
    <main class="mx-auto w-full max-w-5xl flex-1 px-4 py-10 sm:px-6">
        {{ $slot }}
    </main>
    <x-layout.footer />
</body>
</html>
