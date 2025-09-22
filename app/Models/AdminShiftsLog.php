<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminShiftsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'shift_id',
        'shift_name',
        'shift_category',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Log admin shift activity
     */
    public static function log(
        string $action,
        ?int $shiftId = null,
        ?string $shiftName = null,
        ?string $shiftCategory = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): void {
        self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'shift_id' => $shiftId,
            'shift_name' => $shiftName,
            'shift_category' => $shiftCategory,
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
}
