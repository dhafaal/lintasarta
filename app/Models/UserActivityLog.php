<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'resource_type',
        'resource_id',
        'resource_name',
        'additional_data',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'additional_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log user activity
     */
    public static function log(
        string $action,
        ?string $resourceType = null,
        ?int $resourceId = null,
        ?string $resourceName = null,
        ?array $additionalData = null,
        ?string $description = null
    ): void {
        self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'resource_name' => $resourceName,
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
            'checkin' => 'Check In',
            'checkout' => 'Check Out',
            'request_permission' => 'Mengajukan Izin',
            'absent' => 'Menandai Alpha',
            default => ucfirst($this->action),
        };
    }

    /**
     * Get formatted resource type
     */
    public function getFormattedResourceTypeAttribute(): string
    {
        return match ($this->resource_type) {
            'attendances' => 'Kehadiran',
            'permissions' => 'Izin',
            default => ucfirst($this->resource_type ?? ''),
        };
    }
}
