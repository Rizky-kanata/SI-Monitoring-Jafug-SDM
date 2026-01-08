@php
    $profil = $profil ?? null;
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div class="grid gap-2">
        <label for="kode_dosen" class="text-sm font-medium text-slate-600">Kode Dosen</label>
        <input
            type="text"
            id="kode_dosen"
            name="kode_dosen"
            value="{{ old('kode_dosen', $profil?->kode_dosen) }}"
            required
            class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
        >
    </div>
    <div class="grid gap-2">
        <label for="nama_dosen" class="text-sm font-medium text-slate-600">Nama Dosen</label>
        <input
            type="text"
            id="nama_dosen"
            name="nama_dosen"
            value="{{ old('nama_dosen', $profil?->nama_dosen) }}"
            required
            class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
        >
    </div>
    <div class="grid gap-2">
        <label for="prodi" class="text-sm font-medium text-slate-600">Program Studi</label>
        <input
            type="text"
            id="prodi"
            name="prodi"
            value="{{ old('prodi', $profil?->prodi) }}"
            required
            class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
        >
    </div>
    <div class="grid gap-2">
        <label for="sub_kelompok_keahlian" class="text-sm font-medium text-slate-600">Sub Kelompok Keahlian</label>
        <input
            type="text"
            id="sub_kelompok_keahlian"
            name="sub_kelompok_keahlian"
            value="{{ old('sub_kelompok_keahlian', $profil?->sub_kelompok_keahlian) }}"
            required
            class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
        >
    </div>
    <div class="grid gap-2">
        <label for="lab" class="text-sm font-medium text-slate-600">Lab</label>
        <input
            type="text"
            id="lab"
            name="lab"
            value="{{ old('lab', $profil?->lab) }}"
            class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
        >
    </div>
    <div class="grid gap-2">
        <label for="nip" class="text-sm font-medium text-slate-600">NIP</label>
        <input
            type="text"
            id="nip"
            name="nip"
            value="{{ old('nip', $profil?->nip) }}"
            class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
        >
    </div>
    <div class="grid gap-2">
        <label for="nidn" class="text-sm font-medium text-slate-600">NIDN</label>
        <input
            type="text"
            id="nidn"
            name="nidn"
            value="{{ old('nidn', $profil?->nidn) }}"
            class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
        >
    </div>
</div>

<div class="grid gap-2">
    <label for="foto" class="text-sm font-medium text-slate-600">Foto Profil</label>
    <input
        type="file"
        id="foto"
        name="foto"
        accept="image/*"
        class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
    >
    <p class="text-xs text-slate-500">
        {{ $profil ? 'Unggah foto baru untuk mengganti foto yang tersimpan (maksimum 2 MB).' : 'Format JPG, PNG, atau WEBP dengan ukuran maksimum 2 MB.' }}
    </p>
    @if ($profil?->foto_url)
        <div class="mt-2 flex items-center gap-3">
            <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-slate-100 ring-1 ring-slate-200">
                <img src="{{ $profil->foto_url }}" alt="Foto {{ $profil->nama_dosen }}" class="h-full w-full object-cover object-center">
            </div>
            <p class="text-xs text-slate-500">Foto saat ini.</p>
        </div>
    @endif
</div>
