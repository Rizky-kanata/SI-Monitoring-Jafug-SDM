<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Kepangkatan extends Model
{
    use HasFactory;

    /**
     * Metadata status legacy (masih digunakan di modul lain).
     *
     * @var array<string, array<string, string>>
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

    public const TMT_STATUS = [
        'belum' => 'Belum TMT',
        'berjalan' => 'Sedang Dalam Masa TMT',
        'terlewat' => 'Terlewat Masa TMT',
    ];

    protected $fillable = [
        'profil_id',
        'jabatan_fungsional',
        'pangkat',
        'golongan',
        'tanggal_sk',
        'tanggal_mulai',
        'tanggal_tmt',
        'status',
        'is_published',
        'catatan',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_tmt' => 'date',
        'is_published' => 'boolean',
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

    public static function tmtStatusOptions(): array
    {
        return self::TMT_STATUS;
    }

    public function profil(): BelongsTo
    {
        return $this->belongsTo(Profil::class);
    }

    public function scopeWhereTmtStatus(Builder $query, string $status): void
    {
        $status = strtolower($status);
        $now = now();
        $twoYearsAgo = $now->copy()->subYears(2);

        if ($status === 'terlewat') {
            $query
                ->whereNotNull('tanggal_tmt')
                ->whereDate('tanggal_tmt', '<', $twoYearsAgo);

            return;
        }

        if ($status === 'berjalan') {
            $query
                ->whereNotNull('tanggal_tmt')
                ->whereDate('tanggal_tmt', '<=', $now)
                ->whereDate('tanggal_tmt', '>=', $twoYearsAgo);

            return;
        }

        $query->where(function ($builder) use ($now) {
            $builder
                ->whereNull('tanggal_tmt')
                ->orWhereDate('tanggal_tmt', '>', $now);
        });
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

    public function getTanggalTmtDisplayAttribute(): string
    {
        $date = $this->effectiveTmtDate();

        return $date ? $date->format('d/m/Y') : '-';
    }

    public function getTmtStatusKeyAttribute(): string
    {
        $date = $this->effectiveTmtDate();

        if (! $date || now()->lt($date)) {
            return 'belum';
        }

        $end = $date->copy()->addYears(2);

        return now()->lte($end) ? 'berjalan' : 'terlewat';
    }

    public function getStatusTmtLabelAttribute(): string
    {
        return self::TMT_STATUS[$this->tmt_status_key] ?? self::TMT_STATUS['belum'];
    }

    public function getStatusPublikasiLabelAttribute(): string
    {
        return $this->is_published ? 'Sudah' : 'Belum';
    }

    public function getIndicatorColorAttribute(): string
    {
        return match ($this->tmt_status_key) {
            'terlewat' => $this->is_published ? 'yellow' : 'red',
            'berjalan' => $this->is_published ? 'green' : 'yellow',
            default => $this->is_published ? 'green' : 'yellow',
        };
    }

    public function getIndicatorLabelAttribute(): string
    {
        return match ($this->indicator_color) {
            'green' => 'Hijau',
            'yellow' => 'Kuning',
            'red' => 'Merah',
            default => 'Abu-abu',
        };
    }

    public function getIndicatorClassesAttribute(): string
    {
        return match ($this->indicator_color) {
            'green' => 'bg-emerald-500',
            'yellow' => 'bg-amber-400',
            'red' => 'bg-rose-500',
            default => 'bg-slate-400',
        };
    }

    public function getKeteranganAttribute(): string
    {
        $tmtDescription = match ($this->tmt_status_key) {
            'terlewat' => 'Sudah terlewat masa TMT',
            'berjalan' => 'Sedang dalam masa TMT',
            default => 'Belum memasuki masa TMT',
        };

        $publikasiDescription = $this->is_published
            ? 'sudah dipublikasikan'
            : 'belum mengurus publikasi';

        return sprintf('%s dan %s.', $tmtDescription, ucfirst($publikasiDescription));
    }

    private function effectiveTmtDate(): ?Carbon
    {
        return $this->tanggal_tmt ?? $this->tanggal_mulai;
    }
}
