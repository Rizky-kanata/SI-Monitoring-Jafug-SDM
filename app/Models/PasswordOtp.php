<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordOtp extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'code',
        'expires_at',
        'attempts',
        'max_attempts',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at instanceof CarbonInterface
            ? $this->expires_at->isPast()
            : true;
    }

    public function hasAttemptsRemaining(): bool
    {
        return $this->attempts < $this->max_attempts;
    }
}
