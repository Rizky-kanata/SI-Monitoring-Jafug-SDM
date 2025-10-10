<?php

namespace Database\Seeders;

use App\Models\Kepangkatan;
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
                'nama_dosen' => 'Dr. Andi Wijaya',
                'jabatan_fungsional' => 'Guru Besar',
            ],
            [
                'nama_dosen' => 'Dr. Siti Rahmawati',
                'jabatan_fungsional' => 'Lektor Kepala',
            ],
            [
                'nama_dosen' => 'M. Fajar Pratama, M.Kom.',
                'jabatan_fungsional' => 'Lektor',
            ],
        ];

        foreach ($records as $record) {
            Kepangkatan::updateOrCreate(
                ['nama_dosen' => $record['nama_dosen']],
                ['jabatan_fungsional' => $record['jabatan_fungsional']]
            );
        }
    }
}
