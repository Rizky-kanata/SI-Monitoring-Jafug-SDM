@php
    $selectedProfil = old('profil_id', optional($kepangkatan)->profil_id);
    $selectedJabatan = old('jabatan_fungsional', optional($kepangkatan)->jabatan_fungsional);
    $selectedPangkat = old('pangkat', optional($kepangkatan)->pangkat);
    $selectedGolongan = old('golongan', optional($kepangkatan)->golongan);
    $tanggalSk = old('tanggal_sk', optional(optional($kepangkatan)->tanggal_sk)?->format('Y-m-d'));
    $isPublished = old(
        'is_published',
        optional($kepangkatan)->is_published ? '1' : '0'
    );
    $jabatanLabel = $selectedJabatan !== null && $selectedJabatan !== ''
        ? (($jabatanOptions ?? \App\Models\Kepangkatan::jabatanOptions())[$selectedJabatan] ?? $selectedJabatan) . ' (' . $selectedJabatan . ')'
        : '-- Pilih Jabatan --';
@endphp

<div class="space-y-8">
    <section class="rounded-[1.75rem] border border-slate-200 bg-slate-50/70 p-6 shadow-sm">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600">Profil</p>
                <h2 class="mt-2 text-lg font-semibold text-slate-900">Pilih Dosen</h2>
                <p class="mt-1 text-sm text-slate-500">Hubungkan data kepangkatan dengan dosen yang sudah terdaftar di sistem.</p>
            </div>
            <div class="rounded-2xl bg-white px-4 py-3 text-xs text-slate-500 ring-1 ring-slate-200">
                Pastikan profil dosen yang dipilih belum punya data kepangkatan ganda.
            </div>
        </div>

        <div class="relative mt-5">
            <select
                id="profil_id"
                name="profil_id"
                class="w-full appearance-none rounded-3xl border border-slate-200 bg-white px-5 py-3.5 pr-12 text-sm font-medium text-slate-700 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                required
            >
                <option value="">-- Pilih Profil Dosen --</option>
                @foreach ($profilOptions as $profil)
                    <option value="{{ $profil['id'] }}" @selected((int) $profil['id'] === (int) $selectedProfil)>
                        {{ $profil['kode_dosen'] }} - {{ $profil['nama_dosen'] }}
                    </option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                <span class="inline-flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                    </svg>
                </span>
            </div>
        </div>
        @error('profil_id')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </section>

    <section class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">Rincian Jabatan</p>
            <h2 class="mt-2 text-lg font-semibold text-slate-900">Informasi Kepangkatan</h2>
            <p class="mt-1 text-sm text-slate-500">Lengkapi jabatan, pangkat, golongan, dan tanggal SK untuk kebutuhan monitoring.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label for="jabatan_fungsional" class="block text-sm font-semibold text-slate-700">Jabatan Fungsional</label>
                <div class="relative mt-3" data-custom-select>
                <select
                    id="jabatan_fungsional"
                    name="jabatan_fungsional"
                    class="sr-only"
                    required
                    data-custom-select-native
                >
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach (($jabatanOptions ?? \App\Models\Kepangkatan::jabatanOptions()) as $code => $label)
                        <option value="{{ $code }}" @selected($code === $selectedJabatan)>
                            {{ $label }} ({{ $code }})
                        </option>
                    @endforeach
                </select>
                <button
                    type="button"
                    class="flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-left text-sm text-slate-700 shadow-sm transition hover:border-slate-300 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    data-custom-select-trigger
                    aria-haspopup="listbox"
                    aria-expanded="false"
                >
                    <span data-custom-select-label>{{ $jabatanLabel }}</span>
                    <span class="ml-4 inline-flex items-center justify-center text-slate-400 transition" data-custom-select-icon>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </button>
                <div
                    class="absolute left-0 right-0 top-[calc(100%+0.75rem)] z-30 hidden overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl ring-1 ring-slate-100"
                    data-custom-select-menu
                >
                    <div class="max-h-72 overflow-y-auto p-2">
                        <button
                            type="button"
                            class="flex w-full items-center rounded-2xl px-4 py-3 text-left text-sm font-medium text-slate-600 transition hover:bg-slate-100"
                            data-custom-select-option
                            data-value=""
                            data-label="-- Pilih Jabatan --"
                        >
                            -- Pilih Jabatan --
                        </button>
                        @foreach (($jabatanOptions ?? \App\Models\Kepangkatan::jabatanOptions()) as $code => $label)
                            <button
                                type="button"
                                class="mt-1 flex w-full items-center justify-between rounded-2xl px-4 py-3 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                                data-custom-select-option
                                data-value="{{ $code }}"
                                data-label="{{ $label }} ({{ $code }})"
                            >
                                <span>{{ $label }}</span>
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">{{ $code }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
            @error('jabatan_fungsional')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
            </div>
            <div>
                <label for="pangkat" class="block text-sm font-semibold text-slate-700">Pangkat</label>
                <div class="relative mt-3">
                    <select
                        id="pangkat"
                        name="pangkat"
                        class="w-full appearance-none rounded-3xl border border-slate-200 bg-white px-5 py-3.5 pr-12 text-sm font-medium text-slate-700 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                    >
                        <option value="">-- Pilih Pangkat --</option>
                        @foreach (($pangkatOptions ?? \App\Models\Kepangkatan::pangkatOptions()) as $value => $label)
                            <option value="{{ $value }}" @selected($value === $selectedPangkat)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                        <span class="inline-flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </div>
                </div>
                @error('pangkat')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <div>
                <label for="golongan" class="block text-sm font-semibold text-slate-700">Golongan</label>
                <div class="relative mt-3">
                    <select
                        id="golongan"
                        name="golongan"
                        class="w-full appearance-none rounded-3xl border border-slate-200 bg-white px-5 py-3.5 pr-12 text-sm font-medium text-slate-700 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                    >
                        <option value="">-- Pilih Golongan --</option>
                        @foreach (($golonganOptions ?? \App\Models\Kepangkatan::golonganOptions()) as $value => $label)
                            <option value="{{ $value }}" @selected($value === $selectedGolongan)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400">
                        <span class="inline-flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </div>
                </div>
                @error('golongan')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="tanggal_sk" class="block text-sm font-semibold text-slate-700">Tanggal SK</label>
                <div class="relative mt-3">
                    <input
                        type="date"
                        id="tanggal_sk"
                        name="tanggal_sk"
                        value="{{ $tanggalSk }}"
                        class="w-full rounded-3xl border border-slate-200 bg-white px-5 py-3.5 text-sm font-medium text-slate-700 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                    >
                    <div class="pointer-events-none absolute inset-y-0 right-5 flex items-center text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6.75h15A1.5 1.5 0 0 1 21 8.25v9.75a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 18V8.25a1.5 1.5 0 0 1 1.5-1.5Z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-xs text-slate-500">Tanggal ini dipakai sistem untuk membaca masa TMT dan indikator monitoring.</p>
                @error('tanggal_sk')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 grid gap-4 xl:grid-cols-[0.9fr_1.1fr]">
            <div class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5">
                <label class="block text-sm font-semibold text-slate-700">Status Publikasi</label>
                <p class="mt-1 text-xs text-slate-500">Tandai apakah publikasi untuk data kepangkatan ini sudah selesai diurus.</p>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <label class="group cursor-pointer">
                        <input
                            type="radio"
                            name="is_published"
                            value="1"
                            class="peer sr-only"
                            @checked($isPublished === '1')
                        >
                        <span class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition peer-checked:border-emerald-300 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 group-hover:border-emerald-200">
                            <span>Sudah</span>
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        </span>
                    </label>
                    <label class="group cursor-pointer">
                        <input
                            type="radio"
                            name="is_published"
                            value="0"
                            class="peer sr-only"
                            @checked($isPublished === '0')
                        >
                        <span class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition peer-checked:border-amber-300 peer-checked:bg-amber-50 peer-checked:text-amber-700 group-hover:border-amber-200">
                            <span>Belum</span>
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                        </span>
                    </label>
                </div>
                @error('is_published')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <label for="catatan" class="block text-sm font-semibold text-slate-700">Catatan</label>
                <p class="mt-1 text-xs text-slate-500">Isi ringkasan tindak lanjut, catatan berkas, atau info penting lain untuk SDM.</p>
                <textarea
                    id="catatan"
                    name="catatan"
                    rows="5"
                    class="mt-4 w-full rounded-3xl border border-slate-200 bg-slate-50/60 px-4 py-3 text-sm text-slate-700 shadow-sm transition focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100"
                    placeholder="Tuliskan catatan tambahan atau tindak lanjut yang perlu dilakukan.">{{ old('catatan', optional($kepangkatan)->catatan) }}</textarea>
                @error('catatan')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>
