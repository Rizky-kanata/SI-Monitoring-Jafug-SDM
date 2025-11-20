<?php

namespace App\Http\Controllers;

use App\Models\Profil;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{

    public function index(Request $request)
    {
        return $this->renderIndex($request);
    }

    public function create(Request $request)
    {
        return $this->renderIndex($request, 'create');
    }

    public function store(Request $request)
    {
        [$sort, $direction] = $this->resolveSort($request);

        $validated = $request->validate([
            'kode_dosen' => ['required', 'string', Rule::unique('profils', 'kode_dosen')],
            'nama_dosen' => ['required', 'string'],
            'prodi' => ['required', 'string'],
            'sub_kelompok_keahlian' => ['required', 'string'],
            'nip' => ['nullable', 'string', Rule::unique('profils', 'nip')],
            'nidn' => ['nullable', 'string', Rule::unique('profils', 'nidn')],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        Profil::create($this->prepareProfilData($validated, $request));

        return redirect()
            ->route('profils.index', compact('sort', 'direction'))
            ->with('success', 'Profil created successfully.');
    }

    public function edit(Request $request, Profil $profil)
    {
        return $this->renderIndex($request, 'edit', $profil);
    }

    public function update(Request $request, Profil $profil)
    {
        [$sort, $direction] = $this->resolveSort($request);

        $validated = $request->validate([
            'kode_dosen' => ['required', 'string', Rule::unique('profils', 'kode_dosen')->ignore($profil->id)],
            'nama_dosen' => ['required', 'string'],
            'prodi' => ['required', 'string'],
            'sub_kelompok_keahlian' => ['required', 'string'],
            'nip' => ['nullable', 'string', Rule::unique('profils', 'nip')->ignore($profil->id)],
            'nidn' => ['nullable', 'string', Rule::unique('profils', 'nidn')->ignore($profil->id)],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $profil->update($this->prepareProfilData($validated, $request, $profil));

        return redirect()
            ->route('profils.index', compact('sort', 'direction'))
            ->with('success', 'Profil updated successfully.');
    }

    public function destroy(Request $request, Profil $profil)
    {
        [$sort, $direction] = $this->resolveSort($request);

        if ($profil->foto_path) {
            Storage::disk('public')->delete($profil->foto_path);
        }

        $profil->delete();

        return redirect()
            ->route('profils.index', compact('sort', 'direction'))
            ->with('success', 'Profil deleted successfully.');
    }

    private function renderIndex(Request $request, ?string $openModal = null, ?Profil $editingProfil = null)
    {
        [$sort, $direction] = $this->resolveSort($request);

        $profils = Profil::orderBy($sort, $direction)->get();
        $editingProfil = $editingProfil?->fresh();

        return view('dosens', [
            'profils' => $profils,
            'openModal' => $openModal,
            'editingProfil' => $editingProfil,
            'sort' => $sort,
            'direction' => $direction,
            'total' => $profils->count(),
        ]);
    }

    private function resolveSort(Request $request): array
    {
        $allowedSorts = ['nama_dosen', 'prodi', 'sub_kelompok_keahlian', 'created_at'];
        $sort = $request->input('sort', $request->query('sort'));
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'nama_dosen';
        }

        $direction = $request->input('direction', $request->query('direction'));
        $direction = $direction === 'desc' ? 'desc' : 'asc';

        return [$sort, $direction];
    }

    private function prepareProfilData(array $validated, Request $request, ?Profil $profil = null): array
    {
        $data = Arr::except($validated, ['foto']);

        if ($request->hasFile('foto')) {
            if ($profil && $profil->foto_path) {
                Storage::disk('public')->delete($profil->foto_path);
            }

            $data['foto_path'] = $request->file('foto')->store('profil-fotos', 'public');
        } elseif (!$profil) {
            $data['foto_path'] = null;
        }

        return $data;
    }
}
