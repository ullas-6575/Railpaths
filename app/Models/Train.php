<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Train extends Model
{
    use HasFactory;

    protected $fillable = [
        'train_number',
        'name',
        'type',
        'total_seats',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }
}