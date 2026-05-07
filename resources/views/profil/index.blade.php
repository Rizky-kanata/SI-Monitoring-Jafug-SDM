@extends('layouts.app')

@section('title', 'Profil Dosen SDM')

@section('sidebar')
    <x-sidebar class="h-full" />
@endsection

@section('content')
    <div class="rounded-3xl bg-white p-8 shadow-xl ring-1 ring-slate-200">
        <div class="space-y-2">
            <h1 class="text-2xl font-semibold text-slate-900">Daftar Profil Dosen SDM</h1>
            <p class="text-sm text-slate-500">Ringkasan informasi profil dosen versi SDM.</p>
        </div>

        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-[48rem] divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50/80">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-3">Kode Dosen</th>
                        <th class="px-5 py-3">Nama Dosen</th>
                        <th class="px-5 py-3">Program Studi</th>
                        <th class="px-5 py-3">Kelompok Keahlian</th>
                        <th class="px-5 py-3">NIP</th>
                        <th class="px-5 py-3">NIDN</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse ($profils as $profil)
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-5 py-4 font-medium text-slate-700">{{ $profil->kode_dosen }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $profil->nama_dosen }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $profil->prodi }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $profil->kelompok_keahlian }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $profil->nip ?? '-' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $profil->nidn ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">Belum ada data profil.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
