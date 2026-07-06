<?php
namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Not logged in → redirect to admin login
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        // Logged in but not admin → hide admin existence (404)
        if (auth()->user()->role !== UserRole::ADMIN) {
            abort(404);
        }

        return $next($request);
    }
}