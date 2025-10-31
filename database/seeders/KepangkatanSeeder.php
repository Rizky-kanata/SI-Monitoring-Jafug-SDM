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
        $records = [
            [
                'kode_dosen' => 'DEZ',
                'jabatan_fungsional' => 'Asisten Ahli',
                'pangkat' => 'III/b',
                'golongan' => 'Penata Muda Tk. I',
                'tanggal_sk' => Carbon::now()->subMonths(9)->toDateString(),
                'tanggal_mulai' => Carbon::now()->subMonths(8)->toDateString(),
                'status' => 'diajukan',
                'catatan' => 'Menunggu verifikasi dari fakultas.',
            ],
            [
                'kode_dosen' => 'GRA',
                'jabatan_fungsional' => 'Lektor',
                'pangkat' => 'III/d',
                'golongan' => 'Penata Tk. I',
                'tanggal_sk' => Carbon::now()->subYears(2)->subMonths(3)->toDateString(),
                'tanggal_mulai' => Carbon::now()->subYears(2)->toDateString(),
                'status' => 'disetujui',
                'catatan' => 'Telah disahkan dengan SK terbaru.',
            ],
            [
                'kode_dosen' => 'NSX',
                'jabatan_fungsional' => 'Lektor Kepala',
                'pangkat' => 'IV/a',
                'golongan' => 'Pembina',
                'tanggal_sk' => null,
                'tanggal_mulai' => null,
                'status' => 'draft',
                'catatan' => 'Monitoring kelengkapan berkas.',
            ],
            [
                'kode_dosen' => 'EXE',
                'jabatan_fungsional' => 'Lektor Kepala',
                'pangkat' => 'IV/b',
                'golongan' => 'Pembina Tk. I',
                'tanggal_sk' => Carbon::now()->subYear()->toDateString(),
                'tanggal_mulai' => Carbon::now()->subMonths(10)->toDateString(),
                'status' => 'ditolak',
                'catatan' => 'Perlu revisi pada lampiran penelitian.',
            ],
        ];

        foreach ($records as $record) {
            $profil = Profil::where('kode_dosen', $record['kode_dosen'])->first();

            if (! $profil) {
                continue;
            }

            Kepangkatan::updateOrCreate(
                ['profil_id' => $profil->id],
                [
                    'jabatan_fungsional' => $record['jabatan_fungsional'],
                    'pangkat' => $record['pangkat'],
                    'golongan' => $record['golongan'],
                    'tanggal_sk' => $record['tanggal_sk'],
                    'tanggal_mulai' => $record['tanggal_mulai'],
                    'status' => $record['status'],
                    'catatan' => $record['catatan'],
                ]
            );
        }
    }
}
