<?php

namespace App\Enums;

enum UserRole: string
{
    case PASSENGER = 'passenger';
    case STATION_MASTER = 'station_master';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match($this) {
            self::PASSENGER => 'Passenger',
            self::STATION_MASTER => 'Station Master',
            self::ADMIN => 'Administrator',
        };
    }

    public function redirectRoute(): string
    {
        return match($this) {
            self::PASSENGER => 'dashboard',
            self::STATION_MASTER => 'station-master.dashboard',
            self::ADMIN => 'admin.dashboard',
        };
    }
}