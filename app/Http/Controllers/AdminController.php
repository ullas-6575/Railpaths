<?php

namespace App\Http\Controllers;

use App\Models\StationMasterRequest;
use App\Models\Train;
use App\Models\Route;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'totalUsers'         => User::count(),
            'totalPassengers'    => User::where('role', \App\Enums\UserRole::PASSENGER)->count(),
            'totalStationMasters'=> User::where('role', \App\Enums\UserRole::STATION_MASTER)->count(),
            'pendingRequests'    => StationMasterRequest::where('status', 'pending')->count(),
            'totalTrains'        => Train::count(),
            'totalRoutes'        => Route::count(),
        ]);
    }
}