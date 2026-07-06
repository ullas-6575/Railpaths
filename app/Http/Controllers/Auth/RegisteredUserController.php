<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StationMasterRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $role = $request->query('role', 'passenger');

        // Only passenger / station_master can self-register. Admin accounts are not
        // created through the public registration form.
        if (! in_array($role, ['passenger', 'station_master'], true)) {
            $role = 'passenger';
        }

        return view('auth.register', [
            'role' => $role,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'passenger');

        // Base validation rules
        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['nullable', 'in:passenger,station_master'],
        ];

        // Station master needs a station
        if ($role === 'station_master') {
            $rules['station_id'] = ['required', 'exists:stations,id'];
            // Also ensure email is unique in station_master_requests
            $rules['email'][] = 'unique:station_master_requests,email';
        }

        $request->validate($rules);

        // Station Master → create a pending request (not a direct user)
        if ($role === 'station_master') {
            StationMasterRequest::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'password'   => Hash::make($request->password),
                'station_id' => $request->station_id,
                'status'     => 'pending',
            ]);

            return redirect()->route('station-master.pending');
        }

        // Passenger → create user directly
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'passenger',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route($user->role->redirectRoute()));
    }
}
