<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the passenger / station master login view.
     */
    public function create(Request $request): View
    {
        $role = $request->query('role', 'passenger');

        if (! in_array($role, ['passenger', 'station_master'], true)) {
            $role = 'passenger';
        }

        return view('auth.login', [
            'role' => $role,
        ]);
    }

    /**
     * Display the admin login view.
     */
    public function createAdmin(): View
    {
        return view('auth.admin-login');
    }

    /**
     * Display the station master login view.
     */
    public function createStationMaster(): View
    {
        return view('auth.station-master-login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        $requestedRole = $request->input('role');

        // If the form specified an expected role (admin / station_master / passenger)
        // make sure the authenticated account actually holds that role.
        if ($requestedRole && $user->role->value !== $requestedRole) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'These credentials do not have access to that login portal.',
            ]);
        }

        return redirect()->intended(route($user->role->redirectRoute()));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
