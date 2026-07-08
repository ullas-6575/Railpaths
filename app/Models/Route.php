<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'train_id',
        'route_name',
        'departure_time',
        'arrival_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function train(): BelongsTo
    {
        return $this->belongsTo(Train::class);
    }

    /**
     * Stations ordered by stop_order on pivot table
     */
    public function stations(): BelongsToMany
    {
        return $this->belongsToMany(Station::class, 'route_station')
            ->withPivot(['stop_order', 'arrival_time', 'departure_time', 'distance_from_source'])
            ->orderByPivot('stop_order', 'asc');
    }
}