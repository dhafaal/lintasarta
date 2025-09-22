<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminSchedulesLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'schedule_id',
        'target_user_id',
        'target_user_name',
        'shift_id',
        'shift_name',
        'schedule_date',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'schedule_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedules::class, 'schedule_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Log admin schedule activity
     */
    public static function log(
        string $action,
        ?int $scheduleId = null,
        ?int $targetUserId = null,
        ?string $targetUserName = null,
        ?int $shiftId = null,
        ?string $shiftName = null,
        ?string $scheduleDate = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): void {
        self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'schedule_id' => $scheduleId,
            'target_user_id' => $targetUserId,
            'target_user_name' => $targetUserName,
            'shift_id' => $shiftId,
            'shift_name' => $shiftName,
            'schedule_date' => $scheduleDate,
            'old_values' => $oldValues,
            'new_values' => $newValues,
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
     * Get formatted schedule date
     */
    public function getFormattedScheduleDateAttribute(): string
    {
        return $this->schedule_date ? $this->schedule_date->format('d M Y') : '-';
    }
}
