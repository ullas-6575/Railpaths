<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->guest(route('admin.login'));
        }

        if (auth()->user()->role !== UserRole::ADMIN) {
            abort(403);
        }

        return $next($request);
    }
}