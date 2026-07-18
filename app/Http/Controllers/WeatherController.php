<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class WeatherController extends Controller
{
    public function __construct(private WeatherService $weatherService) {}

    public function index()
    {
        $stations = Station::all()->sortBy(fn (Station $station) => $station->railOrder())->values();
        $userType = $this->userType();

        return view('weather.index', compact('stations', 'userType'));
    }

    public function getStationWeather(Station $station): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'weather' => $this->weatherService->getWeather($station->name)]);
        } catch (Throwable $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 502);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate(['city' => ['required', 'string', 'min:2', 'max:100']]);

        try {
            return response()->json(['success' => true, 'weather' => $this->weatherService->getWeather($validated['city'])]);
        } catch (Throwable $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 502);
        }
    }

    public function widget(Request $request): JsonResponse
    {
        $validated = $request->validate(['station_id' => ['required', 'exists:stations,id']]);
        $station = Station::findOrFail($validated['station_id']);

        try {
            $weather = $this->weatherService->getWeather($station->name);
            return response()->json(['success' => true, 'weather' => $weather, 'html' => view('weather.widget', compact('station', 'weather'))->render()]);
        } catch (Throwable $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 502);
        }
    }

    private function userType(): string
    {
        if (! Auth::check()) return 'guest';
        if (Auth::user()->isAdmin()) return 'admin';
        if (Auth::user()->isStationMaster()) return 'station_master';
        return 'passenger';
    }
}
