@extends('layouts.app')

@section('title', 'Dashboard Profil Dosen')

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
                <h2 class="text-2xl font-semibold text-slate-900">Profil Dosen Kelompok Keahlian RIIB</h2>
                <p class="text-sm text-slate-500">Daftar pantau dan pengelolaan informasi profil dosen kelompok keahlian RIIB.</p>
            </div>
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                data-modal-target="create-modal"
            >
                + Tambah Profil
            </button>
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
                            <th class="px-5 py-3">No.</th>
                            <th class="px-5 py-3">Foto</th>
                            <th class="px-5 py-3" aria-sort="{{ $sort === 'nama_dosen' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                @php
                                    $isNamaSorted = $sort === 'nama_dosen';
                                    $namaDirection = $isNamaSorted && $direction === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <a href="{{ route('profils.index', ['sort' => 'nama_dosen', 'direction' => $namaDirection]) }}"
                                   class="flex items-center gap-1 text-xs font-semibold uppercase tracking-wide transition hover:text-blue-600 {{ $isNamaSorted ? 'text-blue-600' : 'text-slate-500' }}">
                                    Nama Dosen
                                    <span class="text-[0.65rem]">{{ $isNamaSorted ? ($direction === 'asc' ? '▲' : '▼') : '⇅' }}</span>
                                </a>
                            </th>
                            <th class="px-5 py-3">Kode Dosen</th>
                            <th class="px-5 py-3" aria-sort="{{ $sort === 'prodi' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                @php
                                    $isProdiSorted = $sort === 'prodi';
                                    $prodiDirection = $isProdiSorted && $direction === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <a href="{{ route('profils.index', ['sort' => 'prodi', 'direction' => $prodiDirection]) }}"
                                   class="flex items-center gap-1 text-xs font-semibold uppercase tracking-wide transition hover:text-blue-600 {{ $isProdiSorted ? 'text-blue-600' : 'text-slate-500' }}">
                                    Program Studi
                                    <span class="text-[0.65rem]">{{ $isProdiSorted ? ($direction === 'asc' ? '▲' : '▼') : '⇅' }}</span>
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
                                    <span class="text-[0.65rem]">{{ $isSubSorted ? ($direction === 'asc' ? '▲' : '▼') : '⇅' }}</span>
                                </a>
                            </th>
                            <th class="px-5 py-3">NIP</th>
                            <th class="px-5 py-3">NIDN</th>
                            <th class="px-5 py-3" aria-sort="{{ $sort === 'created_at' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                                @php
                                    $isCreatedSorted = $sort === 'created_at';
                                    $createdDirection = $isCreatedSorted && $direction === 'asc' ? 'desc' : 'asc';
                                @endphp
                                <a href="{{ route('profils.index', ['sort' => 'created_at', 'direction' => $createdDirection]) }}"
                                   class="flex items-center gap-1 text-xs font-semibold uppercase tracking-wide transition hover:text-blue-600 {{ $isCreatedSorted ? 'text-blue-600' : 'text-slate-500' }}">
                                    Tanggal Input
                                    <span class="text-[0.65rem]">{{ $isCreatedSorted ? ($direction === 'asc' ? '▲' : '▼') : '⇅' }}</span>
                                </a>
                            </th>
                            <th class="px-5 py-3 text-right">Aksi</th>
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
                                    $initials = '—';
                                }
                            @endphp
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4 text-sm font-semibold text-slate-500">{{ $loop->iteration }}</td>
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
                                <td class="px-5 py-4">
                                    <div class="space-y-1">
                                        <p class="text-sm font-semibold text-slate-800">{{ $profil->nama_dosen }}</p>
                                        <p class="text-xs text-slate-500">{{ $profil->kelompok_keahlian }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->kode_dosen }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->prodi }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->sub_kelompok_keahlian }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->nip ?? '—' }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->nidn ?? '—' }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $profil->created_at?->format('d M Y') ?? '—' }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-200"
                                            data-action="edit"
                                            data-update-template="{{ route('profils.update', ['profil' => '__ID__']) }}"
                                            data-profile="{{ $profil->toJson(JSON_UNESCAPED_UNICODE) }}"
                                            data-modal-target="edit-modal"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-3 py-1.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-200"
                                            data-action="delete"
                                            data-destroy-template="{{ route('profils.destroy', ['profil' => '__ID__']) }}"
                                            data-profile="{{ json_encode(['id' => $profil->id, 'nama_dosen' => $profil->nama_dosen], JSON_UNESCAPED_UNICODE) }}"
                                            data-modal-target="delete-modal"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-5 py-10 text-center text-sm text-slate-500">
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

