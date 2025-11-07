@php
    $selectedProfil = old('profil_id', optional($kepangkatan)->profil_id);
    $selectedStatus = old('status', optional($kepangkatan)->status);
    $tanggalSk = old('tanggal_sk', optional(optional($kepangkatan)->tanggal_sk)?->format('Y-m-d'));
    $tanggalMulai = old('tanggal_mulai', optional(optional($kepangkatan)->tanggal_mulai)?->format('Y-m-d'));
    $tanggalTmt = old(
        'tanggal_tmt',
        optional(optional($kepangkatan)->tanggal_tmt ?? optional($kepangkatan)->tanggal_mulai)?->format('Y-m-d')
    );
    $isPublished = old(
        'is_published',
        optional($kepangkatan)->is_published ? '1' : '0'
    );
@endphp

<div class="space-y-6">
    <div>
        <label for="profil_id" class="block text-sm font-semibold text-slate-700">Profil Dosen</label>
        <p class="mt-1 text-xs text-slate-500">Hubungkan data kepangkatan dengan dosen yang sudah terdaftar.</p>
        <select
            id="profil_id"
            name="profil_id"
            class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            required
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
            <label for="pangkat" class="block text-sm font-semibold text-slate-700">Pangkat</label>
            <input
                type="text"
                id="pangkat"
                name="pangkat"
                value="{{ old('pangkat', optional($kepangkatan)->pangkat) }}"
                placeholder="Contoh: Penata Muda Tk. I"
                class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            >
            @error('pangkat')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="golongan" class="block text-sm font-semibold text-slate-700">Golongan</label>
            <input
                type="text"
                id="golongan"
                name="golongan"
                value="{{ old('golongan', optional($kepangkatan)->golongan) }}"
                placeholder="Contoh: III/b"
                class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            >
            @error('golongan')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="status" class="block text-sm font-semibold text-slate-700">Status Proses</label>
            <select
                id="status"
                name="status"
                class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                required
            >
                <option value="">-- Pilih Status --</option>
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected($value === $selectedStatus)>{{ $label }}</option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="tanggal_sk" class="block text-sm font-semibold text-slate-700">Tanggal SK</label>
            <input
                type="date"
                id="tanggal_sk"
                name="tanggal_sk"
                value="{{ $tanggalSk }}"
                class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            >
            @error('tanggal_sk')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="tanggal_mulai" class="block text-sm font-semibold text-slate-700">Tanggal Mulai Tugas</label>
            <input
                type="date"
                id="tanggal_mulai"
                name="tanggal_mulai"
                value="{{ $tanggalMulai }}"
                class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            >
            @error('tanggal_mulai')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="tanggal_tmt" class="block text-sm font-semibold text-slate-700">Tanggal TMT</label>
            <input
                type="date"
                id="tanggal_tmt"
                name="tanggal_tmt"
                value="{{ $tanggalTmt }}"
                class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            >
            <p class="mt-2 text-xs text-slate-500">Tanggal mulai masa TMT. Dihitung selama 2 tahun sejak tanggal ini.</p>
            @error('tanggal_tmt')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
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
            class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            placeholder="Tuliskan catatan tambahan atau tindak lanjut yang perlu dilakukan.">{{ old('catatan', optional($kepangkatan)->catatan) }}</textarea>
        @error('catatan')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>
