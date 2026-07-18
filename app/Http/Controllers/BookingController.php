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
use Illuminate\Validation\ValidationException;

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

        $sourceStation = Station::findOrFail($sourceId);
        $destStation = Station::findOrFail($destinationId);
        if ($sourceStation->railOrder() >= $destStation->railOrder()) {
            throw ValidationException::withMessages([
                'destination' => 'Choose a destination later in the fixed Rangpur to Chattogram direction.',
            ]);
        }

        // Find trains that have routes covering both stations
        $trains = Train::with(['schedules' => function ($query) use ($journeyDate) {
                $query->whereDate('date', $journeyDate);
            }, 'routes.stations'])
            ->whereHas('routes.stations', fn ($query) => $query->whereKey($sourceId))
            ->whereHas('routes.stations', fn ($query) => $query->whereKey($destinationId))
            ->get()
            ->map(function ($train) use ($sourceId, $destinationId, $journeyDate) {
                $route = $train->routes->first();
                $stations = $train->routes->flatMap->stations->sortBy('pivot.stop_order')->values();
                $sourceOrder = $stations->firstWhere('id', $sourceId)?->pivot->stop_order;
                $destinationOrder = $stations->firstWhere('id', $destinationId)?->pivot->stop_order;
                $bookedSeatIds = collect();

                if ($route && $sourceOrder !== null && $destinationOrder !== null) {
                    $bookedSeatIds = Booking::with(['seats', 'route.stations'])
                        ->where('route_id', $route->id)
                        ->whereDate('travel_date', $journeyDate)
                        ->where('status', 'confirmed')
                        ->get()
                        ->filter(function ($booking) use ($route, $sourceOrder, $destinationOrder) {
                            $bookingSource = optional($route->stations->firstWhere('id', $booking->source_station_id)?->pivot)->stop_order;
                            $bookingDestination = optional($route->stations->firstWhere('id', $booking->dest_station_id)?->pivot)->stop_order;
                            return $bookingSource < $destinationOrder && $bookingDestination > $sourceOrder;
                        })
                        ->flatMap(fn ($booking) => $booking->seats->pluck('id'))
                        ->unique()
                        ->values();
                }

                $train->setRelation('routes', $stations);
                $train->available_seats = max(0, $train->total_seats - $bookedSeatIds->count());
                return [$train, $sourceOrder, $destinationOrder];
            })
            ->filter(function ($item) {
                return $item[1] !== null && $item[2] !== null && $item[1] < $item[2];
            })
            ->map(function ($item) {
                return $item[0];
            });

        return view('booking.search-results', compact('trains', 'sourceStation', 'destStation', 'journeyDate'));
    }

    public function showSeats(Request $request, Train $train)
    {
        $request->validate([
            'source' => 'required',
            'destination' => 'required',
            'date' => 'required|date',
        ]);

        $route = $train->routes()->with('stations')->firstOrFail();
        $source = $route->stations->firstWhere('id', $request->source);
        $destination = $route->stations->firstWhere('id', $request->destination);
        abort_unless($source && $destination && $source->pivot->stop_order < $destination->pivot->stop_order, 422);

        $schedule = Schedule::firstOrCreate(
            ['train_id' => $train->id, 'route_id' => $route->id, 'station_id' => $source->id, 'date' => $request->date],
            ['arrival_time' => $source->pivot->arrival_time, 'departure_time' => $source->pivot->departure_time]
        );

        // Get all seats for this train
        $seats = Seat::where('train_id', $train->id)
            ->with(['bookings' => function ($query) use ($request, $route) {
                $query->where('travel_date', $request->date)
                    ->where('status', 'confirmed')
                    ->where('route_id', $route->id)
                    ->with(['route.stations']);
            }])
            ->get()
            ->map(function ($seat) use ($request, $route) {
                // Check if seat is booked for this route segment
                $isBooked = $seat->bookings->contains(function ($booking) use ($request, $route) {
                    $bookingSourceOrder = optional($booking->route->stations->firstWhere('id', $booking->source_station_id)?->pivot)->stop_order;
                    $bookingDestinationOrder = optional($booking->route->stations->firstWhere('id', $booking->dest_station_id)?->pivot)->stop_order;
                    $sourceOrder = optional($route->stations->firstWhere('id', $request->source)?->pivot)->stop_order;
                    $destinationOrder = optional($route->stations->firstWhere('id', $request->destination)?->pivot)->stop_order;

                    return $bookingSourceOrder < $destinationOrder && $bookingDestinationOrder > $sourceOrder;
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

        $schedule = Schedule::with('route.stations')->findOrFail($request->schedule_id);
        abort_unless($schedule->train_id == $request->train_id, 422);
        $sourceOrder = optional($schedule->route->stations->firstWhere('id', $request->source_id)?->pivot)->stop_order;
        $destinationOrder = optional($schedule->route->stations->firstWhere('id', $request->destination_id)?->pivot)->stop_order;
        abort_unless($sourceOrder && $destinationOrder && $sourceOrder < $destinationOrder, 422);

        DB::beginTransaction();
        
        try {
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'route_id' => $schedule->route_id,
                'source_station_id' => $request->source_id,
                'dest_station_id' => $request->destination_id,
                'travel_date' => $schedule->date,
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
