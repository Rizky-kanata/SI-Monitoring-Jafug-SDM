<?php

namespace App\Http\Controllers;

use App\Models\Profil;
use App\Support\SimpleXlsx;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfilController extends Controller
{
    private const IMPORT_HEADERS = [
        'kode_dosen',
        'nama_dosen',
        'prodi',
        'kelompok_keahlian',
        'coe',
        'nip',
        'nidn',
    ];

    public function index(Request $request)
    {
        return $this->renderIndex($request);
    }

    public function create(Request $request)
    {
        return view('profil.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->profilRules());

        Profil::create($this->prepareProfilData($validated));

        return redirect()
            ->route('profils.index')
            ->with('success', 'Data dosen berhasil ditambahkan.');
    }

    public function edit(Request $request, Profil $profil)
    {
        return view('profil.edit', [
            'profil' => $profil,
        ]);
    }

    public function update(Request $request, Profil $profil)
    {
        $validated = $request->validate($this->profilRules($profil));

        $profil->update($this->prepareProfilData($validated));

        return redirect()
            ->route('profils.index')
            ->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function destroy(Request $request, Profil $profil)
    {
        $profil->delete();

        return redirect()
            ->route('profils.index')
            ->with('success', 'Data dosen berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $content = SimpleXlsx::create([
            'Kode Dosen',
            'Nama Dosen',
            'Program Studi',
            'Kelompok Keahlian',
            'CoE',
            'NIP',
            'NIDN',
        ], [[
            'NZR',
            'Mochamad Nizar Palefi Maady',
            'Sistem Informasi',
            'IEBI',
            'INTEREST',
            '21910012',
            '0708119103',
        ]], [
            'text_columns' => [5, 6],
        ]);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'template-import-dosen-sdm.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx'],
        ], [
            'excel_file.required' => 'File Excel wajib diunggah.',
            'excel_file.mimes' => 'Format file harus .xlsx.',
        ]);

        $rows = $this->readImportedRows($request->file('excel_file'));

        if (count($rows) < 2) {
            throw ValidationException::withMessages([
                'excel_file' => 'File import belum berisi data dosen. Pakai template yang sudah disediakan ya.',
            ]);
        }

        $headers = array_map([$this, 'normalizeHeader'], $rows[0]);

        if ($headers !== self::IMPORT_HEADERS) {
            throw ValidationException::withMessages([
                'excel_file' => 'Header template tidak cocok. Download ulang template Excel lalu isi sesuai kolom yang ada.',
            ]);
        }

        $validatedRows = [];
        $seenKodeDosen = [];
        $seenNip = [];
        $seenNidn = [];
        $errors = [];
        $skipped = 0;

        foreach (array_slice($rows, 1) as $index => $row) {
            $rowNumber = $index + 2;
            $payload = $this->mapImportedRow($row);

            if ($this->rowIsEmpty($payload)) {
                $skipped++;

                continue;
            }

            $existingProfil = Profil::query()
                ->where('kode_dosen', $payload['kode_dosen'])
                ->first();

            $validator = Validator::make($payload, $this->profilRules($existingProfil), [], [
                'kode_dosen' => 'kode_dosen',
                'nama_dosen' => 'nama_dosen',
                'prodi' => 'prodi',
                'kelompok_keahlian' => 'kelompok_keahlian',
                'coe' => 'coe',
                'nip' => 'nip',
                'nidn' => 'nidn',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = "Baris {$rowNumber}: {$message}";
                }

                continue;
            }

            $this->collectDuplicateError($payload['kode_dosen'], $rowNumber, 'kode_dosen', $seenKodeDosen, $errors);
            $this->collectDuplicateError($payload['nip'], $rowNumber, 'nip', $seenNip, $errors);
            $this->collectDuplicateError($payload['nidn'], $rowNumber, 'nidn', $seenNidn, $errors);

            $validatedRows[] = [
                'existing' => $existingProfil,
                'payload' => $validator->validated(),
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
            foreach ($validatedRows as $row) {
                /** @var \App\Models\Profil|null $existing */
                $existing = $row['existing'];
                $payload = $row['payload'];

                if ($existing) {
                    $existing->update($payload);
                    $updated++;
                } else {
                    Profil::create($payload);
                    $created++;
                }
            }
        });

        $message = "Import Excel selesai. {$created} data baru ditambahkan, {$updated} data diperbarui.";

        if ($skipped > 0) {
            $message .= " {$skipped} baris kosong dilewati.";
        }

        return redirect()
            ->route('profils.index')
            ->with('success', $message);
    }

    private function renderIndex(Request $request)
    {
        [$sort, $direction] = $this->resolveSort($request);

        $query = Profil::query();

        // Search berdasarkan nama dosen
        if ($request->filled('search')) {
            $query->where('nama_dosen', 'like', '%' . $request->search . '%');
        }

        $profils = $query->orderBy($sort, $direction)->get();

        return view('dosens', [
            'profils' => $profils,
            'sort' => $sort,
            'direction' => $direction,
            'total' => $profils->count(),
            'importHeaders' => self::IMPORT_HEADERS,
        ]);
    }

    private function resolveSort(Request $request): array
    {
        $allowedSorts = ['nama_dosen', 'prodi', 'kelompok_keahlian', 'created_at'];
        $sort = $request->input('sort', $request->query('sort'));
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'nama_dosen';
        }

        $direction = $request->input('direction', $request->query('direction'));
        $direction = $direction === 'desc' ? 'desc' : 'asc';

        return [$sort, $direction];
    }

    private function prepareProfilData(array $validated): array
    {
        return $validated;
    }

    private function readImportedRows($file): array
    {
        $extension = strtolower((string) $file->getClientOriginalExtension());

        if ($extension === 'csv') {
            return $this->readCsvRows($file->getRealPath());
        }

        return SimpleXlsx::readRows($file);
    }

    private function readCsvRows(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw ValidationException::withMessages([
                'excel_file' => 'File CSV tidak bisa dibaca.',
            ]);
        }

        $rows = [];

        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if (count($rows) === 0 && isset($row[0])) {
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $row[0]);
            }

            $rows[] = array_map(
                fn ($value) => is_string($value) ? trim($value) : $value,
                $row
            );
        }

        fclose($handle);

        return $rows;
    }

    private function profilRules(?Profil $profil = null): array
    {
        return [
            'kode_dosen' => ['required', 'string', Rule::unique('profils', 'kode_dosen')->ignore($profil?->id)],
            'nama_dosen' => ['required', 'string'],
            'prodi' => ['required', 'string'],
            'kelompok_keahlian' => ['required', 'string'],
            'coe' => ['nullable', 'string'],
            'nip' => ['nullable', 'string', Rule::unique('profils', 'nip')->ignore($profil?->id)],
            'nidn' => ['nullable', 'string', Rule::unique('profils', 'nidn')->ignore($profil?->id)],
        ];
    }

    private function normalizeHeader(string $header): string
    {
        $normalized = strtolower(trim($header));
        $normalized = str_replace(['`', '.', '-'], ['', '', ' '], $normalized);
        $normalized = preg_replace('/\s+/', '_', $normalized);

        return match ($normalized) {
            'kode' => 'kode_dosen',
            'kode_dosen' => 'kode_dosen',
            'nama' => 'nama_dosen',
            'nama_dosen' => 'nama_dosen',
            'program_studi' => 'prodi',
            'prodi' => 'prodi',
            'kelompok_keahlian' => 'kelompok_keahlian',
            'coe' => 'coe',
            'co_e' => 'coe',
            'nip' => 'nip',
            'nidn' => 'nidn',
            default => $normalized,
        };
    }

    private function mapImportedRow(array $row): array
    {
        $row = array_pad($row, count(self::IMPORT_HEADERS), '');
        $mapped = array_combine(self::IMPORT_HEADERS, array_slice($row, 0, count(self::IMPORT_HEADERS)));

        return collect($mapped)
            ->map(function ($value) {
                $value = trim((string) $value);
                $value = ltrim($value, "'");

                return $value === '' ? null : $value;
            })
            ->all();
    }

    private function rowIsEmpty(array $row): bool
    {
        return collect($row)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->isEmpty();
    }

    private function collectDuplicateError(?string $value, int $rowNumber, string $field, array &$storage, array &$errors): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $normalized = strtolower(trim($value));

        if (isset($storage[$normalized])) {
            $errors[] = "Baris {$rowNumber}: {$field} duplikat dengan baris {$storage[$normalized]}.";

            return;
        }

        $storage[$normalized] = $rowNumber;
    }
}
