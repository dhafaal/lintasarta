<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',        // contoh: "Pagi", "Siang", "Malam"
        'start_time',
        'end_time',
    ];

    // Relasi: 1 shift punya banyak schedule
    public function schedules()
    {
        return $this->hasMany(Schedules::class);
    }

    // Relasi: 1 shift bisa dimiliki banyak user lewat schedules
    public function users()
    {
        return $this->belongsToMany(User::class, 'schedules', 'shift_id', 'user_id');
    }
}
