<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'schedule_id',
        'status',
        'check_in_time',
        'check_out_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedules::class);
    }

    public function permission()
    {
        return $this->hasOne(Permissions::class);
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
