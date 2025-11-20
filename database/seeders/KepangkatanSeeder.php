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
        $statusCycle = ['draft', 'diajukan', 'disetujui', 'ditolak'];
        $jabatanMap = $this->loadJabatanCodes();

        Profil::query()
            ->orderBy('nama_dosen')
            ->get()
            ->each(function (Profil $profil, int $index) use ($now, $statusCycle, $jabatanMap) {
                $status = $statusCycle[$index % count($statusCycle)];
                $jabatan = $this->resolveJabatan($profil, $jabatanMap);
                $pangkatGolongan = $this->mapJabatanToPangkat($jabatan);

                $tanggalMulai = null;
                $tanggalTmt = null;

                if ($status !== 'draft') {
                    $scenario = $index % 3;

                    if ($scenario === 0) {
                        $futureStart = $now->copy()->addMonths(rand(1, 6));
                        $tanggalMulai = $futureStart->toDateString();
                        $tanggalTmt = $futureStart->toDateString();
                    } elseif ($scenario === 1) {
                        $activeStart = $now->copy()->subMonths(rand(1, 18));
                        $tanggalMulai = $activeStart->toDateString();
                        $tanggalTmt = $activeStart->toDateString();
                    } else {
                        $pastStart = $now->copy()->subMonths(rand(30, 48));
                        $tanggalMulai = $pastStart->toDateString();
                        $tanggalTmt = $pastStart->toDateString();
                    }
                }

                Kepangkatan::updateOrCreate(
                    ['profil_id' => $profil->id],
                    [
                        'jabatan_fungsional' => $jabatan,
                        'pangkat' => $pangkatGolongan['pangkat'],
                        'golongan' => $pangkatGolongan['golongan'],
                        'tanggal_sk' => $status === 'draft' ? null : $now->copy()->subMonths(rand(3, 18))->toDateString(),
                        'tanggal_mulai' => $tanggalMulai,
                        'tanggal_tmt' => $tanggalTmt,
                        'status' => $status,
                        'is_published' => in_array($status, ['diajukan', 'disetujui'], true),
                        'catatan' => $this->generateCatatan($status),
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

    private function generateCatatan(string $status): string
    {
        return match ($status) {
            'draft' => 'Lengkapi berkas dan susun draft pengajuan kenaikan pangkat.',
            'diajukan' => 'Berkas telah diajukan ke fakultas, menunggu tindak lanjut.',
            'disetujui' => 'Kenaikan pangkat sudah disahkan. Update data pendukung bila perlu.',
            'ditolak' => 'Pengajuan ditolak. Cek catatan evaluasi dan siapkan revisi.',
            default => '',
        };
    }
}
