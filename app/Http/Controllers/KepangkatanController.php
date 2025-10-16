<?php

namespace App\Http\Controllers;

use App\Models\Kepangkatan;
use App\Models\Profil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KepangkatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $sortable = [
            'nama_dosen',
            'kode_dosen',
            'jabatan_fungsional',
            'tanggal_tmt',
            'status_publikasi',
        ];

        $sort = $request->string('sort')->toString() ?: 'nama_dosen';
        $direction = $request->string('direction')->lower() === 'desc' ? 'desc' : 'asc';

        if (! in_array($sort, $sortable, true)) {
            $sort = 'nama_dosen';
        }

        $query = Kepangkatan::query()->with('profil');

        if ($sort === 'tanggal_tmt') {
            $query
                ->orderByRaw('tanggal_tmt IS NULL')
                ->orderBy('tanggal_tmt', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $kepangkatans = $query->paginate(12)->withQueryString();
        $statusMetadata = Kepangkatan::statusMetadata();
        $statusIndicators = $kepangkatans->mapWithKeys(function (Kepangkatan $record) use ($statusMetadata) {
            return [$record->id => $this->makeIndicatorFor($record, $statusMetadata)];
        });

        return view('kepangkatan.index', [
            'kepangkatans' => $kepangkatans,
            'statusIndicators' => $statusIndicators,
            'statusMetadata' => $statusMetadata,
            'statusOptions' => Kepangkatan::statusOptions(),
            'sort' => $sort,
            'direction' => $direction,
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

        $profil = Profil::where('kode_dosen', $validated['kode_dosen'])->firstOrFail();

        Kepangkatan::create([
            'nama_dosen' => $profil->nama_dosen,
            'kode_dosen' => $validated['kode_dosen'],
            'jabatan_fungsional' => $validated['jabatan_fungsional'],
            'tanggal_tmt' => $validated['tanggal_tmt'],
            'status_publikasi' => $validated['status_publikasi'],
        ]);

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
            'kepangkatan' => $kepangkatan,
            'profilOptions' => $this->profilOptions(),
            'statusOptions' => Kepangkatan::statusOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kepangkatan $kepangkatan): RedirectResponse
    {
        $validated = $this->validatePayload($request, $kepangkatan->id);

        $profil = Profil::where('kode_dosen', $validated['kode_dosen'])->firstOrFail();

        $kepangkatan->update([
            'nama_dosen' => $profil->nama_dosen,
            'kode_dosen' => $validated['kode_dosen'],
            'jabatan_fungsional' => $validated['jabatan_fungsional'],
            'tanggal_tmt' => $validated['tanggal_tmt'],
            'status_publikasi' => $validated['status_publikasi'],
        ]);

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
     * @return array<int, array<string, string>>
     */
    private function profilOptions(): array
    {
        return Profil::query()
            ->orderBy('nama_dosen')
            ->get(['kode_dosen', 'nama_dosen'])
            ->map(fn (Profil $profil) => [
                'kode_dosen' => $profil->kode_dosen,
                'nama_dosen' => $profil->nama_dosen,
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $statusKeys = array_keys(Kepangkatan::statusMetadata());

        return $request->validate([
            'kode_dosen' => [
                'required',
                'string',
                'max:255',
                Rule::exists('profils', 'kode_dosen'),
                Rule::unique('kepangkatans', 'kode_dosen')->ignore($ignoreId),
            ],
            'jabatan_fungsional' => ['required', 'string', 'max:255'],
            'tanggal_tmt' => ['nullable', 'date'],
            'status_publikasi' => ['required', Rule::in($statusKeys)],
        ], [
            'kode_dosen.required' => 'Kode dosen wajib dipilih.',
            'kode_dosen.exists' => 'Kode dosen tidak ditemukan dalam data profil.',
            'kode_dosen.unique' => 'Kode dosen sudah memiliki data kepangkatan.',
        ]);
    }

    /**
     * @param  array<string, array<string, string>>  $statusMetadata
     * @return array{label: string, description: string, badge: string}
     */
    private function makeIndicatorFor(Kepangkatan $record, array $statusMetadata): array
    {
        $metadata = $statusMetadata[$record->status_publikasi] ?? [
            'label' => 'Status Tidak Dikenal',
            'description' => 'Status publikasi tidak terdaftar dalam sistem.',
            'badge' => 'bg-slate-100 text-slate-700 ring-slate-200',
        ];

        $tanggalTmt = $record->tanggal_tmt;

        if ($tanggalTmt === null) {
            return [
                'label' => 'TMT Belum Diisi',
                'description' => 'Lengkapi tanggal TMT untuk memastikan monitoring kepangkatan.',
                'badge' => 'bg-amber-100 text-amber-700 ring-amber-200',
            ];
        }

        if ($tanggalTmt->lte(Carbon::now()->subYears(2))) {
            return [
                'label' => $metadata['label'] . ' • Perlu Pembaruan TMT',
                'description' => 'Tanggal TMT lebih dari 2 tahun yang lalu. Pertimbangkan untuk memperbarui data kepangkatan.',
                'badge' => 'bg-rose-100 text-rose-700 ring-rose-200',
            ];
        }

        return $metadata;
    }
}
