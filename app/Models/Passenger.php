<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    protected $fillable = ['booking_id', 'seat_id', 'name', 'age'];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function seat() { return $this->belongsTo(Seat::class); }
}
