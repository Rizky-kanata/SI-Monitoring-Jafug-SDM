<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table = 'dosens';
    protected $fillable = [
        'kode_dosen',
        'nama_dosen',
        'prodi',
        'kelompok_keahlian',
        'jabatan_fungsional',
        'sub_kelompok_keahlian',
    ];
}
