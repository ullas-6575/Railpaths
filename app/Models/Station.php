<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Station extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'city', 'state'];

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'route_station')
            ->withPivot(['stop_order', 'arrival_time', 'departure_time', 'distance_from_source']);
    }
}