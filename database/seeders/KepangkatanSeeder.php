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
        $metaMap = $this->loadKepangkatanMeta();

        Profil::query()
            ->orderBy('nama_dosen')
            ->get()
            ->each(function (Profil $profil) use ($now, $metaMap) {
                $meta = $metaMap[$profil->kode_dosen] ?? [];
                $jabatan = $this->resolveJabatan($profil, $meta['jabatan'] ?? null);
                $pangkatGolongan = $this->mapJabatanToPangkat($jabatan);
                $tanggalSk = $this->parseTanggal($meta['tanggal_tmt'] ?? null);
                $tanggalMulai = $tanggalSk;
                $tanggalTmt = $tanggalSk;
                $statusPublikasi = strtolower(trim((string) ($meta['status_publikasi'] ?? '')));
                $isPublished = $statusPublikasi === 'sudah';
                $catatan = $isPublished ? 'Publikasi sudah diurus.' : 'Publikasi belum diurus.';

                Kepangkatan::updateOrCreate(
                    ['profil_id' => $profil->id],
                    [
                        'jabatan_fungsional' => $jabatan,
                        'pangkat' => $pangkatGolongan['pangkat'],
                        'golongan' => $pangkatGolongan['golongan'],
                        'tanggal_sk' => $tanggalSk,
                        'tanggal_mulai' => $tanggalMulai,
                        'tanggal_tmt' => $tanggalTmt,
                        'is_published' => $isPublished,
                        'catatan' => $catatan,
                    ]
                );
            });
    }

    private function resolveJabatan(Profil $profil, ?string $jabatanFromMeta = null): string
    {
        $raw = strtoupper(trim((string) ($jabatanFromMeta ?? $profil->jabatan_fungsional ?? ''))); // @phpstan-ignore-line

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
     * @return array<string, array{jabatan: string, tanggal_tmt: string|null, status_publikasi: string|null}>
     */
    private function loadKepangkatanMeta(): array
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
                $item['kode_dosen'] => [
                    'jabatan' => strtoupper(trim((string) ($item['jabatan_fungsional'] ?? ''))),
                    'tanggal_tmt' => $item['tanggal_tmt'] ?? null,
                    'status_publikasi' => $item['status_publikasi'] ?? null,
                ],
            ])
            ->all();
    }

    private function parseTanggal(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

}
