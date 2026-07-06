<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StationMasterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StationMasterRequestController extends Controller
{
    public function index(): View
    {
        $requests = StationMasterRequest::with('station')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->latest()
            ->paginate(20);

        return view('admin.station-master-requests', [
            'requests' => $requests,
        ]);
    }

    public function approve(Request $request, StationMasterRequest $stationMasterRequest): RedirectResponse
    {
        if (!$stationMasterRequest->isPending()) {
            return back()->with('error', 'This request has already been processed.');
        }

        $user = User::create([
            'name'       => $stationMasterRequest->name,
            'email'      => $stationMasterRequest->email,
            'phone'      => $stationMasterRequest->phone,
            'password'   => $stationMasterRequest->password,
            'role'       => 'station_master',
            'station_id' => $stationMasterRequest->station_id,
        ]);

        $stationMasterRequest->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return back()->with('success', "Station Master '{$stationMasterRequest->name}' has been approved.");
    }

    public function reject(Request $request, StationMasterRequest $stationMasterRequest): RedirectResponse
    {
        if (!$stationMasterRequest->isPending()) {
            return back()->with('error', 'This request has already been processed.');
        }

        $stationMasterRequest->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_notes' => $request->input('admin_notes', 'Request rejected.'),
        ]);

        return back()->with('success', "Station Master request from '{$stationMasterRequest->name}' has been rejected.");
    }
}