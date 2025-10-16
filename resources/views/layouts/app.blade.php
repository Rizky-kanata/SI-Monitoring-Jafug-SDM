<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased text-slate-900">
<div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
    <aside class="flex min-h-full bg-gradient-to-b from-slate-900 to-slate-950">
        @hasSection('sidebar')
            @yield('sidebar')
        @else
            <x-sidebar class="h-full" />
        @endif
    </aside>

    <main class="flex min-h-screen flex-col gap-8 p-6 lg:p-10">
        @yield('content')
    </main>
</div>

@stack('modals')
@stack('scripts')
</body>
</html>
