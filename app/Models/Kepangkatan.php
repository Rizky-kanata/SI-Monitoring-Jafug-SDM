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

    public const TMT_STATUS = [
        'belum' => 'Belum TMT',
        'berjalan' => 'Sedang Dalam Masa TMT',
        'terlewat' => 'Terlewat Masa TMT',
    ];

    public const JABATAN_LABELS = [
        'AA' => 'Asisten Ahli',
        'L' => 'Lektor',
        'LK' => 'Lektor Kepala',
        'NJFA' => 'Non-JFA',
    ];

    public const PANGKAT_OPTIONS = [
        'Juru Muda',
        'Juru Muda Tingkat I',
        'Juru',
        'Juru Tingkat I',
        'Pengatur Muda',
        'Pengatur Muda Tingkat I',
        'Pengatur',
        'Pengatur Tingkat I',
        'Penata Muda',
        'Penata Muda Tingkat I',
        'Penata',
        'Penata Tingkat I',
        'Pembina',
        'Pembina Tingkat I',
        'Pembina Utama Muda',
        'Pembina Utama Madya',
        'Pembina Utama',
    ];

    public const GOLONGAN_OPTIONS = [
        'I/a',
        'I/b',
        'I/c',
        'I/d',
        'II/a',
        'II/b',
        'II/c',
        'II/d',
        'III/a',
        'III/b',
        'III/c',
        'III/d',
        'IV/a',
        'IV/b',
        'IV/c',
        'IV/d',
        'IV/e',
    ];

    protected $fillable = [
        'profil_id',
        'jabatan_fungsional',
        'pangkat',
        'golongan',
        'tanggal_sk',
        'tanggal_mulai',
        'tanggal_tmt',
        'is_published',
        'catatan',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_tmt' => 'date',
        'is_published' => 'boolean',
    ];

    public static function jabatanOptions(): array
    {
        return self::JABATAN_LABELS;
    }

    public static function tmtStatusOptions(): array
    {
        return self::TMT_STATUS;
    }

    public static function pangkatOptions(): array
    {
        return array_combine(self::PANGKAT_OPTIONS, self::PANGKAT_OPTIONS);
    }

    public static function golonganOptions(): array
    {
        return array_combine(self::GOLONGAN_OPTIONS, self::GOLONGAN_OPTIONS);
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

    public function getJabatanFungsionalLabelAttribute(): string
    {
        $value = trim((string) ($this->attributes['jabatan_fungsional'] ?? ''));

        if ($value === '') {
            return '-';
        }

        $code = strtoupper($value);

        if (isset(self::JABATAN_LABELS[$code])) {
            return self::JABATAN_LABELS[$code];
        }

        foreach (self::JABATAN_LABELS as $label) {
            if (strcasecmp($label, $value) === 0) {
                return $label;
            }
        }

        return $value;
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
        return $this->tanggal_tmt ?? $this->tanggal_mulai ?? $this->tanggal_sk;
    }
}
