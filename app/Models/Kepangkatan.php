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
        'draft' => [
            'label' => 'Draft',
            'description' => 'Data kepangkatan masih dalam proses pengumpulan berkas.',
            'badge' => 'bg-slate-100 text-slate-700 ring-slate-200',
        ],
        'diajukan' => [
            'label' => 'Diajukan',
            'description' => 'Pengajuan sudah dikirim dan menunggu evaluasi.',
            'badge' => 'bg-amber-100 text-amber-800 ring-amber-200',
        ],
        'disetujui' => [
            'label' => 'Disetujui',
            'description' => 'Kenaikan pangkat telah disetujui dan dinyatakan sah.',
            'badge' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
        ],
        'ditolak' => [
            'label' => 'Perlu Revisi',
            'description' => 'Pengajuan ditolak, mohon cek catatan revisi.',
            'badge' => 'bg-rose-100 text-rose-800 ring-rose-200',
        ],
    ];

    protected $fillable = [
        'profil_id',
        'jabatan_fungsional',
        'pangkat',
        'golongan',
        'tanggal_sk',
        'tanggal_mulai',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'tanggal_mulai' => 'date',
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
        return $this->belongsTo(Profil::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_METADATA[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function getStatusDescriptionAttribute(): string
    {
        return self::STATUS_METADATA[$this->status]['description'] ?? '';
    }

    public function getStatusBadgeAttribute(): string
    {
        return self::STATUS_METADATA[$this->status]['badge'] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
    }
}
