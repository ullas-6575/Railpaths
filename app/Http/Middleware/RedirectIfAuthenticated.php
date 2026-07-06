<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $role = auth()->user()->role;
                
                if ($role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }
                if ($role === 'station_master') {
                    return redirect()->route('station-master.dashboard');
                }
                
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}