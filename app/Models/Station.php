<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'division', 'district'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function routeStations()
    {
        return $this->hasMany(RouteStation::class);
    }
}