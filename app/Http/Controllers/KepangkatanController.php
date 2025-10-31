<?php

namespace App\Http\Controllers;

use App\Models\Kepangkatan;
use App\Models\Profil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KepangkatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->string('status')->toString();
        $search = $request->string('search')->toString();

        $records = Kepangkatan::query()
            ->with('profil')
            ->when(
                $statusFilter !== '' && array_key_exists($statusFilter, Kepangkatan::statusOptions()),
                fn ($query) => $query->where('status', $statusFilter)
            )
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('profil', function ($relation) use ($search) {
                    $relation
                        ->where('nama_dosen', 'like', '%' . $search . '%')
                        ->orWhere('kode_dosen', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view('kepangkatan.index', [
            'kepangkatans' => $records,
            'statusOptions' => Kepangkatan::statusOptions(),
            'statusMetadata' => Kepangkatan::statusMetadata(),
            'filters' => [
                'status' => $statusFilter,
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('kepangkatan.create', [
            'profilOptions' => $this->profilOptions(),
            'statusOptions' => Kepangkatan::statusOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        Kepangkatan::create($validated);

        return redirect()
            ->route('kepangkatan.index')
            ->with('success', 'Data kepangkatan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kepangkatan $kepangkatan): View
    {
        return view('kepangkatan.edit', [
            'kepangkatan' => $kepangkatan->load('profil'),
            'profilOptions' => $this->profilOptions($kepangkatan->profil_id),
            'statusOptions' => Kepangkatan::statusOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kepangkatan $kepangkatan): RedirectResponse
    {
        $validated = $this->validatePayload($request, $kepangkatan->id);

        $kepangkatan->update($validated);

        return redirect()
            ->route('kepangkatan.index')
            ->with('success', 'Data kepangkatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kepangkatan $kepangkatan): RedirectResponse
    {
        $kepangkatan->delete();

        return redirect()
            ->route('kepangkatan.index')
            ->with('success', 'Data kepangkatan berhasil dihapus.');
    }

    /**
     * Ambil daftar profil yang belum memiliki data kepangkatan (kecuali profil yang sedang diedit).
     *
     * @return array<int, array<string, string|int>>
     */
    private function profilOptions(?int $currentProfilId = null): array
    {
        $assigned = Kepangkatan::query()
            ->when($currentProfilId, fn ($query) => $query->where('profil_id', '!=', $currentProfilId))
            ->pluck('profil_id')
            ->all();

        return Profil::query()
            ->orderBy('nama_dosen')
            ->get(['id', 'kode_dosen', 'nama_dosen'])
            ->filter(function (Profil $profil) use ($assigned, $currentProfilId) {
                if ($currentProfilId !== null && $profil->id === $currentProfilId) {
                    return true;
                }

                return ! in_array($profil->id, $assigned, true);
            })
            ->map(fn (Profil $profil) => [
                'id' => $profil->id,
                'kode_dosen' => $profil->kode_dosen,
                'nama_dosen' => $profil->nama_dosen,
            ])
            ->values()
            ->all();
    }

    /**
     * Validasi data kepangkatan sebelum disimpan.
     *
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $statusKeys = array_keys(Kepangkatan::statusMetadata());

        return $request->validate([
            'profil_id' => [
                'required',
                'integer',
                Rule::exists('profils', 'id'),
                Rule::unique('kepangkatans', 'profil_id')->ignore($ignoreId),
            ],
            'jabatan_fungsional' => ['required', 'string', 'max:255'],
            'pangkat' => ['nullable', 'string', 'max:255'],
            'golongan' => ['nullable', 'string', 'max:255'],
            'tanggal_sk' => ['nullable', 'date'],
            'tanggal_mulai' => ['nullable', 'date'],
            'status' => ['required', Rule::in($statusKeys)],
            'catatan' => ['nullable', 'string'],
        ], [
            'profil_id.required' => 'Profil dosen wajib dipilih.',
            'profil_id.exists' => 'Profil dosen tidak ditemukan.',
            'profil_id.unique' => 'Profil dosen sudah memiliki data kepangkatan.',
        ]);
    }
}
