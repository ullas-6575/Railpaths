<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Station;
use App\Models\Train;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RouteController extends Controller
{
    public function create(Train $train)
    {
        $stations = Station::all()->sortBy(fn (Station $station) => $station->railOrder())->values();

        return view('admin.trains.route-builder', compact('train', 'stations'));
    }

    public function store(Request $request, Train $train)
    {
        $validated = $request->validate([
            'route_name' => 'required|string|max:255',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i',
            'stations' => 'required|array|min:2',
            'stations.*.id' => 'nullable|exists:stations,id',
            'stations.*.station_id' => 'nullable|exists:stations,id',
            'stations.*.arrival_time' => 'nullable|date_format:H:i',
            'stations.*.departure_time' => 'nullable|date_format:H:i',
            'stations.*.distance_from_source' => 'nullable|numeric|min:0',
            'stations.*.distance' => 'nullable|numeric|min:0',
        ]);

        $this->ensureForwardStationOrder($validated['stations']);

        DB::transaction(function () use ($validated, $train) {
            $route = $train->routes()->create([
                'route_name' => $validated['route_name'],
                'departure_time' => $validated['departure_time'],
                'arrival_time' => $validated['arrival_time'],
                'is_active' => true,
            ]);

            $stations = [];
            foreach ($validated['stations'] as $index => $station) {
                $stationId = $station['id'] ?? $station['station_id'];
                $stations[$stationId] = [
                    'stop_order' => $index + 1,
                    'arrival_time' => $station['arrival_time'] ?? null,
                    'departure_time' => $station['departure_time'] ?? null,
                    'distance_from_source' => $station['distance_from_source'] ?? $station['distance'] ?? 0,
                ];
            }

            $route->stations()->attach($stations);
        });

        return redirect()->route('admin.trains.routes', $train)
            ->with('success', 'Route created successfully.');
    }

    public function show(Train $train)
    {
        $train->load('routes.stations');

        return view('admin.trains.routes', compact('train'));
    }

    public function edit(Train $train)
    {
        $train->load('routes.stations');
        $route = $train->routes->first();
        $routeStations = $route?->stations->map(fn ($station) => [
            'id' => $station->id,
            'name' => $station->name,
            'code' => $station->code,
            'arrival_time' => $station->pivot->arrival_time,
            'departure_time' => $station->pivot->departure_time,
            'distance_from_source' => $station->pivot->distance_from_source,
            'stop_order' => $station->pivot->stop_order,
        ])->values() ?? collect();
        $usedIds = $route?->stations->pluck('id') ?? collect();
        $availableStations = Station::whereNotIn('id', $usedIds)->orderBy('name')->get();

        return view('admin.trains.route-edit', compact('train', 'routeStations', 'availableStations'));
    }

    public function update(Request $request, Train $train)
    {
        $validated = $request->validate([
            'stations' => 'required|array|min:2',
            'stations.*.id' => 'required|exists:stations,id',
            'stations.*.arrival_time' => 'nullable|date_format:H:i',
            'stations.*.departure_time' => 'nullable|date_format:H:i',
            'stations.*.distance_from_source' => 'nullable|numeric|min:0',
        ]);

        $this->ensureForwardStationOrder($validated['stations']);

        $route = $train->routes()->firstOrFail();
        DB::transaction(function () use ($validated, $route) {
            $stations = [];
            foreach ($validated['stations'] as $index => $station) {
                $stations[$station['id']] = [
                    'stop_order' => $index + 1,
                    'arrival_time' => $station['arrival_time'] ?? null,
                    'departure_time' => $station['departure_time'] ?? null,
                    'distance_from_source' => $station['distance_from_source'] ?? 0,
                ];
            }
            $route->stations()->sync($stations);
        });

        return redirect()->route('admin.trains.routes', $train)
            ->with('success', 'Route updated successfully.');
    }

    private function ensureForwardStationOrder(array $stations): void
    {
        $stationIds = collect($stations)->map(fn ($station) => $station['id'] ?? $station['station_id']);
        $stationOrders = Station::whereIn('id', $stationIds)->get()->keyBy('id');
        $orders = $stationIds->map(fn ($stationId) => $stationOrders->get($stationId)?->railOrder())->all();

        if (in_array(null, $orders, true) || count($orders) !== count(array_unique($orders)) || $orders !== collect($orders)->sort()->values()->all()) {
            throw ValidationException::withMessages([
                'stations' => 'Stations must follow the fixed Rangpur to Chattogram order.',
            ]);
        }
    }

    public function destroy(Route $route)
    {
        $train = $route->train;
        $route->delete();

        return redirect()->route('admin.trains.routes', $train)
            ->with('success', 'Route deleted successfully.');
    }
}
