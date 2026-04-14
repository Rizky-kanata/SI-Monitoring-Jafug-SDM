@extends('layouts.app')

@section('title', 'Data Dosen SDM')

@section('sidebar')
    <x-sidebar class="h-full" />
@endsection

@section('content')
    <div class="rounded-3xl bg-white p-8 shadow-xl ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Data Dosen Pendukung</p>
                <h1 class="text-2xl font-semibold text-slate-900">Data Dosen untuk Monitoring Kepangkatan</h1>
                <p class="text-sm text-slate-500">Kelola data dosen dari satu halaman yang rapi. Input manual tetap ada, dan import massal cukup dijalankan dari sini.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a
                    href="{{ route('profils.create', ['sort' => $sort, 'direction' => $direction]) }}"
                    class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                >
                    Input Manual Dosen
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-700" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->has('excel_file'))
            <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50/80 px-4 py-3 text-sm text-rose-700" role="alert">
                {{ $errors->first('excel_file') }}
            </div>
        @endif

        <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
                <dt class="text-sm font-medium text-slate-500">Total Data Dosen</dt>
                <dd class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($total) }}</dd>
                <dd class="mt-1 text-xs text-slate-500">Jumlah data dosen yang tersimpan.</dd>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5">
                <dt class="text-sm font-medium text-emerald-700">Import Massal</dt>
                <dd class="mt-2 text-xl font-semibold text-slate-900">Excel / CSV</dd>
                <dd class="mt-1 text-xs text-emerald-700/80">Dipakai untuk input awal atau update data dosen secara cepat.</dd>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-5">
                <dt class="text-sm font-medium text-amber-700">Template</dt>
                <dd class="mt-2 text-xl font-semibold text-slate-900">Siap Diunduh</dd>
                <dd class="mt-1 text-xs text-amber-700/80">Header kolom sudah sesuai field sistem supaya import aman.</dd>
            </div>
        </dl>

        <div id="import" class="mt-8 rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Unggah File</p>
                    <p class="mt-1 text-xs text-slate-500">Gunakan template Excel/CSV untuk menambah data dosen secara massal.</p>
                </div>
                <a
                    href="{{ route('profils.template') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                >
                    Download Template
                </a>
            </div>

            <form
                action="{{ route('profils.import', ['sort' => $sort, 'direction' => $direction]) }}"
                method="POST"
                enctype="multipart/form-data"
                class="mt-6 space-y-4"
            >
                @csrf
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50/80 p-4">
                    <label
                        for="excel_file"
                        class="block cursor-pointer rounded-[1.25rem] border-2 border-dashed border-slate-300 bg-white px-5 py-8 text-center transition hover:border-blue-400 hover:bg-blue-50/30"
                    >
                        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-6 w-6">
                                <path d="M3.25 4A2.25 2.25 0 0 1 5.5 1.75h4.19c.597 0 1.169.237 1.59.66l3.81 3.81c.422.421.66.993.66 1.59v6.69a2.25 2.25 0 0 1-2.25 2.25h-8A2.25 2.25 0 0 1 3.25 14.5V4Z" />
                            </svg>
                        </span>
                        <span class="mt-4 block text-base font-semibold text-slate-900">Pilih file Excel atau CSV</span>
                        <span class="mt-2 block text-sm text-slate-500">Format yang didukung: .xlsx dan .csv</span>
                        <span class="mt-4 inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white">
                            Pilih File
                        </span>
                    </label>
                    <input
                        type="file"
                        id="excel_file"
                        name="excel_file"
                        accept=".xlsx,.csv"
                        required
                        class="sr-only"
                        data-import-file-input
                    >
                    <div class="mt-4 flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">File terpilih</p>
                            <p class="mt-1 truncate text-sm font-medium text-slate-800" data-import-file-name>Belum ada file dipilih</p>
                        </div>
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">.xlsx / .csv</span>
                    </div>
                </div>
                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    Upload Data Dosen
                </button>
            </form>
        </div>

        <div class="mt-6 flex items-center justify-between gap-4">
            <form method="GET" action="{{ route('profils.index') }}" class="flex flex-wrap gap-2">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama dosen..."
                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >
                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
                >
                    Cari
                </button>
            </form>
            <a href="{{ route('kepangkatan.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                Kembali ke data kepangkatan
            </a>
        </div>

        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-[64rem] divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-3">Kode Dosen</th>
                            <th class="px-5 py-3">Nama Dosen</th>
                            <th class="px-5 py-3">NIP</th>
                            <th class="px-5 py-3">NIDN</th>
                            <th class="px-5 py-3">Program Studi</th>
                            <th class="px-5 py-3">Sub Kelompok Keahlian</th>
                            <th class="px-5 py-3">Lab</th>
                            <th class="px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($profils as $profil)
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->kode_dosen }}</td>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-800">{{ $profil->nama_dosen }}</p>
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
                                            action="{{ route('profils.destroy', ['profil' => $profil, 'sort' => $sort, 'direction' => $direction]) }}"
                                            method="POST"
                                            onsubmit="return confirm('Hapus data dosen {{ e($profil->nama_dosen) }}?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-200">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-sm text-slate-500">
                                    Belum ada data dosen.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const input = document.querySelector('[data-import-file-input]');
            const fileName = document.querySelector('[data-import-file-name]');

            if (!input || !fileName) return;

            input.addEventListener('change', () => {
                const selected = input.files && input.files.length ? input.files[0].name : 'Belum ada file dipilih';
                fileName.textContent = selected;
            });
        })();
    </script>
@endpush
