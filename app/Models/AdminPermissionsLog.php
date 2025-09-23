<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AdminPermissionsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'permission_id',
        'target_user_id',
        'target_user_name',
        'permission_type',
        'permission_reason',
        'permission_date',
        'old_status',
        'new_status',
        'additional_data',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'additional_data' => 'array',
        'permission_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permissions::class, 'permission_id');
    }

    /**
     * Log admin permission activity
     */
    public static function log(
        string $action,
        ?int $permissionId = null,
        ?int $targetUserId = null,
        ?string $targetUserName = null,
        ?string $permissionType = null,
        ?string $permissionReason = null,
        ?string $permissionDate = null,
        ?string $oldStatus = null,
        ?string $newStatus = null,
        ?array $additionalData = null,
        ?string $description = null
    ): void {
        self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'permission_id' => $permissionId,
            'target_user_id' => $targetUserId,
            'target_user_name' => $targetUserName,
            'permission_type' => $permissionType,
            'permission_reason' => $permissionReason,
            'permission_date' => $permissionDate,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'additional_data' => $additionalData,
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
            'approve' => 'Menyetujui',
            'reject' => 'Menolak',
            default => ucfirst($this->action),
        };
    }

    /**
     * Get action color for UI
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'approve' => 'success',
            'reject' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get permission type color
     */
    public function getPermissionTypeColorAttribute(): string
    {
        return match ($this->permission_type) {
            'izin' => 'primary',
            'sakit' => 'warning',
            'cuti' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->new_status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'pending' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get formatted permission date
     */
    public function getFormattedPermissionDateAttribute(): string
    {
        return $this->permission_date ? $this->permission_date->format('d M Y') : '-';
    }
}
