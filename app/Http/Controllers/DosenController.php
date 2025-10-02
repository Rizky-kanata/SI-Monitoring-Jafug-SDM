<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function index()
    {
        $dosens = Dosen::all();
        return view('dosens', compact('dosens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_dosen' => 'required|unique:dosens,kode_dosen',
            'nama_dosen' => 'required',
            'prodi' => 'required',
            'kelompok_keahlian' => 'required',
            'jabatan_fungsional' => 'required',
            'sub_kelompok_keahlian' => 'required',
        ]);

        Dosen::create($request->all());

        return redirect()->route('dosens.index')->with('success', 'Dosen created successfully.');
    }
}
