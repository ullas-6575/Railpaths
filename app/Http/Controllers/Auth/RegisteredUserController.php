<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StationMasterRequest;
use App\Models\Station;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(Request $request): View
    {
        $role = $request->query('role', 'passenger');

        if (! in_array($role, ['passenger', 'station_master'], true)) {
            $role = 'passenger';
        }

        $stations = Station::all()
            ->sortBy(fn (Station $station) => $station->railOrder())
            ->values();

        return view('auth.register', compact('role', 'stations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'passenger');

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['nullable', 'in:passenger,station_master'],
        ];

        if ($role === 'station_master') {
            $rules['station_id'] = ['required', 'exists:stations,id'];
            $rules['email'][] = 'unique:station_master_requests,email';
        }

        $request->validate($rules);

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

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'passenger',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
