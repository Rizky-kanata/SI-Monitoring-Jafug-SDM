<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Profil extends Model
{
    protected $table = 'profils';
    protected $fillable = [
        'kode_dosen',
        'nama_dosen',
        'prodi',
        'sub_kelompok_keahlian',
        'lab',
        'nip',
        'nidn',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function kepangkatan(): HasOne
    {
        return $this->hasOne(Kepangkatan::class);
    }
}
