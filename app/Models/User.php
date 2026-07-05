<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'station_id',
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
            'role' => UserRole::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isStationMaster(): bool
    {
        return $this->role === UserRole::STATION_MASTER;
    }

    public function isPassenger(): bool
    {
        return $this->role === UserRole::PASSENGER;
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}