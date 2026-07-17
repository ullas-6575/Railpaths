<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'station_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a station master.
     */
    public function isStationMaster(): bool
    {
        return $this->role === 'station_master';
    }

 
    public function isPassenger(): bool
    {
        return $this->role === 'passenger';
    }
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function assignedStation(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'station_id');
    }

   
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Bookings made by this passenger.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Station logs created by this station master.
     */
    public function stationLogs(): HasMany
    {
        return $this->hasMany(StationLog::class, 'station_master_id');
    }
}