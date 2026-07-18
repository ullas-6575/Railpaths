<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\Station;
use Illuminate\Support\Facades\Auth;

class PassengerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $stations = Station::all()
            ->sortBy(fn (Station $station) => $station->railOrder())
            ->values();
        $bookings = Booking::with(['route.train', 'route.stations', 'sourceStation', 'destinationStation'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereDate('travel_date', '>=', now()->toDateString())
            ->orderBy('travel_date')
            ->take(3)
            ->get();
        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stations', 'bookings', 'notifications'));
    }

    public function markNotificationRead(Notification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);
        $notification->update(['is_read' => true]);

        return back();
    }
}
