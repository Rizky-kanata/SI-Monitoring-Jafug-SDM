@extends('layouts.app')

@section('title', 'Data Kepangkatan Dosen')

@section('sidebar')
    <x-sidebar class="h-full" />
@endsection

@section('content')
    <div class="rounded-3xl bg-white p-8 shadow-xl ring-1 ring-slate-200">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1">
                <h1 class="text-2xl font-semibold text-slate-900">Monitoring Kepangkatan Dosen</h1>
            </div>
            <div class="flex flex-col items-center gap-3 md:flex-row">
                <div class="inline-flex items-center gap-3 rounded-3xl bg-slate-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500 ring-1 ring-slate-200 shadow-sm">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-2xl bg-slate-900 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 9h18M4.5 7.5h15a1.5 1.5 0 0 1 1.5 1.5v10.5a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 19.5V9a1.5 1.5 0 0 1 1.5-1.5Zm3 6h3v3h-3v-3Zm6 0h3v3h-3v-3Z" />
                        </svg>
                    </span>
                    <div class="text-right">
                        <p class="text-[0.6rem] tracking-[0.2em] text-slate-400">Hari ini</p>
                        <p id="hari-ini-label" class="text-sm font-semibold tracking-widest text-slate-700">{{ now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('kepangkatan.index') }}" class="mt-8 rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
            <div class="grid gap-4 md:grid-cols-5 md:items-end">
                <div class="md:col-span-1">
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
                <div class="md:col-span-1">
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
                <div class="md:col-span-1">
                    <label for="indicator" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">Indikator</label>
                    <select
                        id="indicator"
                        name="indicator"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    >
                        <option value="">Semua Indikator</option>
                        @foreach ($indicatorOptions as $value => $label)
                            <option value="{{ $value }}" @selected($filters['indicator'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label for="per_page" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">Baris Tabel</label>
                    <select
                        id="per_page"
                        name="per_page"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                        onchange="this.form.submit()"
                    >
                        @foreach ($perPageOptions as $value => $label)
                            <option value="{{ $value }}" @selected((string) $filters['per_page'] === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-1">
                    <span class="block text-xs font-semibold uppercase tracking-wide text-slate-600" aria-hidden="true">&nbsp;</span>
                    <a
                        href="{{ route('kepangkatan.create') }}"
                        class="mt-2 inline-flex w-full items-center justify-center whitespace-nowrap rounded-2xl bg-blue-600 px-4 py-2.5 text-xs font-semibold uppercase tracking-wide text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                    >
                        + Tambah Data Kepangkatan
                    </a>
                </div>
                <div class="md:col-span-5 flex flex-wrap items-end gap-3">
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
                    <div class="ml-auto flex flex-wrap items-center gap-3">
                        <a
                            href="{{ route('kepangkatan.export', collect($filters)->only(['publication', 'tmt_status', 'indicator', 'search'])->filter(fn ($value) => $value !== '')->all()) }}"
                            class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-600 transition hover:bg-slate-100"
                        >
                            Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </form>

        @if (session('success'))
            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-700" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($metaDataCount > 0)
            <div class="mt-6 rounded-2xl border border-blue-200 bg-blue-50/80 px-4 py-3 text-sm text-blue-700" role="status">
                Meta Data Dosen siap: {{ number_format($metaDataCount) }} dosen terdaftar. Import kepangkatan pakai <span class="font-semibold">nama_dosen</span>; upload ulang untuk dosen yang sama akan <span class="font-semibold">memperbarui data lama</span>, bukan membuat duplikat.
            </div>
        @else
            <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-700" role="alert">
                Meta Data Dosen masih kosong. Isi data dosen dulu di halaman <span class="font-semibold">Meta Data Dosen</span> sebelum upload Excel kepangkatan.
            </div>
        @endif

        @if ($errors->has('excel_file'))
            <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50/80 px-4 py-3 text-sm text-rose-700" role="alert">
                {{ $errors->first('excel_file') }}
            </div>
        @endif

        <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Import Excel</p>
                    <h2 class="text-lg font-semibold text-slate-900">Upload data kepangkatan</h2>
                    <p class="text-sm text-slate-500">Pakai template `.xlsx` dengan kolom `nama_dosen` sebagai acuan utama. Nama dosen harus sudah tersedia di Meta Data Dosen.</p>
                </div>
                <a
                    href="{{ route('kepangkatan.template') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                >
                    Download Template XLSX
                </a>
            </div>

            <form
                action="{{ route('kepangkatan.import') }}"
                method="POST"
                enctype="multipart/form-data"
                class="mt-4 flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 lg:flex-row lg:items-center lg:justify-between"
            >
                @csrf
                <div class="flex-1">
                    <label for="kepangkatan_excel_file" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">File Excel</label>
                    <input
                        type="file"
                        id="kepangkatan_excel_file"
                        name="excel_file"
                        accept=".xlsx"
                        required
                        @disabled($metaDataCount === 0)
                        class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm file:mr-4 file:rounded-full file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    >
                    <p class="mt-2 text-xs text-slate-500">Format yang didukung hanya `.xlsx`. Isi kolom pertama dengan <span class="font-semibold">nama_dosen</span> yang sudah ada di Meta Data Dosen.</p>
                </div>
                <button
                    type="submit"
                    @disabled($metaDataCount === 0)
                    class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    Upload Excel
                </button>
            </form>
        </div>

        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[58rem] divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/80">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <th class="px-4 py-3 w-40">Dosen</th>
                            <th class="px-4 py-3 w-32">Jabatan Fungsional</th>
                            <th class="px-4 py-3 w-28">Pangkat</th>
                            <th class="px-4 py-3 w-24">Golongan</th>
                            <th class="px-4 py-3 w-28">Tanggal SK</th>
                            <th class="px-4 py-3 w-32">Status TMT</th>
                            <th class="px-4 py-3 w-28">Status Publikasi</th>
                            <th class="px-4 py-3 text-center w-16">Indikator</th>
                            <th class="px-4 py-3 w-56">Keterangan</th>
                            <th class="px-4 py-3 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($kepangkatans as $kepangkatan)
                            @php
                                $profil = $kepangkatan->profil;
                            @endphp
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-4 py-4 align-top">
                                    <div class="font-semibold text-slate-800">{{ $profil?->nama_dosen ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $profil?->kode_dosen ?? 'Tidak ada kode' }}</div>
                                </td>
                                <td class="px-4 py-4 align-top text-slate-600">{{ $kepangkatan->jabatan_fungsional_label }}</td>
                                <td class="px-4 py-4 align-top text-slate-600">{{ $kepangkatan->pangkat ?: '-' }}</td>
                                <td class="px-4 py-4 align-top text-slate-600">{{ $kepangkatan->golongan ?: '-' }}</td>
                                <td class="px-4 py-4 align-top text-slate-600">
                                    {{ $kepangkatan->tanggal_sk ? $kepangkatan->tanggal_sk->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-4 align-top text-slate-600">
                                    <span class="inline-flex min-w-[7.25rem] items-center justify-center rounded-2xl bg-slate-100 px-2.5 py-1.5 text-center text-xs font-semibold leading-5 text-slate-700 ring-1 ring-slate-200">
                                        {{ $kepangkatan->status_tmt_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 align-top text-slate-600">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $kepangkatan->is_published ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-amber-50 text-amber-700 ring-amber-200' }} ring-1">
                                        {{ $kepangkatan->status_publikasi_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 align-top text-center">
                                    <div class="flex items-center justify-center">
                                        <span
                                            class="inline-flex h-4 w-4 items-center justify-center rounded-full {{ $kepangkatan->indicator_classes }}"
                                            title="Indikator {{ $kepangkatan->indicator_label }}"
                                            aria-label="Indikator {{ $kepangkatan->indicator_label }}"
                                        ></span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 align-top text-slate-600">
                                    <p class="text-sm leading-relaxed">{{ $kepangkatan->keterangan }}</p>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('kepangkatan.edit', $kepangkatan) }}"
                                            data-preserve-scroll
                                            class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-200"
                                        >
                                            Edit
                                        </a>
                                        <form
                                            action="{{ route('kepangkatan.destroy', $kepangkatan) }}"
                                            method="POST"
                                            data-delete-form
                                            data-delete-action="{{ route('kepangkatan.destroy', $kepangkatan) }}"
                                            data-delete-name="{{ $profil?->nama_dosen ?? 'dosen ini' }}"
                                        >
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
                                <td colspan="10" class="px-5 py-10 text-center text-sm text-slate-500">Belum ada data kepangkatan.</td>
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
                <p class="text-xl font-semibold text-slate-900">Hapus data kepangkatan?</p>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Data kepangkatan untuk <span class="font-semibold text-slate-800" data-delete-modal-name>-</span> akan dihapus dari sistem.
                </p>
                <p class="mt-2 text-sm text-slate-500">Tindakan ini tidak bisa dibatalkan.</p>
            </div>

            <div class="mt-8 flex items-center justify-center gap-4">
                <form method="POST" class="hidden" data-delete-modal-form>
                    @csrf
                    @method('DELETE')
                </form>
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
            const scrollStorageKey = 'kepangkatan-index-scroll-y';
            const dateLabel = document.getElementById('hari-ini-label');
            const deleteModal = document.querySelector('[data-delete-modal]');
            const deleteName = document.querySelector('[data-delete-modal-name]');
            const deleteCancel = document.querySelector('[data-delete-cancel]');
            const deleteConfirm = document.querySelector('[data-delete-confirm]');
            const deleteForms = document.querySelectorAll('[data-delete-form]');
            const preserveScrollLinks = document.querySelectorAll('[data-preserve-scroll]');
            const deleteModalForm = document.querySelector('[data-delete-modal-form]');
            let activeDeleteAction = null;

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

            if (dateLabel) {
                const formatter = new Intl.DateTimeFormat('id-ID', {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                });

                const renderDate = () => {
                    const now = new Date();
                    dateLabel.textContent = formatter.format(now).toUpperCase();
                };

                renderDate();
                setInterval(renderDate, 60000);
            }

            preserveScrollLinks.forEach((link) => {
                link.addEventListener('click', saveScrollPosition);
            });

            if (!deleteModal || !deleteName || !deleteCancel || !deleteConfirm || !deleteModalForm || !deleteForms.length) return;

            const closeDeleteModal = () => {
                deleteModal.classList.add('hidden');
                deleteModal.classList.remove('flex');
                deleteModal.setAttribute('aria-hidden', 'true');
                activeDeleteAction = null;
            };

            const openDeleteModal = (form) => {
                activeDeleteAction = form.dataset.deleteAction || form.getAttribute('action');
                deleteName.textContent = form.dataset.deleteName || 'dosen ini';
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
                if (activeDeleteAction) {
                    saveScrollPosition();
                    deleteModalForm.setAttribute('action', activeDeleteAction);
                    deleteModalForm.submit();
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
