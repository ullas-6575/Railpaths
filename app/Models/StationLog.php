<?php
// app/Models/StationLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_master_id',
        'station_id',
        'train_id',
        'schedule_id',
        'scheduled_arrival',
        'actual_arrival',
        'scheduled_departure',
        'actual_departure',
        'delay_minutes',
        'remarks',
        'status',
    ];

    protected $casts = [
        'scheduled_arrival' => 'datetime:H:i',
        'actual_arrival' => 'datetime:H:i',
        'scheduled_departure' => 'datetime:H:i',
        'actual_departure' => 'datetime:H:i',
    ];

    public function stationMaster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'station_master_id');
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function train(): BelongsTo
    {
        return $this->belongsTo(Train::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}