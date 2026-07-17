<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'route_id', 'source_station_id', 'dest_station_id', 'travel_date',
        'class_type', 'seat_count', 'status', 'booked_at', 'cancelled_at', 'refund_amount',
    ];

    protected $casts = ['travel_date' => 'date', 'booked_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function train() { return $this->hasOneThrough(Train::class, Route::class, 'id', 'id', 'route_id', 'train_id'); }
    public function route() { return $this->belongsTo(Route::class); }
    public function passengers() { return $this->hasMany(Passenger::class); }
    public function seats() { return $this->belongsToMany(Seat::class, 'booking_seat'); }
}