@push('modals')
    <div class="modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-6" id="create-modal">
        <div class="relative w-full max-w-2xl rounded-2xl bg-white p-8 shadow-2xl ring-1 ring-slate-200">
            <button type="button" class="modal-close absolute right-4 top-4 text-lg text-slate-400 transition hover:text-slate-600" data-modal-close>&times;</button>
            <h3 class="text-xl font-semibold text-slate-900">Tambah Profil Dosen</h3>
            <form action="{{ route('profils.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 grid gap-4">
                @csrf
                <input type="hidden" name="sort" value="{{ old('sort', $sort) }}">
                <input type="hidden" name="direction" value="{{ old('direction', $direction) }}">
                <div class="grid gap-2">
                    <label for="create-kode_dosen" class="text-sm font-medium text-slate-600">Kode Dosen</label>
                    <input type="text" id="create-kode_dosen" name="kode_dosen" value="{{ old('kode_dosen') }}" required class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                </div>
                <div class="grid gap-2">
                    <label for="create-nama_dosen" class="text-sm font-medium text-slate-600">Nama Dosen</label>
                    <input type="text" id="create-nama_dosen" name="nama_dosen" value="{{ old('nama_dosen') }}" required class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                </div>
                <div class="grid gap-2">
                    <label for="create-prodi" class="text-sm font-medium text-slate-600">Program Studi</label>
                    <input type="text" id="create-prodi" name="prodi" value="{{ old('prodi') }}" required class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                </div>
                <div class="grid gap-2">
                    <label for="create-sub_kelompok_keahlian" class="text-sm font-medium text-slate-600">Sub Kelompok Keahlian</label>
                    <input type="text" id="create-sub_kelompok_keahlian" name="sub_kelompok_keahlian" value="{{ old('sub_kelompok_keahlian') }}" required class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                </div>
                <div class="grid gap-2">
                    <label for="create-foto" class="text-sm font-medium text-slate-600">Foto Profil</label>
                    <input type="file" id="create-foto" name="foto" accept="image/*" class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                    <p class="text-xs text-slate-500">Format JPG, PNG, atau WEBP dengan ukuran maksimum 2 MB.</p>
                </div>
                <div class="grid gap-2">
                    <label for="create-nip" class="text-sm font-medium text-slate-600">NIP</label>
                    <input type="text" id="create-nip" name="nip" value="{{ old('nip') }}" class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                </div>
                <div class="grid gap-2">
                    <label for="create-nidn" class="text-sm font-medium text-slate-600">NIDN</label>
                    <input type="text" id="create-nidn" name="nidn" value="{{ old('nidn') }}" class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                </div>
                <div class="flex items-center justify-end gap-3 pt-4">
                    <button type="button" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100" data-modal-close>Batal</button>
                    <button type="submit" class="rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-6" id="edit-modal">
        <div class="relative w-full max-w-2xl rounded-2xl bg-white p-8 shadow-2xl ring-1 ring-slate-200">
            <button type="button" class="modal-close absolute right-4 top-4 text-lg text-slate-400 transition hover:text-slate-600" data-modal-close>&times;</button>
            <h3 class="text-xl font-semibold text-slate-900">Edit Profil Dosen</h3>
            <form id="edit-form" method="POST" enctype="multipart/form-data" class="mt-6 grid gap-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-profil_id" name="profil_id" value="{{ old('profil_id') }}">
                <input type="hidden" name="sort" value="{{ old('sort', $sort) }}">
                <input type="hidden" name="direction" value="{{ old('direction', $direction) }}">
                <div class="grid gap-2">
                    <label for="edit-kode_dosen" class="text-sm font-medium text-slate-600">Kode Dosen</label>
                    <input type="text" id="edit-kode_dosen" name="kode_dosen" value="{{ old('kode_dosen') }}" required class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                </div>
                <div class="grid gap-2">
                    <label for="edit-nama_dosen" class="text-sm font-medium text-slate-600">Nama Dosen</label>
                    <input type="text" id="edit-nama_dosen" name="nama_dosen" value="{{ old('nama_dosen') }}" required class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                </div>
                <div class="grid gap-2">
                    <label for="edit-prodi" class="text-sm font-medium text-slate-600">Program Studi</label>
                    <input type="text" id="edit-prodi" name="prodi" value="{{ old('prodi') }}" required class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                </div>
                <div class="grid gap-2">
                    <label for="edit-sub_kelompok_keahlian" class="text-sm font-medium text-slate-600">Sub Kelompok Keahlian</label>
                    <input type="text" id="edit-sub_kelompok_keahlian" name="sub_kelompok_keahlian" value="{{ old('sub_kelompok_keahlian') }}" required class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                </div>
                <div class="grid gap-2">
                    <label for="edit-foto" class="text-sm font-medium text-slate-600">Foto Profil</label>
                    <input type="file" id="edit-foto" name="foto" accept="image/*" class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                    <p class="text-xs text-slate-500">Unggah foto baru untuk mengganti foto yang tersimpan (maksimum 2 MB).</p>
                    <div class="mt-2 flex items-center gap-3" data-preview-container>
                        <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-slate-100 ring-1 ring-slate-200">
                            <img src="{{ $editingProfil?->foto_url }}" alt="{{ $editingProfil?->foto_url ? 'Foto ' . $editingProfil->nama_dosen : '' }}" class="h-full w-full object-cover object-center {{ $editingProfil?->foto_url ? '' : 'hidden' }}" data-preview-image>
                            <span class="text-xs font-medium text-slate-500 {{ $editingProfil?->foto_url ? 'hidden' : '' }}" data-preview-placeholder>Belum ada foto</span>
                        </div>
                        <p class="text-xs text-slate-500">Foto saat ini.</p>
                    </div>
                </div>
                <div class="grid gap-2">
                    <label for="edit-nip" class="text-sm font-medium text-slate-600">NIP</label>
                    <input type="text" id="edit-nip" name="nip" value="{{ old('nip') }}" class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                </div>
                <div class="grid gap-2">
                    <label for="edit-nidn" class="text-sm font-medium text-slate-600">NIDN</label>
                    <input type="text" id="edit-nidn" name="nidn" value="{{ old('nidn') }}" class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100">
                </div>
                <div class="flex items-center justify-end gap-3 pt-4">
                    <button type="button" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100" data-modal-close>Batal</button>
                    <button type="submit" class="rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-6" id="delete-modal">
        <div class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl ring-1 ring-slate-200">
            <button type="button" class="modal-close absolute right-4 top-4 text-lg text-slate-400 transition hover:text-slate-600" data-modal-close>&times;</button>
            <h3 class="text-xl font-semibold text-slate-900">Hapus Profil</h3>
            <p id="delete-message" class="mt-3 text-sm text-slate-600">Apakah Anda yakin ingin menghapus profil ini?</p>
            <form id="delete-form" method="POST" class="mt-6 flex items-center justify-end gap-3">
                @csrf
                @method('DELETE')
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
                <button type="button" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100" data-modal-close>Batal</button>
                <button type="submit" class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600">Hapus</button>
            </form>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        const body = document.body;
        const modals = document.querySelectorAll('.modal');

        function openModal(modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            body.style.overflow = 'hidden';
        }

        function closeModal(modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            if (![...modals].some(m => !m.classList.contains('hidden'))) {
                body.style.overflow = '';
            }
        }

        document.querySelectorAll('[data-modal-target]').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const target = document.getElementById(trigger.dataset.modalTarget);
                if (!target) return;

                if (trigger.dataset.action === 'edit') {
                    const profileData = JSON.parse(trigger.dataset.profile);
                    const template = trigger.dataset.updateTemplate;
                    const form = document.getElementById('edit-form');
                    form.action = template.replace('__ID__', profileData.id);
                    form.querySelector('#edit-kode_dosen').value = profileData.kode_dosen ?? '';
                    form.querySelector('#edit-nama_dosen').value = profileData.nama_dosen ?? '';
                    form.querySelector('#edit-prodi').value = profileData.prodi ?? '';
                    form.querySelector('#edit-sub_kelompok_keahlian').value = profileData.sub_kelompok_keahlian ?? '';
                    form.querySelector('#edit-nip').value = profileData.nip ?? '';
                    form.querySelector('#edit-nidn').value = profileData.nidn ?? '';
                    const previewContainer = form.querySelector('[data-preview-container]');
                    const previewImage = form.querySelector('[data-preview-image]');
                    const previewPlaceholder = form.querySelector('[data-preview-placeholder]');
                    if (previewImage) {
                        if (profileData.foto_url) {
                            previewImage.src = profileData.foto_url;
                            previewImage.alt = `Foto ${profileData.nama_dosen ?? 'dosen'}`;
                            previewImage.classList.remove('hidden');
                            if (previewPlaceholder) {
                                previewPlaceholder.classList.add('hidden');
                            }
                        } else {
                            previewImage.src = '';
                            previewImage.alt = '';
                            previewImage.classList.add('hidden');
                            if (previewPlaceholder) {
                                previewPlaceholder.classList.remove('hidden');
                            }
                        }
                    }
                    if (previewContainer) {
                        previewContainer.classList.remove('hidden');
                    }
                    const idField = form.querySelector('#edit-profil_id');
                    if (idField) {
                        idField.value = profileData.id;
                    }
                }

                if (trigger.dataset.action === 'delete') {
                    const profileData = JSON.parse(trigger.dataset.profile);
                    const template = trigger.dataset.destroyTemplate;
                    const form = document.getElementById('delete-form');
                    form.action = template.replace('__ID__', profileData.id);
                    const message = document.getElementById('delete-message');
                    message.textContent = `Apakah Anda yakin ingin menghapus ${profileData.nama_dosen}?`;
                }

                openModal(target);
            });
        });

        document.querySelectorAll('[data-modal-close]').forEach(button => {
            button.addEventListener('click', () => {
                const modal = button.closest('.modal');
                if (modal) {
                    closeModal(modal);
                }
            });
        });

        modals.forEach(modal => {
            modal.addEventListener('click', event => {
                if (event.target === modal) {
                    closeModal(modal);
                }
            });
        });

        const initialModal = @json($openModal ?? null);
        const initialEditingProfil = @json($editingProfil ?? null);
        const hasErrors = @json($errors->any());
        const wasUpdate = @json(old('_method') === 'PUT');
        const oldProfilId = @json(old('profil_id'));

        if (initialModal === 'create') {
            const modal = document.getElementById('create-modal');
            if (modal) {
                openModal(modal);
            }
        }

        if (initialModal === 'edit' && initialEditingProfil) {
            const modal = document.getElementById('edit-modal');
            const editTrigger = document.querySelector('[data-action="edit"]');
            if (modal && editTrigger) {
                const template = editTrigger.dataset.updateTemplate;
                const form = document.getElementById('edit-form');
                form.action = template.replace('__ID__', initialEditingProfil.id);
                form.querySelector('#edit-kode_dosen').value = initialEditingProfil.kode_dosen ?? '';
                form.querySelector('#edit-nama_dosen').value = initialEditingProfil.nama_dosen ?? '';
                form.querySelector('#edit-prodi').value = initialEditingProfil.prodi ?? '';
                form.querySelector('#edit-sub_kelompok_keahlian').value = initialEditingProfil.sub_kelompok_keahlian ?? '';
                form.querySelector('#edit-nip').value = initialEditingProfil.nip ?? '';
                form.querySelector('#edit-nidn').value = initialEditingProfil.nidn ?? '';
                const previewImage = form.querySelector('[data-preview-image]');
                const previewPlaceholder = form.querySelector('[data-preview-placeholder]');
                if (previewImage) {
                    if (initialEditingProfil.foto_url) {
                        previewImage.src = initialEditingProfil.foto_url;
                        previewImage.alt = `Foto ${initialEditingProfil.nama_dosen ?? 'dosen'}`;
                        previewImage.classList.remove('hidden');
                        if (previewPlaceholder) {
                            previewPlaceholder.classList.add('hidden');
                        }
                    } else {
                        previewImage.src = '';
                        previewImage.alt = '';
                        previewImage.classList.add('hidden');
                        if (previewPlaceholder) {
                            previewPlaceholder.classList.remove('hidden');
                        }
                    }
                }
                const idField = form.querySelector('#edit-profil_id');
                if (idField) {
                    idField.value = initialEditingProfil.id;
                }
                openModal(modal);
            }
        }

        if (hasErrors && !wasUpdate) {
            const modal = document.getElementById('create-modal');
            if (modal) {
                openModal(modal);
            }
        }

        if (hasErrors && wasUpdate && oldProfilId) {
            const modal = document.getElementById('edit-modal');
            const editButtons = Array.from(document.querySelectorAll('[data-action="edit"]'));
            const form = document.getElementById('edit-form');
            const matchedButton = editButtons.find(button => {
                try {
                    const profileData = JSON.parse(button.dataset.profile);
                    return Number(profileData.id) === Number(oldProfilId);
                } catch (error) {
                    return false;
                }
            });

            if (modal && form) {
                const template = (matchedButton ?? editButtons[0])?.dataset.updateTemplate;
                if (template) {
                    form.action = template.replace('__ID__', oldProfilId);
                }

                if (matchedButton) {
                    try {
                        const profileData = JSON.parse(matchedButton.dataset.profile);
                        const previewImage = form.querySelector('[data-preview-image]');
                        const previewPlaceholder = form.querySelector('[data-preview-placeholder]');
                        if (previewImage) {
                            if (profileData.foto_url) {
                                previewImage.src = profileData.foto_url;
                                previewImage.alt = `Foto ${profileData.nama_dosen ?? 'dosen'}`;
                                previewImage.classList.remove('hidden');
                                if (previewPlaceholder) {
                                    previewPlaceholder.classList.add('hidden');
                                }
                            } else {
                                previewImage.src = '';
                                previewImage.alt = '';
                                previewImage.classList.add('hidden');
                                if (previewPlaceholder) {
                                    previewPlaceholder.classList.remove('hidden');
                                }
                            }
                        }
                    } catch (error) {
                        // ignore JSON parse issues
                    }
                }

                openModal(modal);
            }
        }
    </script>
@endpush
