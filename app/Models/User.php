<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi: 1 user punya banyak schedule
    public function schedules()
    {
        return $this->hasMany(Schedules::class);
    }

    // Relasi: 1 user bisa punya banyak shift lewat schedules
    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'schedules', 'user_id', 'shift_id');
    }

    public function attendances()
    {
        return $this->hasMany(\App\Models\Attendance::class);
    }
}
