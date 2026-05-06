@extends('layouts.app')

@section('title', 'Dashboard Monitoring Kepangkatan SDM')

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
                        Lihat ringkasan singkat mengenai aktivitas data dosen dan kepangkatan versi SDM.
                    </p>
                </div>
            </div>
        </header>

        <section class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-3xl bg-white p-6 shadow-lg ring-1 ring-slate-200">
                <p class="text-sm font-medium text-slate-500">Total Profil Dosen</p>
                <p class="mt-3 text-4xl font-semibold text-slate-900">{{ number_format($totalProfil) }}</p>
                <p class="mt-2 text-sm text-slate-500">Jumlah dosen dalam basis data SDM.</p>
                <a
                    href="{{ route('profils.index') }}"
                    class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700"
                >
                    Kelola Profil
                    <span aria-hidden="true">&rarr;</span>
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
                    <span aria-hidden="true">&rarr;</span>
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
                    href="https://api.whatsapp.com/send/?phone=628123122708&text&type=phone_number&app_absent=0"
                    class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-5 py-2.5 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <img
                        src="{{ asset('images/whats.png') }}"
                        alt=""
                        class="h-5 w-5 object-contain"
                    >
                    Hubungi Support via WhatsApp
                </a>
            </div>
        </section>
    </div>
@endsection
