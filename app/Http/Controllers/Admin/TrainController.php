<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Station;
use App\Models\Train;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TrainController extends Controller
{
    public function index(): View
    {
        $trains = Train::withCount('routes')->latest()->paginate(10);
        return view('admin.trains.index', compact('trains'));
    }

    public function create(): View
    {
        return view('admin.trains.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'train_number' => 'required|string|max:20|unique:trains',
            'name' => 'required|string|max:255',
            'type' => 'required|in:express,superfast,passenger,local',
            'total_seats' => 'required|integer|min:1',
        ]);

        Train::create($validated);

        return redirect()->route('admin.trains.index')
            ->with('success', 'Train created successfully.');
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
        $stations = Station::orderBy('name')->get();
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