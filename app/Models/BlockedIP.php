<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BlockedIP extends Model
{
    use HasFactory;

    protected $table = 'blocked_ips';

    protected $fillable = [
        'ip_address',
        'reason',
        'failed_attempts',
        'blocked_at',
        'blocked_until',
        'is_permanent',
        'blocked_by',
    ];

    protected $casts = [
        'blocked_at' => 'datetime',
        'blocked_until' => 'datetime',
        'is_permanent' => 'boolean',
    ];

    /**
     * Block an IP address
     */
    public static function blockIP(string $ipAddress, string $reason, int $failedAttempts = 0, ?int $blockMinutes = null, bool $isPermanent = false, ?string $blockedBy = null): self
    {
        $blockedUntil = $isPermanent ? null : ($blockMinutes ? now()->addMinutes($blockMinutes) : now()->addHours(24));

        return self::updateOrCreate(
            ['ip_address' => $ipAddress],
            [
                'reason' => $reason,
                'failed_attempts' => $failedAttempts,
                'blocked_at' => now(),
                'blocked_until' => $blockedUntil,
                'is_permanent' => $isPermanent,
                'blocked_by' => $blockedBy,
            ]
        );
    }

    /**
     * Check if IP is currently blocked
     */
    public static function isBlocked(string $ipAddress): bool
    {
        $blockedIP = self::where('ip_address', $ipAddress)->first();

        if (!$blockedIP) {
            return false;
        }

        // Permanent block
        if ($blockedIP->is_permanent) {
            return true;
        }

        // Temporary block - check if still valid
        if ($blockedIP->blocked_until && Carbon::now()->lt($blockedIP->blocked_until)) {
            return true;
        }

        // Block has expired, remove it
        if ($blockedIP->blocked_until && Carbon::now()->gte($blockedIP->blocked_until)) {
            $blockedIP->delete();
            return false;
        }

        return false;
    }

    /**
     * Unblock an IP address
     */
    public static function unblockIP(string $ipAddress): bool
    {
        return self::where('ip_address', $ipAddress)->delete() > 0;
    }

    /**
     * Get blocked IP info
     */
    public static function getBlockInfo(string $ipAddress): ?self
    {
        return self::where('ip_address', $ipAddress)->first();
    }

    /**
     * Auto-block IP based on failed attempts
     */
    public static function autoBlockIP(string $ipAddress, int $failedAttempts): ?self
    {
        // Block for 1 hour after 10 failed attempts
        if ($failedAttempts >= 10 && $failedAttempts < 20) {
            return self::blockIP($ipAddress, 'Auto-blocked: Too many failed login attempts', $failedAttempts, 60);
        }
        
        // Block for 24 hours after 20 failed attempts
        if ($failedAttempts >= 20 && $failedAttempts < 50) {
            return self::blockIP($ipAddress, 'Auto-blocked: Excessive failed login attempts', $failedAttempts, 1440);
        }
        
        // Permanent block after 50 failed attempts
        if ($failedAttempts >= 50) {
            return self::blockIP($ipAddress, 'Auto-blocked: Suspected brute force attack', $failedAttempts, null, true);
        }

        return null;
    }

    /**
     * Get time remaining for temporary block
     */
    public function getTimeRemaining(): ?int
    {
        if ($this->is_permanent || !$this->blocked_until) {
            return null;
        }

        $now = Carbon::now();
        if ($now->lt($this->blocked_until)) {
            return $now->diffInMinutes($this->blocked_until);
        }

        return null;
    }
}
