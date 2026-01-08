@extends('layouts.app')

@section('title', 'Tambah Profil Dosen')

@section('sidebar')
    <x-sidebar class="h-full" />
@endsection

@section('content')
    <div class="rounded-3xl bg-white p-8 shadow-xl ring-1 ring-slate-200">
        <div class="space-y-1">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Profil Dosen</p>
            <h1 class="text-2xl font-semibold text-slate-900">Tambah Profil Dosen</h1>
            <p class="text-sm text-slate-500">Lengkapi informasi profil dosen untuk kelompok keahlian RIIB.</p>
        </div>

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

        <form action="{{ route('profils.store') }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-8">
            @csrf
            <input type="hidden" name="sort" value="{{ old('sort', $sort) }}">
            <input type="hidden" name="direction" value="{{ old('direction', $direction) }}">

            @include('profil.partials.form-fields', ['profil' => null])

            <div class="flex items-center justify-end gap-3">
                <a
                    href="{{ route('profils.index', ['sort' => $sort, 'direction' => $direction]) }}"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                >
                    Simpan Profil
                </button>
            </div>
        </form>
    </div>
@endsection
