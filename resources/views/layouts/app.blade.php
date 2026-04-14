<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/logo telu.png') }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @php
    $viteManifestExists = file_exists(public_path('build/manifest.json'));
    @endphp

    @if($viteManifestExists)
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="min-h-screen bg-slate-50 font-sans antialiased text-slate-900">
    @if($viteManifestExists)
    <div class="min-h-screen bg-slate-50">
        <header class="sticky top-0 z-30 flex items-center gap-4 bg-slate-900 px-6 py-4 text-white shadow-sm">
            <button
                type="button"
                id="sidebar-toggle"
                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/30 bg-white/10 transition hover:bg-white/20"
                aria-label="Buka menu navigasi"
                aria-expanded="false"
            >
                <span class="sr-only">Toggle menu</span>
                <span class="flex h-4 w-5 flex-col justify-between">
                    <span class="block h-0.5 w-full rounded bg-white"></span>
                    <span class="block h-0.5 w-full rounded bg-white"></span>
                    <span class="block h-0.5 w-full rounded bg-white"></span>
                </span>
            </button>
            <div class="flex flex-col leading-tight">
                <span class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Portal SDM</span>
                <span class="text-lg font-semibold text-white">Monitoring Kepangkatan</span>
            </div>
        </header>

        <div id="sidebar-scrim" class="fixed inset-0 z-30 hidden bg-slate-950/60"></div>

        <aside
            id="sidebar-drawer"
            class="fixed inset-y-0 left-0 z-40 w-[280px] -translate-x-full bg-gradient-to-b from-slate-900 to-slate-950 transition-transform duration-200"
        >
            <div class="flex h-full">
                @hasSection('sidebar')
                @yield('sidebar')
                @else
                <x-sidebar class="h-full" />
                @endif
            </div>
        </aside>

        <main class="flex min-h-screen flex-col gap-8 p-6 lg:p-10">
            @yield('content')
        </main>
    </div>
    @else
    <div class="flex min-h-screen items-center justify-center bg-white px-6 text-center text-slate-700">
        <div class="max-w-xl space-y-4">
            <h1 class="text-2xl font-semibold text-slate-900">Build assets dulu sebelum dashboard dibuka</h1>
            <p>
                File build belum ada di <code>public/build/manifest.json</code>. Jalankan perintah berikut dari root project:
            </p>
            <ol class="list-decimal space-y-1 text-left">
                <li><code>npm install</code></li>
                <li>
                    Pakai <code>npm run dev</code> saat develop atau <code>npm run build</code> sebelum project diserve.
                </li>
            </ol>
            <p>Kalau manifest udah kebentuk, tinggal reload halaman ini.</p>
        </div>
    </div>
    @endif

    @stack('modals')
    @if($viteManifestExists)
    <script>
        (function () {
            const toggle = document.getElementById('sidebar-toggle');
            const drawer = document.getElementById('sidebar-drawer');
            const scrim = document.getElementById('sidebar-scrim');
            if (!toggle || !drawer || !scrim) return;

            const openDrawer = () => {
                drawer.classList.remove('-translate-x-full');
                scrim.classList.remove('hidden');
                toggle.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            };

            const closeDrawer = () => {
                drawer.classList.add('-translate-x-full');
                scrim.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            };

            toggle.addEventListener('click', () => {
                if (drawer.classList.contains('-translate-x-full')) {
                    openDrawer();
                } else {
                    closeDrawer();
                }
            });

            scrim.addEventListener('click', closeDrawer);

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeDrawer();
                }
            });
        })();
    </script>
    @endif
    @stack('scripts')
</body>

</html>
