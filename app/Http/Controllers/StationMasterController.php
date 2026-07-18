<?php
namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\StationLog;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Route;
use App\Models\Booking;
use App\Models\Notification;

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

        $schedule = Schedule::with('train')->findOrFail($scheduleId);
        $stationMaster = Auth::user();
        abort_unless((int) $schedule->station_id === (int) $stationMaster->station_id, 403);

        // Calculate delay
        $delayMinutes = 0;
        $status = 'on_time';
        
        $usingArrival = filled($request->actual_arrival) && filled($schedule->arrival_time);
        $actualTime = $usingArrival ? $request->actual_arrival : $request->actual_departure;
        $scheduledTime = $usingArrival ? $schedule->arrival_time : $schedule->departure_time;

        if ($actualTime && $scheduledTime) {
            $scheduled = Carbon::parse($scheduledTime);
            $actual = Carbon::parse($actualTime);
            $delayMinutes = max(0, $scheduled->diffInMinutes($actual, false));

            if ($delayMinutes > 0) {
                $status = 'delayed';
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
            $this->notifyPassengersOfDelay($schedule->train_id, $schedule->date, $delayMinutes, $schedule->station_id);
        }

        return redirect()->back()->with('success', 'Train log updated successfully. Delay: ' . $delayMinutes . ' minutes');
    }

    private function notifyPassengersOfDelay($trainId, $travelDate, $delayMinutes, $stationId)
    {
        $routes = Route::with('stations')->where('train_id', $trainId)->get();
        $routeIds = $routes->pluck('id');

        $train = \App\Models\Train::find($trainId);
        $bookings = \App\Models\Booking::with('user')
            ->whereIn('route_id', $routeIds)
            ->whereDate('travel_date', $travelDate)
            ->where('status', 'confirmed')
            ->get();

        $station = \App\Models\Station::find($stationId);
        $stationName = $station ? $station->name : 'a station';

        foreach ($bookings as $booking) {
            $route = $routes->firstWhere('id', $booking->route_id);
            $loggedOrder = optional($route?->stations->firstWhere('id', $stationId)?->pivot)->stop_order;
            $sourceOrder = optional($route?->stations->firstWhere('id', $booking->source_station_id)?->pivot)->stop_order;
            $destinationOrder = optional($route?->stations->firstWhere('id', $booking->dest_station_id)?->pivot)->stop_order;

            // A passenger is affected only while the train is on their booked segment.
            if (! $loggedOrder || ! $sourceOrder || ! $destinationOrder ||
                $loggedOrder < $sourceOrder || $loggedOrder > $destinationOrder) {
                continue;
            }

            $trainName = $train?->name ?? 'Your booked train';
            $message = "{$trainName} is delayed by {$delayMinutes} minutes at {$stationName}. Please check the updated timing before travelling.";

            $alreadyNotified = Notification::where('user_id', $booking->user_id)
                ->where('type', 'delay')
                ->where('message', $message)
                ->whereDate('created_at', now()->toDateString())
                ->exists();

            if (! $alreadyNotified) {
                Notification::create([
                'user_id' => $booking->user_id,
                'title' => 'Train Delay Alert',
                'message' => $message,
                'type' => 'delay',
                'is_read' => false,
                ]);
            }
        }
    }
}
