<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminUsersLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'target_user_id',
        'target_user_name',
        'target_user_email',
        'target_user_role',
        'old_values',
        'new_values',
        'password_changed',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'password_changed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /**
     * Log admin user activity
     */
    public static function log(
        string $action,
        ?int $targetUserId = null,
        ?string $targetUserName = null,
        ?string $targetUserEmail = null,
        ?string $targetUserRole = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        bool $passwordChanged = false,
        ?string $description = null
    ): void {
        self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'target_user_id' => $targetUserId,
            'target_user_name' => $targetUserName,
            'target_user_email' => $targetUserEmail,
            'target_user_role' => $targetUserRole,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'password_changed' => $passwordChanged,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get formatted action name
     */
    public function getFormattedActionAttribute(): string
    {
        return match ($this->action) {
            'create' => 'Membuat',
            'update' => 'Mengubah',
            'delete' => 'Menghapus',
            default => ucfirst($this->action),
        };
    }

    /**
     * Get action color for UI
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'create' => 'success',
            'update' => 'warning',
            'delete' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get role badge color
     */
    public function getRoleColorAttribute(): string
    {
        return match ($this->target_user_role) {
            'admin' => 'danger',
            'operator' => 'warning',
            'user' => 'primary',
            default => 'secondary',
        };
    }
}
