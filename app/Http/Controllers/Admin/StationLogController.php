<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StationLog;
use Illuminate\Http\Request;

class StationLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = StationLog::with(['stationMaster', 'station', 'train', 'schedule'])
            ->latest();

        if ($request->station_id) {
            $query->where('station_id', $request->station_id);
        }

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $logs = $query->paginate(20);
        $stations = \App\Models\Station::orderBy('name')->get();

        return view('admin.station-logs.index', compact('logs', 'stations'));
    }

    public function show(StationLog $log)
    {
        return view('admin.station-logs.show', compact('log'));
    }

    public function statistics()
    {
        $stats = [
            'total_logs_today' => StationLog::whereDate('created_at', today())->count(),
            'delayed_trains' => StationLog::whereDate('created_at', today())->where('status', 'delayed')->count(),
            'avg_delay' => StationLog::whereDate('created_at', today())->where('delay_minutes', '>', 0)->avg('delay_minutes') ?? 0,
            'station_wise' => StationLog::with('station')
                ->selectRaw('station_id, COUNT(*) as total, SUM(CASE WHEN status = "delayed" THEN 1 ELSE 0 END) as delayed')
                ->whereDate('created_at', today())
                ->groupBy('station_id')
                ->get(),
        ];

        return view('admin.station-logs.statistics', compact('stats'));
    }
}
