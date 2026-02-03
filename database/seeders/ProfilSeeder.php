<?php

namespace Database\Seeders;

use App\Models\Profil;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ProfilSeeder extends Seeder
{
    private const LAB_OPTIONS = [
        'Lab Data & AI',
        'Lab Rekayasa Perangkat Lunak',
        'Lab Sistem Cerdas',
        'Lab Multimedia & Jaringan',
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('database/data/dosen_riib.json');
        $json = file_get_contents($path);

        if ($json === false) {
            throw new RuntimeException("Gagal membaca file data profil: {$path}");
        }

        $json = preg_replace('/^\xEF\xBB\xBF/', '', $json ?? '');
        $data = json_decode($json, true);

        if (! is_array($data)) {
            throw new RuntimeException('Format JSON dosen_riib.json tidak valid: ' . json_last_error_msg());
        }

        foreach ($data as $item) {
            Profil::updateOrCreate(
                ['kode_dosen' => $item['kode_dosen']],
                [
                    'nama_dosen' => $item['nama_dosen'],
                    'prodi' => $item['prodi'],
                    'sub_kelompok_keahlian' => $item['sub_kelompok_keahlian'],
                    'lab' => $this->resolveLab($item),
                    'nip' => $this->normalizeIdentifier($item['nip'] ?? null),
                    'nidn' => $this->normalizeIdentifier($item['nidn'] ?? null),
                    'foto_path' => $this->resolveFotoPath($item['foto'] ?? null),
                ]
            );
        }
    }

    private function normalizeIdentifier(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '#N/A') {
            return null;
        }

        return $value;
    }

    private function resolveFotoPath(?string $filename): ?string
    {
        $filename = trim((string) $filename);

        if ($filename === '') {
            return null;
        }

        $path = 'profil-fotos/' . ltrim($filename, "/\\");

        return Storage::disk('public')->exists($path) ? $path : null;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function resolveLab(array $item): string
    {
        $lab = trim((string) ($item['lab'] ?? ''));

        if ($lab !== '') {
            return $lab;
        }

        $seed = (string) ($item['kode_dosen'] ?? $item['nama_dosen'] ?? uniqid());
        $index = abs((int) crc32($seed)) % count(self::LAB_OPTIONS);

        return self::LAB_OPTIONS[$index];
    }
}
