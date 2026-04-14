@extends('layouts.app')

@section('title', 'Ubah Data Kepangkatan')

@section('sidebar')
    <x-sidebar class="h-full" />
@endsection

@section('content')
    <div class="overflow-hidden rounded-[2rem] bg-white shadow-xl ring-1 ring-slate-200">
        <div class="border-b border-slate-200 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 px-8 py-8 text-white">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-blue-200">Kepangkatan Dosen</p>
                    <h1 class="text-3xl font-semibold tracking-tight text-white">Ubah Data Kepangkatan</h1>
                    <p class="max-w-2xl text-sm leading-6 text-slate-300">Perbarui detail kenaikan pangkat untuk {{ $kepangkatan->profil?->nama_dosen ?? 'dosen terkait' }} supaya catatan monitoring tetap akurat.</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 px-5 py-4 text-sm text-slate-200 shadow-lg backdrop-blur">
                    <p class="text-[0.7rem] font-semibold uppercase tracking-[0.24em] text-slate-400">Mode Edit</p>
                    <p class="mt-2 max-w-sm leading-6">Sesuaikan data yang berubah tanpa perlu input ulang seluruh profil dosen.</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            @if ($errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50/80 px-4 py-3 text-sm text-rose-700" role="alert">
                    <strong class="font-semibold">Terjadi kesalahan:</strong>
                    <ul class="mt-3 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('kepangkatan.update', $kepangkatan) }}" method="POST" class="mt-8 space-y-8">
                @csrf
                @method('PUT')

                @include('kepangkatan.partials.form-fields', [
                    'kepangkatan' => $kepangkatan,
                    'profilOptions' => $profilOptions,
                    'jabatanOptions' => $jabatanOptions,
                    'pangkatOptions' => $pangkatOptions,
                    'golonganOptions' => $golonganOptions,
                ])

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-end">
                    <a href="{{ route('kepangkatan.index') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-full bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
