<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'email',
        'status',
        'description',
        'ip_address',
        'user_agent',
        'attempted_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log authentication activity
     */
    public static function log(
        string $action,
        string $status,
        ?string $email = null,
        ?int $userId = null,
        ?string $description = null
    ): void {
        self::create([
            'user_id' => $userId,
            'action' => $action,
            'email' => $email,
            'status' => $status,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'attempted_at' => now(),
        ]);
    }

    /**
     * Get formatted action name
     */
    public function getFormattedActionAttribute(): string
    {
        return match ($this->action) {
            'login' => 'Login',
            'logout' => 'Logout',
            'failed_login' => 'Login Gagal',
            'password_reset' => 'Reset Password',
            default => ucfirst($this->action),
        };
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return match ($this->status) {
            'success' => 'Berhasil',
            'failed' => 'Gagal',
            'blocked' => 'Diblokir',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'success' => 'success',
            'failed' => 'danger',
            'blocked' => 'warning',
            default => 'secondary',
        };
    }
}
