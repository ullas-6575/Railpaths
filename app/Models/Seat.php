<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = ['train_id', 'seat_number'];

    public function train() { return $this->belongsTo(Train::class); }
    public function bookings() { return $this->belongsToMany(Booking::class, 'booking_seat'); }
}
