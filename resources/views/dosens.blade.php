@extends('layouts.app')

@section('title', 'Dashboard Profil Dosen')

@section('sidebar')
    <x-sidebar class="h-full" />
@endsection

@section('content')
    <div class="rounded-3xl bg-white p-8 shadow-xl ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold text-slate-900">Profil Dosen RIIB</h2>
                <p class="text-sm text-slate-500">Daftar pantau dan pengelolaan informasi profil dosen RIIB.</p>
            </div>
            <a
                href="{{ route('profils.create', ['sort' => $sort, 'direction' => $direction]) }}"
                class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
            >
                + Tambah Profil
            </a>
        </div>

        @if (session('success'))
            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-700" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50/80 px-4 py-3 text-sm text-rose-700" role="alert">
                <strong class="font-semibold">Terjadi kesalahan:</strong>
                <ul class="mt-3 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
                <dt class="text-sm font-medium text-slate-500">Total Profil Dosen</dt>
                <dd class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($total) }}</dd>
                <dd class="mt-1 text-xs text-slate-500">Jumlah profil yang tersimpan saat ini.</dd>
            </div>
        </dl>

        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-[72rem] divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-3">Foto Dosen</th>
                            <th class="px-5 py-3">Kode Dosen</th>
                            <th class="px-5 py-3" aria-sort="{{ $sort === 'nama_dosen' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                @php
                                    $isNamaSorted = $sort === 'nama_dosen';
                                    $namaDirection = $isNamaSorted && $direction === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <a href="{{ route('profils.index', ['sort' => 'nama_dosen', 'direction' => $namaDirection]) }}"
                                   class="flex items-center gap-1 text-xs font-semibold uppercase tracking-wide transition hover:text-blue-600 {{ $isNamaSorted ? 'text-blue-600' : 'text-slate-500' }}">
                                    Nama Dosen
                                    <span class="text-[0.65rem]">{{ $isNamaSorted ? ($direction === 'asc' ? '^' : 'v') : '<>' }}</span>
                                </a>
                            </th>
                            <th class="px-5 py-3">NIP</th>
                            <th class="px-5 py-3">NIDN</th>
                            <th class="px-5 py-3" aria-sort="{{ $sort === 'prodi' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                @php
                                    $isProdiSorted = $sort === 'prodi';
                                    $prodiDirection = $isProdiSorted && $direction === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <a href="{{ route('profils.index', ['sort' => 'prodi', 'direction' => $prodiDirection]) }}"
                                   class="flex items-center gap-1 text-xs font-semibold uppercase tracking-wide transition hover:text-blue-600 {{ $isProdiSorted ? 'text-blue-600' : 'text-slate-500' }}">
                                    Program Studi
                                    <span class="text-[0.65rem]">{{ $isProdiSorted ? ($direction === 'asc' ? '^' : 'v') : '<>' }}</span>
                                </a>
                            </th>
                            <th class="px-5 py-3" aria-sort="{{ $sort === 'sub_kelompok_keahlian' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                @php
                                    $isSubSorted = $sort === 'sub_kelompok_keahlian';
                                    $subDirection = $isSubSorted && $direction === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <a href="{{ route('profils.index', ['sort' => 'sub_kelompok_keahlian', 'direction' => $subDirection]) }}"
                                   class="flex items-center gap-1 text-xs font-semibold uppercase tracking-wide transition hover:text-blue-600 {{ $isSubSorted ? 'text-blue-600' : 'text-slate-500' }}">
                                    Sub Kelompok Keahlian
                                    <span class="text-[0.65rem]">{{ $isSubSorted ? ($direction === 'asc' ? '^' : 'v') : '<>' }}</span>
                                </a>
                            </th>
                            <th class="px-5 py-3">Lab</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($profils as $profil)
                            @php
                                $initials = '';
                                $words = preg_split('/\s+/', trim($profil->nama_dosen ?? ''), -1, PREG_SPLIT_NO_EMPTY);
                                foreach ($words as $word) {
                                    $initials .= mb_strtoupper(mb_substr($word, 0, 1));
                                    if (mb_strlen($initials) >= 2) {
                                        $initials = mb_substr($initials, 0, 2);
                                        break;
                                    }
                                }
                                if ($initials === '') {
                                    $initials = '-';
                                }
                            @endphp
                                                        <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    <div class="flex items-center">
                                        <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-full bg-slate-100 ring-1 ring-slate-200">
                                            @if ($profil->foto_url)
                                                <img src="{{ $profil->foto_url }}" alt="Foto {{ $profil->nama_dosen }}" class="h-full w-full object-cover object-center">
                                            @else
                                                <span class="text-sm font-semibold text-slate-500">{{ $initials }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->kode_dosen }}</td>
                                <td class="px-5 py-4">
                                    <div class="space-y-1">
                                        <p class="text-sm font-semibold text-slate-800">{{ $profil->nama_dosen }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->nip ?? '-' }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->nidn ?? '-' }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->prodi }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->sub_kelompok_keahlian }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->lab ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('profils.edit', ['profil' => $profil, 'sort' => $sort, 'direction' => $direction]) }}"
                                            class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-200"
                                        >
                                            Edit
                                        </a>
                                        <form
                                            action="{{ route('profils.destroy', $profil) }}"
                                            method="POST"
                                            onsubmit="return confirm('Hapus profil {{ e($profil->nama_dosen) }}?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="sort" value="{{ $sort }}">
                                            <input type="hidden" name="direction" value="{{ $direction }}">
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-200">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-10 text-center text-sm text-slate-500">
                                    Belum ada data profil dosen.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
