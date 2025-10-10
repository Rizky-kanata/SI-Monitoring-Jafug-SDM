<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    protected $table = 'profils';
    protected $fillable = [
        'kode_dosen',
        'nama_dosen',
        'prodi',
        'kelompok_keahlian',
        'sub_kelompok_keahlian',
        'nip',
        'nidn',
    ];
}
