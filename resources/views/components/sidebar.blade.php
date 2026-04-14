@props(['links' => []])

@php
    $defaultLinks = [
        [
            'label' => 'Dashboard',
            'href' => route('dashboard'),
            'active' => request()->routeIs('dashboard'),
        ],
        [
            'label' => 'Data Profil Dosen',
            'href' => route('profils.index'),
            'active' => request()->routeIs('profils.*'),
        ],
        [
            'label' => 'Data Kepangkatan',
            'href' => route('kepangkatan.index'),
            'active' => request()->routeIs('kepangkatan.*'),
        ],
    ];

    $navLinks = collect(empty($links) ? $defaultLinks : $links)
        ->map(fn ($link) => [
            'label' => $link['label'] ?? '',
            'href' => $link['href'] ?? '#',
            'active' => (bool) ($link['active'] ?? false),
            'new_tab' => (bool) ($link['new_tab'] ?? false),
        ])
        ->all();
@endphp

<div {{ $attributes->class('flex h-full flex-col gap-10 px-6 py-8 text-white lg:px-8') }}>
    <div class="flex flex-col items-start gap-4">
        <img
            src="{{ asset('images/TelU Sby-1.png') }}"
            alt="Telkom University Surabaya"
            class="w-full max-w-[210px] h-auto object-contain"
        >
        <div class="space-y-1">
            <h1 class="text-lg font-semibold leading-tight">Admin Dosen SDM</h1>
            <p class="text-sm text-slate-200/80">Pengelolaan data dosen dan kepangkatan.</p>
        </div>
    </div>

    <nav class="space-y-2">
        @foreach ($navLinks as $link)
            @php
                $isActive = $link['active'] ?? false;
                $href = $link['href'] ?? '#';
                $openInNewTab = $link['new_tab'] ?? false;
            @endphp
            <a
                href="{{ $href }}"
                @if ($openInNewTab)
                    target="_blank" rel="noopener noreferrer"
                @endif
                @class([
                    'flex items-center rounded-full px-6 py-3 text-base font-semibold transition',
                    'bg-white/10 text-white shadow-sm backdrop-blur hover:bg-white/15' => $isActive,
                    'text-slate-200/90 hover:bg-white/10 hover:text-white' => ! $isActive,
                ])
            >
                {{ $link['label'] ?? '' }}
            </a>
        @endforeach

        {{ $slot }}
    </nav>

    <form method="POST" action="{{ route('logout') }}" class="mt-auto sticky bottom-8 z-20">
        @csrf
        <button
            type="submit"
            class="w-full rounded-2xl bg-white/10 px-4 py-2 text-left text-sm font-medium text-white transition hover:bg-white/15"
        >
            Logout
        </button>
    </form>
</div>
