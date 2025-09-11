<?php

// app/Models/Permission.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    protected $fillable = ['user_id', 'schedule_id', 'reason', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedules::class, 'schedule_id');
    }
}
