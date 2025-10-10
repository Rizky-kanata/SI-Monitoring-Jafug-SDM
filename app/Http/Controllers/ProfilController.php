<?php

namespace App\Http\Controllers;

use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function index()
    {
        $profils = Profil::orderBy('nama_dosen')->get();

        return view('dosens', [
            'profils' => $profils,
            'openModal' => null,
            'editingProfil' => null,
        ]);
    }

    public function create()
    {
        $profils = Profil::orderBy('nama_dosen')->get();

        return view('dosens', [
            'profils' => $profils,
            'openModal' => 'create',
            'editingProfil' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_dosen' => ['required', 'string', Rule::unique('profils', 'kode_dosen')],
            'nama_dosen' => ['required', 'string'],
            'prodi' => ['required', 'string'],
            'kelompok_keahlian' => ['required', 'string'],
            'sub_kelompok_keahlian' => ['required', 'string'],
            'nip' => ['nullable', 'string', Rule::unique('profils', 'nip')],
            'nidn' => ['nullable', 'string', Rule::unique('profils', 'nidn')],
        ]);

        Profil::create($validated);

        return redirect()->route('profils.index')->with('success', 'Profil created successfully.');
    }

    public function edit(Profil $profil)
    {
        $profils = Profil::orderBy('nama_dosen')->get();

        return view('dosens', [
            'profils' => $profils,
            'openModal' => 'edit',
            'editingProfil' => $profil,
        ]);
    }

    public function update(Request $request, Profil $profil)
    {
        $validated = $request->validate([
            'kode_dosen' => ['required', 'string', Rule::unique('profils', 'kode_dosen')->ignore($profil->id)],
            'nama_dosen' => ['required', 'string'],
            'prodi' => ['required', 'string'],
            'kelompok_keahlian' => ['required', 'string'],
            'sub_kelompok_keahlian' => ['required', 'string'],
            'nip' => ['nullable', 'string', Rule::unique('profils', 'nip')->ignore($profil->id)],
            'nidn' => ['nullable', 'string', Rule::unique('profils', 'nidn')->ignore($profil->id)],
        ]);

        $profil->update($validated);

        return redirect()->route('profils.index')->with('success', 'Profil updated successfully.');
    }

    public function destroy(Profil $profil)
    {
        $profil->delete();

        return redirect()->route('profils.index')->with('success', 'Profil deleted successfully.');
    }
}
