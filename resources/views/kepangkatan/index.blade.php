@extends('layouts.app')

@section('title', 'Data Kepangkatan Dosen')

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
    <div class="rounded-3xl bg-white p-8 shadow-xl ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold text-slate-900">Manajemen Kepangkatan Dosen</h1>
                <p class="text-sm text-slate-500">Pantau proses kenaikan pangkat dan catatan tindak lanjut untuk setiap dosen KK RIIB.</p>
            </div>
            <a
                href="{{ route('kepangkatan.create') }}"
                class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-blue-600"
            >
                + Tambah Data Kepangkatan
            </a>
        </div>

        <form method="GET" action="{{ route('kepangkatan.index') }}" class="mt-8 rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
            <div class="grid gap-4 md:grid-cols-3 md:items-end">
                <div class="md:col-span-2">
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
                    <label for="status" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">Status</label>
                    <select
                        id="status"
                        name="status"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    >
                        <option value="">Semua Status</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
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
                            <th class="px-5 py-3">Pangkat &amp; Golongan</th>
                            <th class="px-5 py-3">Tanggal SK</th>
                            <th class="px-5 py-3">Mulai Tugas</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Catatan</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($kepangkatans as $kepangkatan)
                            @php
                                $profil = $kepangkatan->profil;
                                $metadata = $statusMetadata[$kepangkatan->status] ?? null;
                                $catatanSingkat = \Illuminate\Support\Str::limit($kepangkatan->catatan ?? '-', 70);
                            @endphp
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800">{{ $profil?->nama_dosen ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $profil?->kode_dosen ?? 'Tidak ada kode' }}</div>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $kepangkatan->jabatan_fungsional }}</td>
                                <td class="px-5 py-4 text-slate-600">
                                    <div>{{ $kepangkatan->pangkat ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $kepangkatan->golongan ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    {{ $kepangkatan->tanggal_sk?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    {{ $kepangkatan->tanggal_mulai?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $metadata['badge'] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                                            {{ $metadata['label'] ?? ucfirst($kepangkatan->status) }}
                                        </span>
                                        @if (! empty($metadata['description']))
                                            <p class="text-xs text-slate-500">{{ $metadata['description'] }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    <p class="text-xs text-slate-500">{{ $catatanSingkat }}</p>
                                    <p class="mt-2 text-[0.65rem] uppercase tracking-wide text-slate-400">Update {{ $kepangkatan->updated_at?->format('d/m/Y') ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('kepangkatan.edit', $kepangkatan) }}"
                                            class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-200"
                                        >
                                            Edit
                                        </a>
                                        <form action="{{ route('kepangkatan.destroy', $kepangkatan) }}" method="POST" onsubmit="return confirm('Hapus data kepangkatan untuk {{ $profil?->nama_dosen ?? 'dosen ini' }}?');">
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
