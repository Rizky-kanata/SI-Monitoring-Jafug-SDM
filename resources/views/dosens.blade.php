@extends('layouts.app')

@section('title', 'Data Dosen SDM')

@section('sidebar')
    <x-sidebar class="h-full" />
@endsection

@section('content')
    @php
        $templateColumns = [
            'kode_dosen' => 'Kode Dosen',
            'nama_dosen' => 'Nama Dosen',
            'prodi' => 'Program Studi',
            'kelompok_keahlian' => 'Kelompok Keahlian',
            'coe' => 'CoE',
            'nip' => 'NIP',
            'nidn' => 'NIDN',
        ];
    @endphp

    <div class="rounded-3xl bg-white p-8 shadow-xl ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Data Dosen Pendukung</p>
                <h1 class="text-2xl font-semibold text-slate-900">Meta Data Dosen</h1>
                <p class="text-sm text-slate-500">Kelola data dosen dari satu halaman yang rapi untuk kebutuhan monitoring kepangkatan.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a
                    href="{{ route('profils.create') }}"
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

        <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:max-w-md">
            <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5">
                <dt class="text-sm font-medium text-slate-500">Total Data Dosen</dt>
                <dd class="mt-2 text-3xl font-semibold text-slate-900">{{ number_format($total) }}</dd>
                <dd class="mt-1 text-xs text-slate-500">Jumlah data dosen yang tersimpan.</dd>
            </div>
        </dl>

        <div id="import" class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Import Excel</p>
                    <h2 class="text-lg font-semibold text-slate-900">Upload meta data dosen</h2>
                    <p class="text-sm text-slate-500">Pakai template `.xlsx` untuk input data dosen massal sesuai format yang sudah disediakan.</p>
                </div>
                <a
                    href="{{ route('profils.template') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                >
                    Download Template XLSX
                </a>
            </div>

            <form
                action="{{ route('profils.import') }}"
                method="POST"
                enctype="multipart/form-data"
                class="mt-6 rounded-3xl border border-slate-200 bg-slate-50/70 p-5 sm:p-6"
            >
                @csrf
                <label for="excel_file" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">File Excel</label>
                <div class="mt-4 flex flex-col gap-4 xl:flex-row xl:items-center">
                    <label
                        for="excel_file"
                        class="flex w-full cursor-pointer items-center gap-5 rounded-2xl border border-slate-200 bg-white px-5 py-4 text-slate-700 shadow-sm transition hover:border-blue-300"
                        data-import-dropzone
                    >
                        <span class="inline-flex min-w-[11rem] items-center justify-center rounded-full bg-slate-900 px-6 py-3 text-base font-semibold text-white">
                            Choose File
                        </span>
                        <span class="flex-1 truncate text-base text-slate-600" data-import-file-name>No file chosen</span>
                        <span class="hidden text-sm font-medium text-blue-600 xl:inline" data-import-drop-hint>atau drag &amp; drop file di sini</span>
                    </label>
                    <input
                        type="file"
                        id="excel_file"
                        name="excel_file"
                        accept=".xlsx"
                        required
                        class="sr-only"
                        data-import-file-input
                    >
                    <button
                        type="submit"
                        class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Upload Excel
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6 flex items-center gap-4">
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
        </div>

        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[64rem] divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-5 py-3">Kode Dosen</th>
                            <th class="px-5 py-3">Nama Dosen</th>
                            <th class="px-5 py-3">NIP</th>
                            <th class="px-5 py-3">NIDN</th>
                            <th class="px-5 py-3">Program Studi</th>
                            <th class="px-5 py-3">Kelompok Keahlian</th>
                            <th class="px-5 py-3"><span class="normal-case tracking-normal">CoE</span></th>
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
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->kelompok_keahlian }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->coe ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('profils.edit', ['profil' => $profil]) }}"
                                            data-preserve-scroll
                                            class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-200"
                                        >
                                            Edit
                                        </a>
                                        <form
                                            action="{{ route('profils.destroy', ['profil' => $profil]) }}"
                                            method="POST"
                                            data-delete-form
                                            data-delete-name="{{ $profil->nama_dosen }}"
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

    <div
        class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-950/50 px-4 backdrop-blur-sm"
        data-delete-modal
        aria-hidden="true"
    >
        <div class="w-[22rem] max-w-[calc(100vw-2rem)] rounded-[1.75rem] bg-white px-6 py-8 text-center shadow-2xl ring-1 ring-slate-200">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-100 text-rose-600">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
                    <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.99-2.77L4.088 6.66l-.209.035a.75.75 0 0 1-.256-1.478 48.567 48.567 0 0 1 3.878-.512v-.227A2.25 2.25 0 0 1 9.75 2.25h4.5A2.25 2.25 0 0 1 16.5 4.478Zm-6-.227a.75.75 0 0 0-.75.75V5.1c1.497-.067 3.003-.067 4.5 0V5a.75.75 0 0 0-.75-.75h-3Z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="mt-5">
                <p class="text-xl font-semibold text-slate-900">Hapus data dosen?</p>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Data dosen untuk <span class="font-semibold text-slate-800" data-delete-modal-name>-</span> akan dihapus dari sistem.
                </p>
                <p class="mt-2 text-sm text-slate-500">Tindakan ini tidak bisa dibatalkan.</p>
            </div>

            <div class="mt-8 flex items-center justify-center gap-4">
                <button
                    type="button"
                    class="inline-flex min-w-24 items-center justify-center gap-1 rounded-full bg-blue-100 px-4 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-200"
                    data-delete-cancel
                >
                    Batal
                </button>
                <button
                    type="button"
                    class="inline-flex min-w-24 items-center justify-center gap-1 rounded-full bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-200"
                    data-delete-confirm
                >
                    Ya, hapus
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const scrollStorageKey = 'profils-index-scroll-y';
            const input = document.querySelector('[data-import-file-input]');
            const fileName = document.querySelector('[data-import-file-name]');
            const deleteModal = document.querySelector('[data-delete-modal]');
            const deleteName = document.querySelector('[data-delete-modal-name]');
            const deleteCancel = document.querySelector('[data-delete-cancel]');
            const deleteConfirm = document.querySelector('[data-delete-confirm]');
            const deleteForms = document.querySelectorAll('[data-delete-form]');
            const preserveScrollLinks = document.querySelectorAll('[data-preserve-scroll]');
            let activeDeleteForm = null;

            const saveScrollPosition = () => {
                sessionStorage.setItem(scrollStorageKey, String(window.scrollY || window.pageYOffset || 0));
            };

            const restoreScrollPosition = () => {
                const savedPosition = sessionStorage.getItem(scrollStorageKey);

                if (savedPosition === null) {
                    return;
                }

                const targetPosition = Number(savedPosition) || 0;
                let attempts = 0;
                const maxAttempts = 12;

                const tryRestore = () => {
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'auto',
                    });

                    const currentPosition = window.scrollY || window.pageYOffset || 0;
                    const reachedTarget = Math.abs(currentPosition - targetPosition) <= 4;
                    const reachedPageBottom = window.innerHeight + currentPosition >= document.documentElement.scrollHeight - 4;

                    if (reachedTarget || reachedPageBottom || attempts >= maxAttempts) {
                        sessionStorage.removeItem(scrollStorageKey);
                        return;
                    }

                    attempts += 1;
                    window.setTimeout(tryRestore, 120);
                };

                window.requestAnimationFrame(() => {
                    window.setTimeout(tryRestore, 60);
                });
            };

            restoreScrollPosition();

            if (input && fileName) {
                input.addEventListener('change', () => {
                    const selected = input.files && input.files.length ? input.files[0].name : 'No file chosen';
                    fileName.textContent = selected;
                });
            }

            preserveScrollLinks.forEach((link) => {
                link.addEventListener('click', saveScrollPosition);
            });

            if (!deleteModal || !deleteName || !deleteCancel || !deleteConfirm || !deleteForms.length) return;

            const closeDeleteModal = () => {
                deleteModal.classList.add('hidden');
                deleteModal.classList.remove('flex');
                deleteModal.setAttribute('aria-hidden', 'true');
                activeDeleteForm = null;
            };

            const openDeleteModal = (form) => {
                activeDeleteForm = form;
                deleteName.textContent = form.dataset.deleteName || 'data ini';
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
                deleteModal.setAttribute('aria-hidden', 'false');
            };

            deleteForms.forEach((form) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    openDeleteModal(form);
                });
            });

            deleteCancel.addEventListener('click', closeDeleteModal);

            deleteConfirm.addEventListener('click', () => {
                if (activeDeleteForm) {
                    saveScrollPosition();
                    activeDeleteForm.submit();
                }
            });

            deleteModal.addEventListener('click', (event) => {
                if (event.target === deleteModal) {
                    closeDeleteModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
                    closeDeleteModal();
                }
            });
        })();
    </script>
@endpush
