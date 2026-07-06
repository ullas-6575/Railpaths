<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request): View
    {
        $isAdmin = $request->is('admin/login') || $request->query('role') === 'admin';
        $role = $request->query('role', 'passenger');

        if ($request->is('admin/login')) {
            return view('auth.admin-login', compact('isAdmin', 'role'));
        }
        
        if ($request->is('station-master/login')) {
            return view('auth.station-master-login', compact('isAdmin', 'role'));
        }

        return view('auth.login', compact('isAdmin', 'role'));
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $role = auth()->user()->role;

        if ($role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }
        
        if ($role === 'station_master') {
            return redirect()->intended(route('station-master.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}