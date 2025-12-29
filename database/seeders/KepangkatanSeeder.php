<?php

namespace Database\Seeders;

use App\Models\Kepangkatan;
use App\Models\Profil;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class KepangkatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $jabatanMap = $this->loadJabatanCodes();

        Profil::query()
            ->orderBy('nama_dosen')
            ->get()
            ->each(function (Profil $profil, int $index) use ($now, $jabatanMap) {
                $jabatan = $this->resolveJabatan($profil, $jabatanMap);
                $pangkatGolongan = $this->mapJabatanToPangkat($jabatan);
                $scenario = $index % 3;

                if ($scenario === 0) {
                    $tanggalSk = $now->copy()->addMonths(rand(1, 9))->toDateString();
                    $isPublished = false;
                    $catatan = 'Menunggu periode TMT dimulai.';
                } elseif ($scenario === 1) {
                    $tanggalSk = $now->copy()->subMonths(rand(1, 18))->toDateString();
                    $isPublished = $index % 2 === 0;
                    $catatan = $isPublished ? 'Publikasi sudah diurus.' : 'Publikasi belum diurus.';
                } else {
                    $tanggalSk = $now->copy()->subMonths(rand(30, 60))->toDateString();
                    $isPublished = $index % 3 === 0;
                    $catatan = $isPublished ? 'Publikasi sudah diurus.' : 'Publikasi belum diurus.';
                }

                Kepangkatan::updateOrCreate(
                    ['profil_id' => $profil->id],
                    [
                        'jabatan_fungsional' => $jabatan,
                        'pangkat' => $pangkatGolongan['pangkat'],
                        'golongan' => $pangkatGolongan['golongan'],
                        'tanggal_sk' => $tanggalSk,
                        'tanggal_mulai' => $tanggalSk,
                        'tanggal_tmt' => $tanggalSk,
                        'is_published' => $isPublished,
                        'catatan' => $catatan,
                    ]
                );
            });
    }

    private function resolveJabatan(Profil $profil, array $jabatanMap): string
    {
        $raw = strtoupper(trim((string) ($jabatanMap[$profil->kode_dosen] ?? $profil->jabatan_fungsional ?? ''))); // @phpstan-ignore-line

        return match ($raw) {
            'AA', 'ASISTEN AHLI' => 'AA',
            'L', 'LEKTOR' => 'L',
            'LK', 'LEKTOR KEPALA' => 'LK',
            'NJFA', 'NON-JFA', 'NON JFA' => 'NJFA',
            default => 'NJFA',
        };
    }

    /**
     * @return array{pangkat: string|null, golongan: string|null}
     */
    private function mapJabatanToPangkat(string $jabatan): array
    {
        return match ($jabatan) {
            'LK' => ['pangkat' => 'IV/a', 'golongan' => 'Pembina'],
            'L' => ['pangkat' => 'III/c', 'golongan' => 'Penata'],
            'AA' => ['pangkat' => 'III/b', 'golongan' => 'Penata Muda Tk. I'],
            default => ['pangkat' => null, 'golongan' => null],
        };
    }

    /**
     * @return array<string, string>
     */
    private function loadJabatanCodes(): array
    {
        $path = base_path('database/data/dosen_riib.json');
        $content = file_get_contents($path);

        if ($content === false) {
            return [];
        }

        $json = preg_replace('/^\xEF\xBB\xBF/', '', $content ?? '');
        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            return [];
        }

        return collect($decoded)
            ->mapWithKeys(fn ($item) => [
                $item['kode_dosen'] => strtoupper(trim((string) ($item['jabatan_fungsional'] ?? ''))),
            ])
            ->all();
    }

}
