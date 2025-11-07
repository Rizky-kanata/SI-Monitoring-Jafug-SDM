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

                $tanggalMulai = $status === 'draft'
                    ? null
                    : $now->copy()->subMonths(rand(1, 12))->toDateString();
                $tanggalTmt = $status === 'draft'
                    ? null
                    : $now->copy()->subMonths(rand(1, 18))->toDateString();

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
