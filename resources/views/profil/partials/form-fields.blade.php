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
        <label for="kelompok_keahlian" class="text-sm font-medium text-slate-600">Kelompok Keahlian</label>
        <input
            type="text"
            id="kelompok_keahlian"
            name="kelompok_keahlian"
            value="{{ old('kelompok_keahlian', $profil?->kelompok_keahlian) }}"
            required
            class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
        >
    </div>
    <div class="grid gap-2">
        <label for="coe" class="text-sm font-medium text-slate-600">CoE</label>
        <input
            type="text"
            id="coe"
            name="coe"
            value="{{ old('coe', $profil?->coe) }}"
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
