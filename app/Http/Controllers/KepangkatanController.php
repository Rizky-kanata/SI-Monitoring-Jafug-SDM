<?php

namespace App\Http\Controllers;

use App\Models\Kepangkatan;
use App\Models\Profil;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class KepangkatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = $this->resolveFilters($request);
        $recordsQuery = $this->buildFilteredQuery($filters)
            ->with('profil')
            ->orderByDesc('updated_at');

        $perPage = $this->resolvePerPage($filters['per_page'], $recordsQuery);
        $records = $recordsQuery->paginate($perPage)->withQueryString();

        return view('kepangkatan.index', [
            'kepangkatans' => $records,
            'tmtStatusOptions' => Kepangkatan::tmtStatusOptions(),
            'filters' => $filters,
            'perPageOptions' => $this->perPageOptions(),
        ]);
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->resolveFilters($request);
        $records = $this->buildFilteredQuery($filters)
            ->with('profil')
            ->orderByDesc('updated_at')
            ->get();

        $statusLabel = $filters['tmt_status'] !== ''
            ? (Kepangkatan::tmtStatusOptions()[$filters['tmt_status']] ?? $filters['tmt_status'])
            : 'Semua Status';

        $filenameSuffix = $filters['tmt_status'] !== '' ? $filters['tmt_status'] : 'semua';
        $filename = 'kepangkatan-tmt-' . Str::slug($filenameSuffix) . '.pdf';

        return Pdf::loadView('kepangkatan.pdf', [
            'records' => $records,
            'filters' => $filters,
            'statusLabel' => $statusLabel,
        ])->download($filename);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('kepangkatan.create', [
            'profilOptions' => $this->profilOptions(),
            'jabatanOptions' => Kepangkatan::jabatanOptions(),
            'pangkatOptions' => Kepangkatan::pangkatOptions(),
            'golonganOptions' => Kepangkatan::golonganOptions(),
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
            'jabatanOptions' => Kepangkatan::jabatanOptions(),
            'pangkatOptions' => Kepangkatan::pangkatOptions(),
            'golonganOptions' => Kepangkatan::golonganOptions(),
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
        $data = $request->validate([
            'profil_id' => [
                'required',
                'integer',
                Rule::exists('profils', 'id'),
                Rule::unique('kepangkatans', 'profil_id')->ignore($ignoreId),
            ],
            'jabatan_fungsional' => ['required', Rule::in(array_keys(Kepangkatan::jabatanOptions()))],
            'pangkat' => ['nullable', Rule::in(array_keys(Kepangkatan::pangkatOptions()))],
            'golongan' => ['nullable', Rule::in(array_keys(Kepangkatan::golonganOptions()))],
            'tanggal_sk' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
            'catatan' => ['nullable', 'string'],
        ], [
            'profil_id.required' => 'Profil dosen wajib dipilih.',
            'profil_id.exists' => 'Profil dosen tidak ditemukan.',
            'profil_id.unique' => 'Profil dosen sudah memiliki data kepangkatan.',
            'pangkat.in' => 'Pangkat harus dipilih dari daftar yang tersedia.',
            'golongan.in' => 'Golongan harus dipilih dari daftar yang tersedia.',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['tanggal_mulai'] = $data['tanggal_sk'] ?? null;
        $data['tanggal_tmt'] = $data['tanggal_sk'] ?? null;

        return $data;
    }

    /**
     * @return array<string, string>
     */
    private function resolveFilters(Request $request): array
    {
        return [
            'publication' => $request->string('publication')->toString(),
            'tmt_status' => $request->string('tmt_status')->toString(),
            'search' => $request->string('search')->toString(),
            'per_page' => $request->string('per_page')->toString(),
        ];
    }

    private function buildFilteredQuery(array $filters): Builder
    {
        $publicationFilter = $filters['publication'];
        $tmtStatusFilter = $filters['tmt_status'];
        $search = $filters['search'];

        return Kepangkatan::query()
            ->when($publicationFilter !== '', function ($query) use ($publicationFilter) {
                if (in_array($publicationFilter, ['sudah', 'belum'], true)) {
                    $query->where('is_published', $publicationFilter === 'sudah');
                }
            })
            ->when(
                $tmtStatusFilter !== '' && array_key_exists($tmtStatusFilter, Kepangkatan::tmtStatusOptions()),
                fn ($query) => $query->whereTmtStatus($tmtStatusFilter)
            )
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('profil', function ($relation) use ($search) {
                    $relation
                        ->where('nama_dosen', 'like', '%' . $search . '%')
                        ->orWhere('kode_dosen', 'like', '%' . $search . '%');
                });
            });
    }

    /**
     * @return array<int|string, string>
     */
    private function perPageOptions(): array
    {
        return [
            10 => '10',
            25 => '25',
            50 => '50',
            75 => '75',
            100 => '100',
            'all' => 'All',
        ];
    }

    private function resolvePerPage(string $value, Builder $query): int
    {
        $options = array_keys($this->perPageOptions());
        $value = $value === '' ? '10' : $value;

        if ($value === 'all') {
            $count = (clone $query)->count();
            return max(1, $count);
        }

        $numeric = (int) $value;

        if (! in_array($numeric, $options, true)) {
            $numeric = 10;
        }

        return $numeric;
    }
}
