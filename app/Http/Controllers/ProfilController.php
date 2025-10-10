<?php

namespace App\Http\Controllers;

use App\Models\Profil;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function index()
    {
        $profils = Profil::all();

        return view('profils.index', compact('profils'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_dosen' => 'required|unique:profils,kode_dosen',
            'nama_dosen' => 'required',
            'prodi' => 'required',
            'kelompok_keahlian' => 'required',
            'sub_kelompok_keahlian' => 'required',
            'nip' => 'nullable|unique:profils,nip',
            'nidn' => 'nullable|unique:profils,nidn',
        ]);

        Profil::create($request->only([
            'kode_dosen',
            'nama_dosen',
            'prodi',
            'kelompok_keahlian',
            'sub_kelompok_keahlian',
            'nip',
            'nidn',
        ]));

        return redirect()->route('profils.index')->with('success', 'Profil created successfully.');
    }
}
