<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\Train;
use App\Models\Schedule;
use App\Models\Booking;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'source' => 'required|exists:stations,id',
            'destination' => 'required|exists:stations,id|different:source',
            'journey_date' => 'required|date|after_or_equal:today',
        ]);

        $sourceId = $request->source;
        $destinationId = $request->destination;
        $journeyDate = $request->journey_date;

        // Find trains that have routes covering both stations
        $trains = Train::with(['schedules' => function ($query) use ($journeyDate) {
                $query->whereDate('date', $journeyDate);
            }, 'routes.stations'])
            ->whereHas('routes.stations', fn ($query) => $query->whereKey($sourceId))
            ->whereHas('routes.stations', fn ($query) => $query->whereKey($destinationId))
            ->get()
            ->map(function ($train) use ($sourceId, $destinationId) {
                $stations = $train->routes->flatMap->stations->sortBy('pivot.stop_order')->values();
                $sourceOrder = $stations->firstWhere('id', $sourceId)?->pivot->stop_order;
                $destinationOrder = $stations->firstWhere('id', $destinationId)?->pivot->stop_order;
                $train->setRelation('routes', $stations);
                return [$train, $sourceOrder, $destinationOrder];
            })
            ->filter(function ($item) {
                return $item[1] !== null && $item[2] !== null && $item[1] < $item[2];
            })
            ->map(function ($item) {
                return $item[0];
            });

        $sourceStation = Station::find($sourceId);
        $destStation = Station::find($destinationId);

        return view('booking.search-results', compact('trains', 'sourceStation', 'destStation', 'journeyDate'));
    }

    public function showSeats(Request $request, Train $train)
    {
        $request->validate([
            'source' => 'required',
            'destination' => 'required',
            'date' => 'required|date',
        ]);

        $schedule = Schedule::where('train_id', $train->id)
            ->whereDate('date', $request->date)
            ->firstOrFail();

        // Get all seats for this train
        $seats = Seat::where('train_id', $train->id)
            ->with(['bookings' => function ($query) use ($request) {
                $query->where('journey_date', $request->date)
                      ->where('status', 'confirmed');
            }])
            ->get()
            ->map(function ($seat) use ($request) {
                // Check if seat is booked for this route segment
                $isBooked = $seat->bookings->contains(function ($booking) use ($request) {
                    // Simplified: check if any booking overlaps with requested route
                    return true; // Implement proper overlap logic based on your schema
                });
                
                $seat->is_available = !$isBooked;
                return $seat;
            });

        return view('booking.select-seats', compact('train', 'schedule', 'seats', 'request'));
    }

    public function book(Request $request)
    {
        $request->validate([
            'train_id' => 'required|exists:trains,id',
            'schedule_id' => 'required|exists:schedules,id',
            'source_id' => 'required|exists:stations,id',
            'destination_id' => 'required|exists:stations,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
            'passenger_names' => 'required|array',
            'passenger_ages' => 'required|array',
        ]);

        DB::beginTransaction();
        
        try {
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'route_id' => Schedule::findOrFail($request->schedule_id)->route_id,
                'source_station_id' => $request->source_id,
                'dest_station_id' => $request->destination_id,
                'travel_date' => Schedule::findOrFail($request->schedule_id)->date,
                'class_type' => 'shovan',
                'seat_count' => count($request->seat_ids),
                'status' => 'confirmed',
            ]);

            $booking->seats()->sync($request->seat_ids);

            foreach ($request->seat_ids as $index => $seatId) {
                $booking->passengers()->create([
                    'seat_id' => $seatId,
                    'name' => $request->passenger_names[$index],
                    'age' => $request->passenger_ages[$index],
                ]);
            }

            DB::commit();

            return redirect()->route('booking.confirmation', $booking)->with('success', 'Booking confirmed!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Booking failed. Please try again.');
        }
    }

    private function calculateFare($trainId, $sourceId, $destId, $passengerCount)
    {
        // Implement your fare calculation logic
        $baseFare = 100; // Base fare per passenger
        return $baseFare * $passengerCount;
    }

    private function generatePNR()
    {
        return strtoupper(substr(uniqid(), -10));
    }

    public function confirmation(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        return view('booking.confirmation', compact('booking'));
    }
}
