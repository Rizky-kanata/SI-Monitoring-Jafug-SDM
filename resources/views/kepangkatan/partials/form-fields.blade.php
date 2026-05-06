@php
    $selectedProfil = old('profil_id', optional($kepangkatan)->profil_id);
    $selectedJabatan = old('jabatan_fungsional', optional($kepangkatan)->jabatan_fungsional);
    $selectedPangkat = old('pangkat', optional($kepangkatan)->pangkat);
    $selectedGolongan = old('golongan', optional($kepangkatan)->golongan);
    $tanggalSk = old('tanggal_sk', optional(optional($kepangkatan)->tanggal_sk)?->format('Y-m-d'));
    $isPublished = old('is_published', optional($kepangkatan)->is_published ? '1' : '0');
@endphp

<div class="space-y-6">
    <div>
        <label for="profil_id" class="block text-sm font-semibold text-slate-700">Profil Dosen</label>
        <p class="mt-1 text-xs text-slate-500">Hubungkan data kepangkatan dengan dosen yang sudah terdaftar.</p>
        <select
            id="profil_id"
            name="profil_id"
            required
            class="mt-3 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
        >
            <option value="">-- Pilih Profil Dosen --</option>
            @foreach ($profilOptions as $profil)
                <option value="{{ $profil['id'] }}" @selected((int) $profil['id'] === (int) $selectedProfil)>
                    {{ $profil['kode_dosen'] }} - {{ $profil['nama_dosen'] }}
                </option>
            @endforeach
        </select>
        @error('profil_id')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="jabatan_fungsional" class="block text-sm font-semibold text-slate-700">Jabatan Fungsional</label>
            <select
                id="jabatan_fungsional"
                name="jabatan_fungsional"
                required
                class="mt-3 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            >
                <option value="">-- Pilih Jabatan --</option>
                @foreach (($jabatanOptions ?? \App\Models\Kepangkatan::jabatanOptions()) as $code => $label)
                    <option value="{{ $code }}" @selected($code === $selectedJabatan)>
                        {{ $label }} ({{ $code }})
                    </option>
                @endforeach
            </select>
            @error('jabatan_fungsional')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="pangkat" class="block text-sm font-semibold text-slate-700">Pangkat</label>
            <select
                id="pangkat"
                name="pangkat"
                class="mt-3 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            >
                <option value="">-- Pilih Pangkat --</option>
                @foreach (($pangkatOptions ?? \App\Models\Kepangkatan::pangkatOptions()) as $value => $label)
                    <option value="{{ $value }}" @selected($value === $selectedPangkat)>{{ $label }}</option>
                @endforeach
            </select>
            @error('pangkat')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="golongan" class="block text-sm font-semibold text-slate-700">Golongan</label>
            <select
                id="golongan"
                name="golongan"
                class="mt-3 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            >
                <option value="">-- Pilih Golongan --</option>
                @foreach (($golonganOptions ?? \App\Models\Kepangkatan::golonganOptions()) as $value => $label)
                    <option value="{{ $value }}" @selected($value === $selectedGolongan)>{{ $label }}</option>
                @endforeach
            </select>
            @error('golongan')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="tanggal_sk" class="block text-sm font-semibold text-slate-700">Tanggal SK</label>
            <input
                type="date"
                id="tanggal_sk"
                name="tanggal_sk"
                value="{{ $tanggalSk }}"
                class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            >
            @error('tanggal_sk')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-slate-700">Status Publikasi</label>
            <div class="mt-4 flex flex-wrap gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input
                        type="radio"
                        name="is_published"
                        value="1"
                        class="h-4 w-4 border-slate-300 text-emerald-600 focus:ring-emerald-500"
                        @checked($isPublished === '1')
                    >
                    Sudah
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input
                        type="radio"
                        name="is_published"
                        value="0"
                        class="h-4 w-4 border-slate-300 text-amber-600 focus:ring-amber-500"
                        @checked($isPublished === '0')
                    >
                    Belum
                </label>
            </div>
            @error('is_published')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="catatan" class="block text-sm font-semibold text-slate-700">Catatan</label>
        <textarea
            id="catatan"
            name="catatan"
            rows="4"
            class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            placeholder="Tuliskan catatan tambahan atau tindak lanjut yang perlu dilakukan."
        >{{ old('catatan', optional($kepangkatan)->catatan) }}</textarea>
        @error('catatan')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>
