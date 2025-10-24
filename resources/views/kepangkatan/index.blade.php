@extends('layouts.app')

@section('title', 'Dashboard Kepangkatan Dosen KK RIIB')

@section('sidebar')
    <x-sidebar :links="[
        ['label' => 'Data Profil Dosen', 'href' => route('profils.index'), 'active' => request()->routeIs('profils.*')],
        ['label' => 'Data Kepangkatan', 'href' => route('kepangkatan.index'), 'active' => request()->routeIs('kepangkatan.*')],
        ['label' => 'Data Linieritas', 'href' => '#'],
        ['label' => 'Data Matrix', 'href' => '#'],
        ['label' => 'Data Pengajaran & Muatan Riset', 'href' => '#'],
        ['label' => 'Data Materi Kegiatan & Dokumen SK', 'href' => '#'],
    ]" class="h-full" />
@endsection

@section('content')
    @php
        $baseQuery = request()->except('page', 'sort', 'direction');
        $buildSortLink = function (string $field) use ($baseQuery, $sort, $direction) {
            $isSorted = $sort === $field;
            $nextDirection = $isSorted && $direction === 'asc' ? 'desc' : 'asc';

            return [
                'href' => route('kepangkatan.index', array_merge($baseQuery, [
                    'sort' => $field,
                    'direction' => $nextDirection,
                ])),
                'isSorted' => $isSorted,
                'symbol' => $isSorted ? ($direction === 'asc' ? '▲' : '▼') : '⇅',
                'aria' => $isSorted ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none',
            ];
        };
    @endphp

    <div class="rounded-3xl bg-white p-8 shadow-xl ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold text-slate-900">Kepangkatan Dosen Kelompok Keahlian RIIB</h1>
                <p class="text-sm text-slate-500">Monitor proses publikasi kepangkatan dan masa berlaku TMT dosen KK RIIB.</p>
            </div>
            <a
                href="{{ route('kepangkatan.create') }}"
                class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
            >
                + Tambah Data Kepangkatan
            </a>
        </div>

        @if (session('success'))
            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-700" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-[60rem] divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            @php($kodeSort = $buildSortLink('kode_dosen'))
                            <th class="px-5 py-3" aria-sort="{{ $kodeSort['aria'] }}">
                                <a href="{{ $kodeSort['href'] }}" class="flex items-center gap-1 text-xs font-semibold uppercase tracking-wide transition hover:text-blue-600 {{ $kodeSort['isSorted'] ? 'text-blue-600' : 'text-slate-500' }}">
                                    Kode Dosen
                                    <span class="text-[0.65rem]">{{ $kodeSort['symbol'] }}</span>
                                </a>
                            </th>
                            @php($namaSort = $buildSortLink('nama_dosen'))
                            <th class="px-5 py-3" aria-sort="{{ $namaSort['aria'] }}">
                                <a href="{{ $namaSort['href'] }}" class="flex items-center gap-1 text-xs font-semibold uppercase tracking-wide transition hover:text-blue-600 {{ $namaSort['isSorted'] ? 'text-blue-600' : 'text-slate-500' }}">
                                    Nama Dosen
                                    <span class="text-[0.65rem]">{{ $namaSort['symbol'] }}</span>
                                </a>
                            </th>
                            @php($jabatanSort = $buildSortLink('jabatan_fungsional'))
                            <th class="px-5 py-3" aria-sort="{{ $jabatanSort['aria'] }}">
                                <a href="{{ $jabatanSort['href'] }}" class="flex items-center gap-1 text-xs font-semibold uppercase tracking-wide transition hover:text-blue-600 {{ $jabatanSort['isSorted'] ? 'text-blue-600' : 'text-slate-500' }}">
                                    Jabatan Fungsional
                                    <span class="text-[0.65rem]">{{ $jabatanSort['symbol'] }}</span>
                                </a>
                            </th>
                            @php($tmtSort = $buildSortLink('tanggal_tmt'))
                            <th class="px-5 py-3" aria-sort="{{ $tmtSort['aria'] }}">
                                <a href="{{ $tmtSort['href'] }}" class="flex items-center gap-1 text-xs font-semibold uppercase tracking-wide transition hover:text-blue-600 {{ $tmtSort['isSorted'] ? 'text-blue-600' : 'text-slate-500' }}">
                                    Tanggal TMT
                                    <span class="text-[0.65rem]">{{ $tmtSort['symbol'] }}</span>
                                </a>
                            </th>
                            @php($statusSort = $buildSortLink('status_publikasi'))
                            <th class="px-5 py-3" aria-sort="{{ $statusSort['aria'] }}">
                                <a href="{{ $statusSort['href'] }}" class="flex items-center gap-1 text-xs font-semibold uppercase tracking-wide transition hover:text-blue-600 {{ $statusSort['isSorted'] ? 'text-blue-600' : 'text-slate-500' }}">
                                    Status Publikasi
                                    <span class="text-[0.65rem]">{{ $statusSort['symbol'] }}</span>
                                </a>
                            </th>
                            <th class="px-5 py-3">Indikator Monitoring</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @if ($kepangkatans->isEmpty())
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">Belum ada data kepangkatan.</td>
                            </tr>
                        @else
                            @foreach ($kepangkatans as $kepangkatan)
                                @php
                                    $indicator = $statusIndicators[$kepangkatan->id] ?? null;
                                    $statusLabel = $statusMetadata[$kepangkatan->status_publikasi]['label'] ?? 'Status Tidak Dikenal';
                                @endphp
                                <tr class="transition hover:bg-slate-50/80">
                                    <td class="px-5 py-4 font-medium text-slate-700">{{ $kepangkatan->kode_dosen ?? '—' }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $kepangkatan->nama_dosen }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $kepangkatan->jabatan_fungsional }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $kepangkatan->tanggal_tmt?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $statusLabel }}</td>
                                    <td class="px-5 py-4">
                                        @if ($indicator)
                                            <div class="space-y-1">
                                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $indicator['badge'] }}">
                                                    {{ $indicator['label'] }}
                                                </span>
                                                <p class="text-xs text-slate-500">{{ $indicator['description'] }}</p>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a
                                                href="{{ route('kepangkatan.edit', $kepangkatan) }}"
                                                class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-200"
                                            >
                                                Edit
                                            </a>
                                            <form action="{{ route('kepangkatan.destroy', $kepangkatan) }}" method="POST" onsubmit="return confirm('Hapus data kepangkatan untuk {{ $kepangkatan->nama_dosen }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-200">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if ($kepangkatans->hasPages())
            <div class="mt-6">{{ $kepangkatans->links() }}</div>
        @endif
    </div>
@endsection
