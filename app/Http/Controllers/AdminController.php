<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard(): View
    {
        $totalUsers = User::count();
        $totalPassengers = User::where('role', 'passenger')->count();
        $totalStationMasters = User::where('role', 'station_master')->count();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalPassengers' => $totalPassengers,
            'totalStationMasters' => $totalStationMasters,
        ]);
    }
}