</div>

@once
    @push('scripts')
        <script>
            (function () {
                const instances = document.querySelectorAll('[data-custom-select]');
                if (!instances.length) return;

                instances.forEach((wrapper) => {
                    const nativeSelect = wrapper.querySelector('[data-custom-select-native]');
                    const trigger = wrapper.querySelector('[data-custom-select-trigger]');
                    const menu = wrapper.querySelector('[data-custom-select-menu]');
                    const label = wrapper.querySelector('[data-custom-select-label]');
                    const icon = wrapper.querySelector('[data-custom-select-icon]');
                    const options = wrapper.querySelectorAll('[data-custom-select-option]');

                    if (!nativeSelect || !trigger || !menu || !label || !icon || !options.length) return;

                    const closeMenu = () => {
                        menu.classList.add('hidden');
                        trigger.setAttribute('aria-expanded', 'false');
                        icon.classList.remove('rotate-180');
                    };

                    const openMenu = () => {
                        menu.classList.remove('hidden');
                        trigger.setAttribute('aria-expanded', 'true');
                        icon.classList.add('rotate-180');
                    };

                    const setSelected = (value, text) => {
                        nativeSelect.value = value;
                        label.textContent = text;

                        options.forEach((option) => {
                            const isActive = option.dataset.value === value;
                            option.classList.toggle('bg-slate-900', isActive);
                            option.classList.toggle('text-white', isActive);
                            option.classList.toggle('hover:bg-slate-100', !isActive);
                            option.classList.toggle('text-slate-700', !isActive);

                            const badge = option.querySelector('span:last-child');
                            if (badge) {
                                badge.classList.toggle('bg-white/15', isActive);
                                badge.classList.toggle('text-white', isActive);
                                badge.classList.toggle('bg-slate-100', !isActive);
                                badge.classList.toggle('text-slate-500', !isActive);
                            }
                        });
                    };

                    trigger.addEventListener('click', () => {
                        const isHidden = menu.classList.contains('hidden');
                        document.querySelectorAll('[data-custom-select-menu]').forEach((otherMenu) => {
                            if (otherMenu !== menu) {
                                otherMenu.classList.add('hidden');
                            }
                        });
                        document.querySelectorAll('[data-custom-select-trigger]').forEach((otherTrigger) => {
                            if (otherTrigger !== trigger) {
                                otherTrigger.setAttribute('aria-expanded', 'false');
                            }
                        });
                        document.querySelectorAll('[data-custom-select-icon]').forEach((otherIcon) => {
                            if (otherIcon !== icon) {
                                otherIcon.classList.remove('rotate-180');
                            }
                        });

                        if (isHidden) {
                            openMenu();
                        } else {
                            closeMenu();
                        }
                    });

                    options.forEach((option) => {
                        option.addEventListener('click', () => {
                            setSelected(option.dataset.value || '', option.dataset.label || option.textContent.trim());
                            closeMenu();
                        });
                    });

                    document.addEventListener('click', (event) => {
                        if (!wrapper.contains(event.target)) {
                            closeMenu();
                        }
                    });

                    document.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape') {
                            closeMenu();
                        }
                    });

                    setSelected(nativeSelect.value, label.textContent.trim());
                });
            })();
        </script>
    @endpush
@endonce
