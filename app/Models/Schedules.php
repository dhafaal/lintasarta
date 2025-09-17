<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedules extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'shift_id', 'schedule_date'];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'schedule_id');
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class, 'schedule_id');
    }
    
        public function permissions()
    {
        return $this->hasMany(Permissions::class, 'schedule_id'); // ✅ relasi izin
    }

    // App\Models\Schedules.php
    public function getDurationInMinutesAttribute()
    {
        if (!$this->shift) return 0;

        $start = \Carbon\Carbon::parse($this->shift->start_time);
        $end   = \Carbon\Carbon::parse($this->shift->end_time);

        if ($end->lt($start)) {
            $end->addDay();
        }

        return $start->diffInMinutes($end);
    }
}
