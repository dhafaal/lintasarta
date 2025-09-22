<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'is_trusted_device',
        'last_activity',
        'expires_at',
    ];

    protected $casts = [
        'is_trusted_device' => 'boolean',
        'last_activity' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create or update user session
     */
    public static function createOrUpdate(int $userId, string $sessionId, string $ipAddress, string $userAgent, ?string $deviceFingerprint = null): self
    {
        $isTrustedDevice = self::isTrustedDevice($userId, $ipAddress, $userAgent);

        return self::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'device_fingerprint' => $deviceFingerprint,
                'is_trusted_device' => $isTrustedDevice,
                'last_activity' => now(),
                'expires_at' => now()->addHours(24),
            ]
        );
    }

    /**
     * Check if device is trusted
     */
    public static function isTrustedDevice(int $userId, string $ipAddress, string $userAgent): bool
    {
        return self::where('user_id', $userId)
            ->where('ip_address', $ipAddress)
            ->where('user_agent', $userAgent)
            ->where('is_trusted_device', true)
            ->where('last_activity', '>=', Carbon::now()->subDays(30))
            ->exists();
    }

    /**
     * Mark device as trusted
     */
    public function markAsTrusted(): void
    {
        $this->update(['is_trusted_device' => true]);
    }

    /**
     * Check for suspicious activity
     */
    public static function checkSuspiciousActivity(int $userId): array
    {
        $suspiciousActivities = [];

        // Check for multiple concurrent sessions
        $activeSessions = self::where('user_id', $userId)
            ->where('last_activity', '>=', Carbon::now()->subMinutes(30))
            ->count();

        if ($activeSessions > 3) {
            $suspiciousActivities[] = 'Multiple concurrent sessions detected';
        }

        // Check for login from new locations
        $recentSessions = self::where('user_id', $userId)
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->get();

        $uniqueIPs = $recentSessions->pluck('ip_address')->unique();
        if ($uniqueIPs->count() > 5) {
            $suspiciousActivities[] = 'Login from multiple IP addresses';
        }

        // Check for untrusted devices
        $untrustedSessions = $recentSessions->where('is_trusted_device', false);
        if ($untrustedSessions->count() > 0) {
            $suspiciousActivities[] = 'Login from untrusted device(s)';
        }

        return $suspiciousActivities;
    }

    /**
     * Clean expired sessions
     */
    public static function cleanExpiredSessions(): int
    {
        return self::where('expires_at', '<', now())->delete();
    }

    /**
     * Terminate user sessions
     */
    public static function terminateUserSessions(int $userId, ?string $exceptSessionId = null): int
    {
        $query = self::where('user_id', $userId);
        
        if ($exceptSessionId) {
            $query->where('session_id', '!=', $exceptSessionId);
        }

        return $query->delete();
    }

    /**
     * Get active sessions for user
     */
    public static function getActiveSessions(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('user_id', $userId)
            ->where('last_activity', '>=', Carbon::now()->subMinutes(30))
            ->orderBy('last_activity', 'desc')
            ->get();
    }

    /**
     * Update last activity
     */
    public function updateActivity(): void
    {
        $this->update(['last_activity' => now()]);
    }

    /**
     * Generate device fingerprint
     */
    public static function generateDeviceFingerprint(string $userAgent, string $ipAddress): string
    {
        return hash('sha256', $userAgent . '|' . $ipAddress . '|' . config('app.key'));
    }
}
