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

        Profil::query()
            ->orderBy('nama_dosen')
            ->get()
            ->each(function (Profil $profil, int $index) use ($now, $statusCycle) {
                $status = $statusCycle[$index % count($statusCycle)];
                $jabatan = $this->resolveJabatan($profil);
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

    private function resolveJabatan(Profil $profil): string
    {
        $default = 'Asisten Ahli';
        $mapping = [
            'AA' => 'Asisten Ahli',
            'L' => 'Lektor',
            'LK' => 'Lektor Kepala',
            'NJFA' => 'Non-JFA',
        ];

        $jabatan = strtoupper(trim((string) ($profil->jabatan_fungsional ?? '')));

        return $mapping[$jabatan] ?? $default;
    }

    /**
     * @return array{pangkat: string|null, golongan: string|null}
     */
    private function mapJabatanToPangkat(string $jabatan): array
    {
        return match ($jabatan) {
            'Lektor Kepala' => ['pangkat' => 'IV/a', 'golongan' => 'Pembina'],
            'Lektor' => ['pangkat' => 'III/c', 'golongan' => 'Penata'],
            'Asisten Ahli' => ['pangkat' => 'III/b', 'golongan' => 'Penata Muda Tk. I'],
            default => ['pangkat' => null, 'golongan' => null],
        };
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
