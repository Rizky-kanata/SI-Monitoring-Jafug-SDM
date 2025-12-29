@extends('layouts.app')

@section('title', 'Dashboard Admin RIIB')

@section('sidebar')
    <x-sidebar class="h-full" />
@endsection

@section('content')
    <div class="grid gap-6">
        <header class="rounded-3xl bg-white p-8 shadow-xl ring-1 ring-slate-200">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Selamat datang kembali</p>
                    <h1 class="mt-2 text-3xl font-semibold text-slate-900">
                        {{ $user?->name ?? 'Administrator' }}
                    </h1>
                    <p class="mt-2 max-w-xl text-sm text-slate-500">
                        Lihat ringkasan singkat mengenai aktivitas data profil dan kepangkatan dosen RIIB.
                    </p>
                </div>
                <div class="rounded-2xl bg-slate-900 px-6 py-4 text-white shadow-lg">
                    <p class="text-xs uppercase tracking-wide text-slate-300">Total Modul Aktif</p>
                    <p class="mt-2 text-3xl font-semibold">2</p>
                    <p class="text-xs text-slate-300">Profil Dosen &amp; Kepangkatan</p>
                </div>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-3xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
                <p class="text-sm font-medium text-slate-500">Total Profil Dosen</p>
                <p class="mt-3 text-4xl font-semibold text-slate-900">{{ number_format($totalProfil) }}</p>
                <p class="mt-2 text-sm text-slate-500">Jumlah dosen dalam basis data RIIB.</p>
                <a
                    href="{{ route('profils.index') }}"
                    class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700"
                >
                    Kelola Profil
                    <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
                <p class="text-sm font-medium text-slate-500">Total Data Kepangkatan</p>
                <p class="mt-3 text-4xl font-semibold text-slate-900">{{ number_format($totalKepangkatan) }}</p>
                <p class="mt-2 text-sm text-slate-500">Cakupan data kenaikan pangkat yang tercatat.</p>
                <a
                    href="{{ route('kepangkatan.index') }}"
                    class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700"
                >
                    Kelola Kepangkatan
                    <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="rounded-3xl bg-gradient-to-br from-blue-600 to-blue-700 p-6 text-white shadow-lg">
                <p class="text-sm font-medium text-blue-100">Status Kepangkatan</p>
                <ul class="mt-3 space-y-2 text-sm text-blue-50">
                    <li>- Merah: Sudah lewat TMT tapi belum mengurus Publikasi.</li>
                    <li>- Kuning: Masih dalam periode TMT dan belum punya publikasi dan/atau jika sudah melewati periode TMT namun sudah mengurus publikasi.</li>
                    <li>- Hijau: Jika sedang maupun belum menjalani periode TMT namun sudah mengurus publikasi.</li>
                </ul>
                <p class="mt-4 text-xs uppercase tracking-wide text-blue-100">Gunakan modul kepangkatan untuk memperbarui status.</p>
            </div>
        </section>

        <section class="rounded-3xl bg-white p-6 shadow-xl ring-1 ring-slate-200">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Butuh Bantuan?</h2>
                    <p class="text-sm text-slate-500">Hubungi tim support untuk bantuan teknis atau kendala penggunaan dashboard.</p>
                </div>
                <a
                    href="https://api.whatsapp.com/send/?phone={{ config('services.whatsapp.recipient') ?? '6289516003000' }}&text&type=phone_number&app_absent=0"
                    class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Hubungi Support via WhatsApp
                    <span aria-hidden="true">↗</span>
                </a>
            </div>
        </section>

        <section class="rounded-3xl bg-white p-6 shadow-xl ring-1 ring-slate-200">
            <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Aktivitas Kepangkatan Terbaru</h2>
                    <p class="text-sm text-slate-500">Lima pembaruan terakhir di modul kepangkatan.</p>
                </div>
                <a
                    href="{{ route('kepangkatan.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700"
                >
                    Lihat semua data
                    <span aria-hidden="true">→</span>
                </a>
            </header>

            <div class="mt-6">
                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50/80 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Dosen</th>
                                <th class="px-5 py-3">Jabatan</th>
                                <th class="px-5 py-3">Status TMT</th>
                                <th class="px-5 py-3">Status Publikasi</th>
                                <th class="px-5 py-3">Diubah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($recentKepangkatan as $record)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-slate-800">{{ $record->profil?->nama_dosen ?? 'Tanpa nama' }}</div>
                                        <div class="text-xs text-slate-500">{{ $record->profil?->kode_dosen ?? 'Tidak ada kode' }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ $record->jabatan_fungsional_label ?? '-' }}</td>
                                    <td class="px-5 py-4 text-slate-600">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                            {{ $record->status_tmt_label }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $record->is_published ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-amber-50 text-amber-700 ring-amber-200' }} ring-1">
                                            {{ $record->status_publikasi_label }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-slate-500">{{ $record->updated_at?->diffForHumans() ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-6 text-center text-sm text-slate-500">
                                        Belum ada aktivitas kepangkatan yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
