<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Station extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'division', 'district'];

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'route_station')
            ->withPivot(['stop_order', 'arrival_time', 'departure_time', 'distance_from_source']);
    }

    /**
     * The application's single, fixed rail direction.
     */
    public static function railSequence(): array
    {
        return ['RP', 'RJ', 'KH', 'BA', 'MY', 'DA', 'SYL', 'CM', 'FE', 'NK', 'CTG'];
    }

    public function railOrder(): int
    {
        return array_search($this->code, self::railSequence(), true) + 1;
    }
}
