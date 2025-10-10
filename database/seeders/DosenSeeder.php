<?php

namespace Database\Seeders;

use App\Models\Dosen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(base_path('\database\data\dosen_riib.json'));
        $data = json_decode($json, true);

        foreach ($data as $item) {
            Dosen::create([
                'kode_dosen' => $item['kode_dosen'],
                'nama_dosen' => $item['nama_dosen'],
                'prodi' => $item['prodi'],
                'kelompok_keahlian' => $item['kelompok_keahlian'],
                'jabatan_fungsional' => $item['jabatan_fungsional'],
                'sub_kelompok_keahlian' => $item['sub_kelompok_keahlian'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
