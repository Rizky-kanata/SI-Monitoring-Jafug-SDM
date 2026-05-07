<?php

namespace App\Http\Controllers;

use App\Models\Kepangkatan;
use App\Models\Profil;
use App\Support\SimpleXlsx;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class KepangkatanController extends Controller
{
    private const TEMPLATE_HEADERS = [
        'nama_dosen',
        'jabatan_fungsional',
        'pangkat',
        'golongan',
        'tanggal_sk',
        'status_publikasi',
    ];

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
            'metaDataCount' => Profil::query()->count(),
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

    public function downloadTemplate()
    {
        $content = SimpleXlsx::create(self::TEMPLATE_HEADERS, [[
            'Nama Dosen',
            'Lektor',
            'Penata',
            'III/c',
            '2026-04-22',
            'Belum',
        ]]);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'template-monitoring-kepangkatan.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx'],
        ], [
            'excel_file.required' => 'File Excel wajib diunggah.',
            'excel_file.mimes' => 'Format file harus .xlsx.',
        ]);

        $rows = SimpleXlsx::readRows($request->file('excel_file'));

        if (count($rows) < 2) {
            throw ValidationException::withMessages([
                'excel_file' => 'File import belum berisi data kepangkatan. Download dulu template yang sudah disediakan.',
            ]);
        }

        $headers = array_map([$this, 'normalizeHeader'], $rows[0]);

        if ($headers !== self::TEMPLATE_HEADERS) {
            throw ValidationException::withMessages([
                'excel_file' => 'Header template tidak cocok. Download ulang template Excel kepangkatan lalu isi sesuai kolom yang ada.',
            ]);
        }

        $validatedRows = [];
        $errors = [];
        $seenProfilIds = [];
        $skipped = 0;

        foreach (array_slice($rows, 1) as $index => $row) {
            $rowNumber = $index + 2;
            $payload = $this->mapImportedRow($row);

            if ($this->rowIsEmpty($payload)) {
                $skipped++;
                continue;
            }

            $namaDosen = $this->normalizeImportedNamaDosen($payload['nama_dosen']);

            if ($namaDosen === null) {
                $errors[] = "Baris {$rowNumber}: kolom nama_dosen wajib diisi.";
                continue;
            }

            $matchingProfils = Profil::query()
                ->where('nama_dosen', $namaDosen)
                ->get();

            if ($matchingProfils->isEmpty()) {
                $errors[] = "Baris {$rowNumber}: nama_dosen `{$namaDosen}` belum ada di Meta Data Dosen.";
                continue;
            }

            if ($matchingProfils->count() > 1) {
                $errors[] = "Baris {$rowNumber}: nama_dosen `{$namaDosen}` terdeteksi ganda di Meta Data Dosen. Rapikan dulu data dosennya atau pakai nama yang unik.";
                continue;
            }

            $existingProfil = $matchingProfils->first();

            $profilUniqKey = 'existing:' . $existingProfil->id;

            if (isset($seenProfilIds[$profilUniqKey])) {
                $errors[] = "Baris {$rowNumber}: nama_dosen `{$existingProfil->nama_dosen}` terduplikasi di file import.";
                continue;
            }

            $seenProfilIds[$profilUniqKey] = true;

            $normalizedPayload = [
                'jabatan_fungsional' => $this->normalizeJabatan($payload['jabatan_fungsional']),
                'pangkat' => $payload['pangkat'] !== '' ? $payload['pangkat'] : null,
                'golongan' => $payload['golongan'] !== '' ? $payload['golongan'] : null,
                'tanggal_sk' => $payload['tanggal_sk'] !== '' ? $payload['tanggal_sk'] : null,
                'is_published' => $this->normalizePublished($payload['status_publikasi']),
                'catatan' => null,
            ];

            $validator = Validator::make($normalizedPayload, [
                'jabatan_fungsional' => ['required', Rule::in(array_keys(Kepangkatan::jabatanOptions()))],
                'pangkat' => ['nullable', Rule::in(array_keys(Kepangkatan::pangkatOptions()))],
                'golongan' => ['nullable', Rule::in(array_keys(Kepangkatan::golonganOptions()))],
                'tanggal_sk' => ['nullable', 'date'],
                'is_published' => ['required', 'boolean'],
                'catatan' => ['nullable', 'string'],
            ], [
                'jabatan_fungsional.in' => 'jabatan_fungsional harus dipilih dari daftar yang tersedia.',
                'pangkat.in' => 'pangkat harus sesuai daftar pangkat yang tersedia.',
                'golongan.in' => 'golongan harus sesuai daftar golongan yang tersedia.',
                'tanggal_sk.date' => 'tanggal_sk harus berupa tanggal yang valid (contoh: 2026-04-22).',
                'is_published.required' => 'status_publikasi wajib diisi dengan `Sudah` atau `Belum`.',
                'is_published.boolean' => 'status_publikasi wajib diisi dengan `Sudah` atau `Belum`.',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = "Baris {$rowNumber}: {$message}";
                }

                continue;
            }

            $validatedRows[] = [
                'existing_profil_id' => $existingProfil->id,
                'kepangkatan_payload' => $validator->validated(),
            ];
        }

        if ($errors !== []) {
            throw ValidationException::withMessages([
                'excel_file' => implode(' ', $errors),
            ]);
        }

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($validatedRows, &$created, &$updated) {
            foreach ($validatedRows as $payload) {
                $profilId = $payload['existing_profil_id'];

                $recordPayload = $payload['kepangkatan_payload'];
                $recordPayload['profil_id'] = $profilId;
                $recordPayload['tanggal_mulai'] = $recordPayload['tanggal_sk'];
                $recordPayload['tanggal_tmt'] = $recordPayload['tanggal_sk'];

                $existing = Kepangkatan::query()->where('profil_id', $profilId)->first();

                if ($existing) {
                    $existing->update($recordPayload);
                    $updated++;
                } else {
                    Kepangkatan::create($recordPayload);
                    $created++;
                }
            }
        });

        $message = "Import Excel kepangkatan selesai. {$created} data baru ditambahkan, {$updated} data diperbarui.";

        if ($skipped > 0) {
            $message .= " {$skipped} baris kosong dilewati.";
        }

        return redirect()
            ->route('kepangkatan.index')
            ->with('success', $message);
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

    private function normalizeHeader(string $header): string
    {
        return Str::of($header)
            ->trim()
            ->lower()
            ->replace([' ', '-'], '_')
            ->replace('/', '_')
            ->toString();
    }

    private function mapImportedRow(array $row): array
    {
        $values = array_values($row);

        return [
            'nama_dosen' => trim((string) ($values[0] ?? '')),
            'jabatan_fungsional' => trim((string) ($values[1] ?? '')),
            'pangkat' => trim((string) ($values[2] ?? '')),
            'golongan' => trim((string) ($values[3] ?? '')),
            'tanggal_sk' => trim((string) ($values[4] ?? '')),
            'status_publikasi' => trim((string) ($values[5] ?? '')),
        ];
    }

    private function rowIsEmpty(array $payload): bool
    {
        foreach ($payload as $value) {
            if ($value !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeImportedNamaDosen(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        return $value;
    }

    private function normalizeJabatan(string $value): string
    {
        $value = trim($value);
        $upper = strtoupper($value);

        if (isset(Kepangkatan::jabatanOptions()[$upper])) {
            return $upper;
        }

        foreach (Kepangkatan::jabatanOptions() as $code => $label) {
            if (strcasecmp($label, $value) === 0) {
                return $code;
            }
        }

        return $value;
    }

    private function normalizePublished(string $value): ?bool
    {
        $normalized = Str::lower(trim($value));

        return match ($normalized) {
            'sudah', 'ya', 'yes', 'true', '1' => true,
            'belum', 'tidak', 'no', 'false', '0' => false,
            default => null,
        };
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
