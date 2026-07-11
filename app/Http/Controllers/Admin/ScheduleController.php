<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Train;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $trains = Train::withCount('routes')->get();
        $selectedTrainId = $request->get('train_id');
        $selectedTrain = null;
        $calendarData = null;

        if ($selectedTrainId) {
            $selectedTrain = Train::findOrFail($selectedTrainId);
            $selectedTrain->load('routes');
            $selectedTrain->setRelation('routes', $selectedTrain->routes->sortBy('pivot.stop_order')->values());

            $month = $request->get('month', now()->format('Y-m'));
            $calendarData = $this->buildCalendar($month, $selectedTrain);
        }

        return view('admin.trains.schedule', compact(
            'trains',
            'selectedTrain',
            'selectedTrainId',
            'calendarData'
        ));
    }

    private function buildCalendar(string $month, Train $train): array
    {
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SATURDAY);

        $days = [];
        $current = $startOfCalendar->copy();
        $routes = $train->routes;

        while ($current <= $endOfCalendar) {
            $dayEvents = [];
            $dateStr = $current->format('Y-m-d');

            if ($current->month === $startOfMonth->month && $routes->isNotEmpty()) {
                foreach ($routes as $index => $station) {
                    $dayEvents[] = [
                        'station_id' => $station->id,
                        'station_name' => $station->name,
                        'station_code' => $station->code,
                        'arrival_time' => $station->pivot->arrival_time,
                        'departure_time' => $station->pivot->departure_time,
                        'stop_order' => $station->pivot->stop_order,
                        'distance_from_source' => $station->pivot->distance_from_source,
                        'is_source' => $index === 0,
                        'is_destination' => $index === $routes->count() - 1,
                    ];
                }
            }

            $days[] = [
                'date' => $dateStr,
                'day' => $current->day,
                'is_current_month' => $current->month === $startOfMonth->month,
                'is_today' => $current->isToday(),
                'events' => $dayEvents,
            ];

            $current->addDay();
        }

        return [
            'month_name' => $startOfMonth->format('F Y'),
            'prev_month' => $startOfMonth->copy()->subMonth()->format('Y-m'),
            'next_month' => $startOfMonth->copy()->addMonth()->format('Y-m'),
            'weeks' => array_chunk($days, 7),
        ];
    }

    public function apiSchedule(Request $request, Train $train)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $train->load('routes');
        $train->setRelation('routes', $train->routes->sortBy('pivot.stop_order')->values());

        $events = [];
        foreach ($train->routes as $index => $station) {
            $events[] = [
                'station_id' => $station->id,
                'station_name' => $station->name,
                'station_code' => $station->code,
                'arrival_time' => $station->pivot->arrival_time,
                'departure_time' => $station->pivot->departure_time,
                'stop_order' => $station->pivot->stop_order,
                'distance_from_source' => $station->pivot->distance_from_source,
                'is_source' => $index === 0,
                'is_destination' => $index === $train->routes->count() - 1,
            ];
        }

        return response()->json([
            'train' => ['id' => $train->id, 'name' => $train->name, 'number' => $train->train_number],
            'date' => $date,
            'events' => $events,
        ]);
    }
}