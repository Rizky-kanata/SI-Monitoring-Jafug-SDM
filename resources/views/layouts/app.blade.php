<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @php($viteManifestExists = file_exists(public_path('build/manifest.json')))

    @if($viteManifestExists)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased text-slate-900">
@if($viteManifestExists)
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
@else
    <div class="flex min-h-screen items-center justify-center bg-white px-6 text-center text-slate-700">
        <div class="max-w-xl space-y-4">
            <h1 class="text-2xl font-semibold text-slate-900">Build assets before loading the dashboard</h1>
            <p>
                The Vite manifest could not be found at <code>public/build/manifest.json</code>. To generate it, run the
                following commands from the project root:
            </p>
            <ol class="list-decimal space-y-1 text-left">
                <li><code>npm install</code></li>
                <li>
                    Either <code>npm run dev</code> while developing or <code>npm run build</code> before serving the
                    compiled assets.
                </li>
            </ol>
            <p>Once the manifest exists you can reload this page.</p>
        </div>
    </div>
@endif

@stack('modals')
@stack('scripts')
</body>
</html>
