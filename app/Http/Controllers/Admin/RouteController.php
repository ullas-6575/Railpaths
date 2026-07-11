<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Train;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    /**
     * Show route builder (create new route).
     */
    public function create(Train $train)
    {
        $stations = Station::orderBy('name')->get();
        return view('admin.trains.route-builder', compact('train', 'stations'));
    }

    /**
     * Store new route.
     */
    public function store(Request $request, Train $train)
    {
        $validated = $request->validate([
            'stations' => 'required|array|min:2',
            'stations.*.id' => 'required|exists:stations,id',
            'stations.*.arrival_time' => 'required|date_format:H:i',
            'stations.*.departure_time' => 'required|date_format:H:i',
            'stations.*.distance_from_source' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($train, $validated) {
            foreach ($validated['stations'] as $index => $stationData) {
                $train->routes()->attach($stationData['id'], [
                    'stop_order' => $index + 1,
                    'arrival_time' => $stationData['arrival_time'],
                    'departure_time' => $stationData['departure_time'],
                    'distance_from_source' => $stationData['distance_from_source'],
                ]);
            }
        });

        return redirect()
            ->route('admin.trains.routes.show', $train)
            ->with('success', 'Route created successfully.');
    }

    /**
     * Show route details.
     */
    public function show(Train $train)
    {
        // Load routes and sort collection by pivot stop_order after loading
        $train->load('routes');
        $train->setRelation('routes', $train->routes->sortBy('pivot.stop_order')->values());

        return view('admin.trains.route-show', compact('train'));
    }

    /**
     * Show route edit form with drag-drop reordering.
     */
    public function edit(Train $train)
    {
        // Load routes and sort collection by pivot stop_order after loading
        $train->load('routes');
        $train->setRelation('routes', $train->routes->sortBy('pivot.stop_order')->values());

        $routeStations = $train->routes->map(function ($station) {
            return [
                'id' => $station->id,
                'name' => $station->name,
                'code' => $station->code,
                'arrival_time' => $station->pivot->arrival_time,
                'departure_time' => $station->pivot->departure_time,
                'distance_from_source' => $station->pivot->distance_from_source,
                'stop_order' => $station->pivot->stop_order,
            ];
        });

        $availableStations = Station::whereNotIn('id', $train->routes->pluck('id'))
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('admin.trains.route-edit', compact('train', 'routeStations', 'availableStations'));
    }

    /**
     * Update the route with reordered stations.
     */
    public function update(Request $request, Train $train)
    {
        $validated = $request->validate([
            'stations' => 'required|array|min:2',
            'stations.*.id' => 'required|exists:stations,id',
            'stations.*.arrival_time' => 'required|date_format:H:i',
            'stations.*.departure_time' => 'required|date_format:H:i',
            'stations.*.distance_from_source' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($train, $validated) {
            $train->routes()->detach();

            foreach ($validated['stations'] as $index => $stationData) {
                $train->routes()->attach($stationData['id'], [
                    'stop_order' => $index + 1,
                    'arrival_time' => $stationData['arrival_time'],
                    'departure_time' => $stationData['departure_time'],
                    'distance_from_source' => $stationData['distance_from_source'],
                ]);
            }
        });

        return redirect()
            ->route('admin.trains.routes.show', $train)
            ->with('success', 'Route updated successfully with new station order.');
    }
}