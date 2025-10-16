<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kepangkatan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public const STATUS_METADATA = [
        'belum_diajukan' => [
            'label' => 'Belum Diajukan',
            'description' => 'Dokumen kepangkatan belum diajukan untuk proses publikasi.',
            'badge' => 'bg-slate-100 text-slate-700 ring-slate-200',
        ],
        'menunggu_verifikasi' => [
            'label' => 'Menunggu Verifikasi',
            'description' => 'Pengajuan sudah dikirim dan menunggu verifikasi operator.',
            'badge' => 'bg-amber-100 text-amber-700 ring-amber-200',
        ],
        'perlu_revisi' => [
            'label' => 'Perlu Revisi',
            'description' => 'Hasil review meminta revisi sebelum publikasi.',
            'badge' => 'bg-rose-100 text-rose-700 ring-rose-200',
        ],
        'siap_publikasi' => [
            'label' => 'Siap Publikasi',
            'description' => 'Semua persyaratan terpenuhi dan siap dipublikasikan.',
            'badge' => 'bg-sky-100 text-sky-700 ring-sky-200',
        ],
        'terpublikasi' => [
            'label' => 'Terpublikasi',
            'description' => 'Data kepangkatan telah terpublikasi secara resmi.',
            'badge' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
        ],
    ];

    protected $fillable = [
        'nama_dosen',
        'kode_dosen',
        'jabatan_fungsional',
        'tanggal_tmt',
        'status_publikasi',
    ];

    protected $casts = [
        'tanggal_tmt' => 'date',
    ];

    public static function statusOptions(): array
    {
        return array_combine(
            array_keys(self::STATUS_METADATA),
            array_column(self::STATUS_METADATA, 'label')
        );
    }

    public static function statusMetadata(): array
    {
        return self::STATUS_METADATA;
    }

    public function profil(): BelongsTo
    {
        return $this->belongsTo(Profil::class, 'kode_dosen', 'kode_dosen');
    }
}
