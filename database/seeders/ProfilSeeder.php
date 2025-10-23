<?php

namespace Database\Seeders;

use App\Models\Profil;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(base_path('database/data/dosen_riib.json'));
        $data = json_decode($json, true);

        foreach ($data as $item) {
            Profil::create([
                'kode_dosen' => $item['kode_dosen'],
                'nama_dosen' => $item['nama_dosen'],
                'prodi' => $item['prodi'],
                'kelompok_keahlian' => $item['kelompok_keahlian'],
                'sub_kelompok_keahlian' => $item['sub_kelompok_keahlian'],
                'nip' => $this->normalizeIdentifier($item['nip'] ?? null),
                'nidn' => $this->normalizeIdentifier($item['nidn'] ?? null),
                'foto_path' => $this->resolveFotoPath($item['foto'] ?? null),
            ]);
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
}
