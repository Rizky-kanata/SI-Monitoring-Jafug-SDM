@extends('layouts.app')

@section('title', 'Data Kepangkatan Dosen')

@section('sidebar')
    <x-sidebar class="h-full" />
@endsection

@section('content')
    @php
        $totalProfil = $metrics['totalProfil'] ?? 0;
        $totalKepangkatan = $metrics['totalKepangkatan'] ?? 0;
        $totalPublished = $metrics['totalPublished'] ?? 0;
    @endphp

    <div class="rounded-3xl bg-white p-8 shadow-xl ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold text-slate-900">Manajemen Kepangkatan Dosen</h1>
                <p class="text-sm text-slate-500">Pantau proses kenaikan pangkat (TMT) dan catatan tindak lanjut untuk setiap dosen KK RIIB.</p>
            </div>
            <div class="flex flex-col items-center gap-3 md:flex-row">
                <a
                    href="{{ route('kepangkatan.create') }}"
                    class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                >
                    + Tambah Data Kepangkatan
                </a>
                <div class="inline-flex items-center gap-3 rounded-3xl bg-slate-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500 ring-1 ring-slate-200 shadow-sm">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-2xl bg-slate-900 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 9h18M4.5 7.5h15a1.5 1.5 0 0 1 1.5 1.5v10.5a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 19.5V9a1.5 1.5 0 0 1 1.5-1.5Zm3 6h3v3h-3v-3Zm6 0h3v3h-3v-3Z" />
                        </svg>
                    </span>
                    <div class="text-right">
                        <p class="text-[0.6rem] tracking-[0.2em] text-slate-400">Hari ini</p>
                        <p class="text-sm font-semibold tracking-widest text-slate-700">{{ now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <dl class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
                <dt class="text-sm font-medium text-slate-500">Total Dosen</dt>
                <dd class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($totalProfil) }}</dd>
                <dd class="mt-1 text-xs text-slate-500">Jumlah dosen yang terdaftar di modul profil.</dd>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
                <dt class="text-sm font-medium text-slate-500">Data Kepangkatan Tercatat</dt>
                <dd class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($totalKepangkatan) }}</dd>
                <dd class="mt-1 text-xs text-slate-500">Total entri kepangkatan yang tersimpan.</dd>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
                <dt class="text-sm font-medium text-slate-500">Status Publikasi</dt>
                <dd class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($totalPublished) }}</dd>
                <dd class="mt-1 text-xs text-slate-500">Data kepangkatan yang sudah dipublikasikan.</dd>
            </div>
        </dl>

        <form method="GET" action="{{ route('kepangkatan.index') }}" class="mt-8 rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
            <div class="grid gap-4 md:grid-cols-3 md:items-end">
                <div class="md:col-span-3">
                    <label for="search" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">Cari Dosen</label>
                    <input
                        type="search"
                        id="search"
                        name="search"
                        value="{{ $filters['search'] }}"
                        placeholder="Masukkan nama atau kode dosen"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    >
                </div>
                <div>
                    <label for="publication" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">Status Publikasi</label>
                    <select
                        id="publication"
                        name="publication"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    >
                        <option value="">Semua Status Publikasi</option>
                        <option value="sudah" @selected($filters['publication'] === 'sudah')>Sudah dipublikasikan</option>
                        <option value="belum" @selected($filters['publication'] === 'belum')>Belum dipublikasikan</option>
                    </select>
                </div>
                <div>
                    <label for="tmt_status" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">Status TMT</label>
                    <select
                        id="tmt_status"
                        name="tmt_status"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    >
                        <option value="">Semua Status TMT</option>
                        @foreach ($tmtStatusOptions as $value => $label)
                            <option value="{{ $value }}" @selected($filters['tmt_status'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3 flex flex-wrap items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700"
                    >
                        Terapkan Filter
                    </button>
                    <a
                        href="{{ route('kepangkatan.index') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white"
                    >
                        Reset
                    </a>
                    <span class="text-xs text-slate-500">Total {{ $kepangkatans->total() }} data</span>
                </div>
            </div>
        </form>

        @if (session('success'))
            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-700" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-[64rem] divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-3">Dosen</th>
                            <th class="px-5 py-3">Jabatan Fungsional</th>
                            <th class="px-5 py-3 w-36">Tanggal TMT</th>
                            <th class="px-5 py-3 w-36">Status TMT</th>
                            <th class="px-5 py-3 w-32">Status Publikasi</th>
                            <th class="px-5 py-3 text-center w-20">Indikator</th>
                            <th class="px-5 py-3 text-center">Keterangan</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($kepangkatans as $kepangkatan)
                            @php
                                $profil = $kepangkatan->profil;
                            @endphp
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800">{{ $profil?->nama_dosen ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $profil?->kode_dosen ?? 'Tidak ada kode' }}</div>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $kepangkatan->jabatan_fungsional_label }}</td>
                                <td class="px-5 py-4 text-slate-600 w-36">
                                    {{ $kepangkatan->tanggal_tmt_display }}
                                </td>
                                <td class="px-5 py-4 text-slate-600 w-36">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                        {{ $kepangkatan->status_tmt_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-600 w-32">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $kepangkatan->is_published ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-amber-50 text-amber-700 ring-amber-200' }} ring-1">
                                        {{ $kepangkatan->status_publikasi_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center w-20">
                                    <div class="flex items-center justify-center">
                                        <span
                                            class="inline-flex h-4 w-4 items-center justify-center rounded-full {{ $kepangkatan->indicator_classes }}"
                                            title="Indikator {{ $kepangkatan->indicator_label }}"
                                            aria-label="Indikator {{ $kepangkatan->indicator_label }}"
                                        ></span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    <p class="text-s leading-relaxed">{{ $kepangkatan->keterangan }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('kepangkatan.edit', $kepangkatan) }}"
                                            class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-200"
                                        >
                                            Edit
                                        </a>
                                        <form action="{{ route('kepangkatan.destroy', $kepangkatan) }}" method="POST" onsubmit="return confirm('Hapus data kepangkatan untuk {{ $profil?->nama_dosen ?? '' }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-200">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-sm text-slate-500">Belum ada data kepangkatan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($kepangkatans->hasPages())
            <div class="mt-6">{{ $kepangkatans->links() }}</div>
        @endif
    </div>
@endsection
