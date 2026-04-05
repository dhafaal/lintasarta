<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleSwapRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'requested_schedule_id',
        'target_user_id',
        'target_schedule_id',
        'reason',
        'status',
        'admin_id',
        'admin_note',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function requestedSchedule()
    {
        return $this->belongsTo(Schedules::class, 'requested_schedule_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function targetSchedule()
    {
        return $this->belongsTo(Schedules::class, 'target_schedule_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
