<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RememberToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token_hash',
        'device_fingerprint',
        'ip_address',
        'user_agent',
        'expires_at',
        'last_used_at',
        'is_revoked',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'is_revoked' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a secure remember token
     */
    public static function generateToken(int $userId, string $ipAddress, string $userAgent): array
    {
        // Generate a cryptographically secure token
        $token = Str::random(64);
        $tokenHash = Hash::make($token);
        $deviceFingerprint = self::generateDeviceFingerprint($userAgent, $ipAddress);

        // Create token record
        $rememberToken = self::create([
            'user_id' => $userId,
            'token_hash' => $tokenHash,
            'device_fingerprint' => $deviceFingerprint,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'expires_at' => Carbon::now()->addDays(30), // 30 days expiry
            'last_used_at' => Carbon::now(),
            'is_revoked' => false,
        ]);

        return [
            'token' => $token,
            'model' => $rememberToken,
        ];
    }

    /**
     * Validate remember token
     */
    public static function validateToken(string $token, string $ipAddress, string $userAgent): ?User
    {
        $deviceFingerprint = self::generateDeviceFingerprint($userAgent, $ipAddress);
        
        // Find all non-revoked tokens for this device
        $rememberTokens = self::where('is_revoked', false)
            ->where('expires_at', '>', Carbon::now())
            ->where('device_fingerprint', $deviceFingerprint)
            ->get();

        foreach ($rememberTokens as $rememberToken) {
            if (Hash::check($token, $rememberToken->token_hash)) {
                // Update last used
                $rememberToken->update([
                    'last_used_at' => Carbon::now(),
                    'ip_address' => $ipAddress, // Update IP if changed
                ]);

                return $rememberToken->user;
            }
        }

        return null;
    }

    /**
     * Revoke token
     */
    public function revoke(): void
    {
        $this->update(['is_revoked' => true]);
    }

    /**
     * Revoke all tokens for user
     */
    public static function revokeAllForUser(int $userId): void
    {
        self::where('user_id', $userId)->update(['is_revoked' => true]);
    }

    /**
     * Revoke all tokens for device
     */
    public static function revokeAllForDevice(string $deviceFingerprint): void
    {
        self::where('device_fingerprint', $deviceFingerprint)->update(['is_revoked' => true]);
    }

    /**
     * Clean up expired tokens
     */
    public static function cleanupExpired(): int
    {
        return self::where('expires_at', '<', Carbon::now())
            ->orWhere('is_revoked', true)
            ->delete();
    }

    /**
     * Generate device fingerprint
     */
    public static function generateDeviceFingerprint(string $userAgent, string $ipAddress): string
    {
        return hash('sha256', $userAgent . '|' . $ipAddress);
    }

    /**
     * Check if token is valid
     */
    public function isValid(): bool
    {
        return !$this->is_revoked && 
               $this->expires_at->isFuture() &&
               $this->last_used_at->diffInDays(Carbon::now()) <= 7; // Auto-expire if not used for 7 days
    }

    /**
     * Get active tokens for user
     */
    public static function getActiveTokensForUser(int $userId)
    {
        return self::where('user_id', $userId)
            ->where('is_revoked', false)
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('last_used_at', 'desc')
            ->get();
    }
}
