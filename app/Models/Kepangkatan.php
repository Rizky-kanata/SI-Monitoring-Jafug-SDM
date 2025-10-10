<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kepangkatan extends Model
{
    use HasFactory;

    protected $table = 'kepangkatan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_dosen',
        'jabatan_fungsional',
    ];
}
