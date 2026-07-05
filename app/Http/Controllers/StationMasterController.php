<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StationMasterController extends Controller
{
    /**
     * Show the station master dashboard.
     */
    public function dashboard(): View
    {
        return view('station-master.dashboard', [
            'user' => Auth::user(),
        ]);
    }
}
