<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Seat;
use App\Models\Station;
use App\Models\Train;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class TrainController extends Controller
{
    public function index(): View
    {
        $trains = Train::withCount('routes')->latest()->paginate(10);
        return view('admin.trains.index', compact('trains'));
    }

    public function create(): View
    {
        $stations = Station::all()->sortBy(fn (Station $station) => $station->railOrder())->values();
        return view('admin.trains.create', compact('stations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'train_number' => 'required|string|max:20|unique:trains',
            'name' => 'required|string|max:255',
            'type' => 'required|in:express,superfast,passenger,local',
            'total_seats' => 'required|integer|min:1',
            'start_station_id' => 'required|exists:stations,id',
            'end_station_id' => 'required|exists:stations,id|different:start_station_id',
            'start_time' => 'required|date_format:H:i',
        ]);

        DB::transaction(function () use ($validated) {
            $train = Train::create([
                'train_number' => $validated['train_number'],
                'name' => $validated['name'],
                'type' => $validated['type'],
                'total_seats' => $validated['total_seats'],
                'is_active' => true,
            ]);

            // Auto-generate seat records
            for ($s = 1; $s <= $validated['total_seats']; $s++) {
                Seat::create([
                    'train_id' => $train->id,
                    'seat_number' => 'S' . str_pad($s, 3, '0', STR_PAD_LEFT),
                ]);
            }

            $masterSequence = Station::railSequence();

            $startStation = Station::find($validated['start_station_id']);
            $endStation = Station::find($validated['end_station_id']);

            $startIndex = array_search($startStation->code, $masterSequence);
            $endIndex = array_search($endStation->code, $masterSequence);

            if ($startIndex === false || $endIndex === false || $startIndex >= $endIndex) {
                throw ValidationException::withMessages([
                    'end_station_id' => 'A train must travel forward in the fixed Rangpur to Chattogram direction.',
                ]);
            }

            $step = 1;
            $pathCodes = [];
            for ($i = $startIndex; $startIndex < $endIndex ? $i <= $endIndex : $i >= $endIndex; $i += $step) {
                $pathCodes[] = $masterSequence[$i];
            }

            $stationsInPath = Station::whereIn('code', $pathCodes)->get()->keyBy('code');

            $route = $train->routes()->create([
                'route_name' => $startStation->name . ' to ' . $endStation->name,
                'departure_time' => $validated['start_time'],
                'arrival_time' => \Carbon\Carbon::parse($validated['start_time'])->addMinutes((count($pathCodes) - 1) * 30)->format('H:i'),
                'is_active' => true,
            ]);

            $currentTime = \Carbon\Carbon::parse($validated['start_time']);
            $distance = 0;
            $stopOrder = 1;

            foreach ($pathCodes as $code) {
                $station = $stationsInPath[$code];
                
                $route->stations()->attach($station->id, [
                    'stop_order' => $stopOrder,
                    'arrival_time' => $stopOrder === 1 ? null : $currentTime->format('H:i'),
                    'departure_time' => $stopOrder === count($pathCodes) ? null : $currentTime->format('H:i'),
                    'distance_from_source' => $distance,
                ]);

                // Create schedule for today automatically
                Schedule::create([
                    'train_id' => $train->id,
                    'route_id' => $route->id,
                    'station_id' => $station->id,
                    'date' => now()->toDateString(),
                    'arrival_time' => $stopOrder === 1 ? null : $currentTime->format('H:i'),
                    'departure_time' => $stopOrder === count($pathCodes) ? null : $currentTime->format('H:i'),
                ]);

                $currentTime->addMinutes(30);
                $distance += 50; 
                $stopOrder++;
            }
        });

        return redirect()->route('admin.trains.index')
            ->with('success', 'Train, route, and schedule created successfully.');
    }

    public function edit(Train $train): View
    {
        $train->load('routes.stations');
        return view('admin.trains.edit', compact('train'));
    }

    public function update(Request $request, Train $train): RedirectResponse
    {
        $validated = $request->validate([
            'train_number' => 'required|string|max:20|unique:trains,train_number,' . $train->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:express,superfast,passenger,local',
            'total_seats' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $train->update($validated);

        return redirect()->route('admin.trains.index')
            ->with('success', 'Train updated successfully.');
    }

    public function destroy(Train $train): RedirectResponse
    {
        $train->delete();
        return redirect()->route('admin.trains.index')
            ->with('success', 'Train deleted successfully.');
    }

    // ==================== ROUTE BUILDER ====================

    public function routeBuilder(Train $train): View
    {
        $train->load(['routes.stations']);
        $stations = Station::all()->sortBy(fn (Station $station) => $station->railOrder())->values();
        return view('admin.trains.route-builder', compact('train', 'stations'));
    }

    public function storeRoute(Request $request, Train $train): RedirectResponse
    {
        $validated = $request->validate([
            'route_name' => 'required|string|max:255',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i',
            'stations' => 'required|array|min:2',
            'stations.*.station_id' => 'required|exists:stations,id',
            'stations.*.arrival_time' => 'nullable|date_format:H:i',
            'stations.*.departure_time' => 'nullable|date_format:H:i',
            'stations.*.distance' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $train) {
            // Create route
            $route = Route::create([
                'train_id' => $train->id,
                'route_name' => $validated['route_name'],
                'departure_time' => $validated['departure_time'],
                'arrival_time' => $validated['arrival_time'],
            ]);

            // Attach stations with stop_order
            $syncData = [];
            foreach ($validated['stations'] as $index => $stationData) {
                $syncData[$stationData['station_id']] = [
                    'stop_order' => $index + 1,
                    'arrival_time' => $stationData['arrival_time'] ?? null,
                    'departure_time' => $stationData['departure_time'] ?? null,
                    'distance_from_source' => $stationData['distance'],
                ];
            }

            $route->stations()->attach($syncData);
        });

        return redirect()->route('admin.trains.routes', $train)
            ->with('success', 'Route created successfully.');
    }

    public function updateRouteOrder(Request $request, Route $route): RedirectResponse
    {
        $validated = $request->validate([
            'stations' => 'required|array',
            'stations.*.station_id' => 'required|exists:stations,id',
            'stations.*.arrival_time' => 'nullable|date_format:H:i',
            'stations.*.departure_time' => 'nullable|date_format:H:i',
            'stations.*.distance' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $route) {
            // Detach all and re-attach in new order
            $route->stations()->detach();

            $syncData = [];
            foreach ($validated['stations'] as $index => $stationData) {
                $syncData[$stationData['station_id']] = [
                    'stop_order' => $index + 1,
                    'arrival_time' => $stationData['arrival_time'] ?? null,
                    'departure_time' => $stationData['departure_time'] ?? null,
                    'distance_from_source' => $stationData['distance'],
                ];
            }

            $route->stations()->attach($syncData);
        });

        return redirect()->route('admin.trains.routes', $route->train_id)
            ->with('success', 'Route order updated successfully.');
    }

    public function destroyRoute(Route $route): RedirectResponse
    {
        $trainId = $route->train_id;
        $route->delete();
        return redirect()->route('admin.trains.routes', $trainId)
            ->with('success', 'Route deleted successfully.');
    }

    public function routes(Train $train): View
    {
        $train->load(['routes.stations']);
        return view('admin.trains.routes', compact('train'));
    }
}
