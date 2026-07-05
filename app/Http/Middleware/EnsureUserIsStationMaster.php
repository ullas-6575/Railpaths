<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStationMaster
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->guest(route('station-master.login'));
        }

        if (auth()->user()->role !== UserRole::STATION_MASTER) {
            abort(403);
        }

        // Ensure station master can only access their assigned station
        if ($request->route('station') && $request->route('station')->id !== auth()->user()->station_id) {
            abort(403);
        }

        return $next($request);
    }
}