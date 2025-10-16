<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'foto_path',
    ];

    protected $appends = [
        'foto_url',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto_path ? Storage::url($this->foto_path) : null;
    }
}
