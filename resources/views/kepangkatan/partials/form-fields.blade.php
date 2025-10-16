@php
    $selectedKodeDosen = old('kode_dosen', optional($kepangkatan)->kode_dosen);
    $selectedStatus = old('status_publikasi', optional($kepangkatan)->status_publikasi);
    $tanggalTmtValue = old('tanggal_tmt', optional(optional($kepangkatan)->tanggal_tmt)?->format('Y-m-d'));
@endphp

<div class="space-y-6">
    <div>
        <label for="kode_dosen" class="block text-sm font-semibold text-slate-700">Kode Dosen</label>
        <p class="mt-1 text-xs text-slate-500">Pilih dosen berdasarkan data profil yang telah tersedia.</p>
        <select
            id="kode_dosen"
            name="kode_dosen"
            class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            required
        >
            <option value="">— Pilih Kode Dosen —</option>
            @foreach ($profilOptions as $profil)
                <option value="{{ $profil['kode_dosen'] }}" @selected($profil['kode_dosen'] === $selectedKodeDosen)>
                    {{ $profil['kode_dosen'] }} — {{ $profil['nama_dosen'] }}
                </option>
            @endforeach
        </select>
        @error('kode_dosen')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="jabatan_fungsional" class="block text-sm font-semibold text-slate-700">Jabatan Fungsional</label>
        <input
            type="text"
            id="jabatan_fungsional"
            name="jabatan_fungsional"
            value="{{ old('jabatan_fungsional', optional($kepangkatan)->jabatan_fungsional) }}"
            class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            required
        >
        @error('jabatan_fungsional')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tanggal_tmt" class="block text-sm font-semibold text-slate-700">Tanggal TMT</label>
        <p class="mt-1 text-xs text-slate-500">Opsional, isi apabila terdapat tanggal mulai tugas (TMT).</p>
        <input
            type="date"
            id="tanggal_tmt"
            name="tanggal_tmt"
            value="{{ $tanggalTmtValue }}"
            class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
        >
        @error('tanggal_tmt')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status_publikasi" class="block text-sm font-semibold text-slate-700">Status Publikasi</label>
        <p class="mt-1 text-xs text-slate-500">Pilih status terkini dari proses publikasi kepangkatan.</p>
        <select
            id="status_publikasi"
            name="status_publikasi"
            class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            required
        >
            <option value="">— Pilih Status Publikasi —</option>
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}" @selected($value === $selectedStatus)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status_publikasi')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>
