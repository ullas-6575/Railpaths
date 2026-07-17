<?php
namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\StationLog;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StationMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:station_master']);
    }

    public function dashboard()
    {
        $stationMaster = Auth::user();
        $station = $stationMaster->assignedStation;
        if (! $station) {
            return view('station-master.dashboard', ['station' => null, 'incomingTrains' => collect()]);
        }
        
        $today = now()->toDateString();
        
        // Get today's scheduled trains for this station
        $incomingTrains = Schedule::with(['train', 'route'])
            ->where('station_id', $station->id)
            ->whereDate('date', $today)
            ->orderBy('arrival_time')
            ->get()
            ->map(function ($schedule) {
                $log = StationLog::where('schedule_id', $schedule->id)->first();
                $schedule->log = $log;
                $schedule->status = $log ? $log->status : 'pending';
                $schedule->delay = $log ? $log->delay_minutes : 0;
                return $schedule;
            });

        return view('station-master.dashboard', compact('station', 'incomingTrains'));
    }

    public function logTrain(Request $request, $scheduleId)
    {
        $request->validate([
            'actual_arrival' => 'nullable|date_format:H:i',
            'actual_departure' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string|max:500',
        ]);

        $schedule = Schedule::findOrFail($scheduleId);
        $stationMaster = Auth::user();

        // Calculate delay
        $delayMinutes = 0;
        $status = 'on_time';
        
        if ($request->actual_arrival) {
            $scheduled = Carbon::parse($schedule->arrival_time);
            $actual = Carbon::parse($request->actual_arrival);
            $delayMinutes = $actual->diffInMinutes($scheduled, false);
            
            if ($delayMinutes > 5) {
                $status = 'delayed';
            } elseif ($delayMinutes < 0) {
                $delayMinutes = 0; // Early arrival
            }
        }

        $log = StationLog::updateOrCreate(
            ['schedule_id' => $scheduleId],
            [
                'station_master_id' => $stationMaster->id,
                'station_id' => $schedule->station_id,
                'train_id' => $schedule->train_id,
                'scheduled_arrival' => $schedule->arrival_time,
                'actual_arrival' => $request->actual_arrival,
                'scheduled_departure' => $schedule->departure_time,
                'actual_departure' => $request->actual_departure,
                'delay_minutes' => $delayMinutes,
                'remarks' => $request->remarks,
                'status' => $status,
            ]
        );

        // Notify affected passengers if delayed
        if ($status === 'delayed' && $delayMinutes > 0) {
            $this->notifyPassengersOfDelay($schedule->train_id, $delayMinutes, $schedule->station_id);
        }

        return redirect()->back()->with('success', 'Train log updated successfully. Delay: ' . $delayMinutes . ' minutes');
    }

    private function notifyPassengersOfDelay($trainId, $delayMinutes, $stationId)
    {
        $bookings = \App\Models\Booking::with('user')
            ->where('train_id', $trainId)
            ->where('journey_date', now()->toDateString())
            ->where('status', 'confirmed')
            ->get();

        foreach ($bookings as $booking) {
            \App\Models\Notification::create([
                'user_id' => $booking->user_id,
                'title' => 'Train Delay Alert',
                'message' => "Your train {$booking->train->name} is delayed by {$delayMinutes} minutes at station. Expected new arrival time will be updated shortly.",
                'type' => 'delay',
                'is_read' => false,
            ]);
        }
    }
}
