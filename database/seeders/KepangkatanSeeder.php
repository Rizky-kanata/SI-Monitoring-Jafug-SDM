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
                'nama_dosen' => 'Desita Nur Rachmaniar',
                'jabatan_fungsional' => 'Asisten Ahli',
                'tanggal_tmt' => Carbon::now()->subMonths(8)->format('Y-m-d'),
                'status_publikasi' => 'menunggu_verifikasi',
            ],
            [
                'kode_dosen' => 'GRA',
                'nama_dosen' => 'Granita Hajar',
                'jabatan_fungsional' => 'Lektor',
                'tanggal_tmt' => Carbon::now()->subYears(3)->format('Y-m-d'),
                'status_publikasi' => 'terpublikasi',
            ],
            [
                'kode_dosen' => 'NSX',
                'nama_dosen' => 'Nisa Isrofi',
                'jabatan_fungsional' => 'Lektor Kepala',
                'tanggal_tmt' => null,
                'status_publikasi' => 'belum_diajukan',
            ],
        ];

        foreach ($records as $record) {
            $profil = Profil::where('kode_dosen', $record['kode_dosen'])->first();

            Kepangkatan::updateOrCreate(
                ['kode_dosen' => $record['kode_dosen']],
                [
                    'nama_dosen' => $profil?->nama_dosen ?? $record['nama_dosen'],
                    'jabatan_fungsional' => $record['jabatan_fungsional'],
                    'tanggal_tmt' => $record['tanggal_tmt'],
                    'status_publikasi' => $record['status_publikasi'],
                ]
            );
        }
    }
}
