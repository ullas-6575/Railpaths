<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StationMasterRequest;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StationMasterRequestController extends Controller
{
    /**
     * Display a listing of station master requests.
     */
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

    /**
     * Approve a station master request — create the user account.
     */
    public function approve(Request $request, StationMasterRequest $stationMasterRequest): RedirectResponse
    {
        if (!$stationMasterRequest->isPending()) {
            return back()->with('error', 'This request has already been processed.');
        }

        // Create the actual user
        $user = User::create([
            'name'       => $stationMasterRequest->name,
            'email'      => $stationMasterRequest->email,
            'phone'      => $stationMasterRequest->phone,
            'password'   => $stationMasterRequest->password, // Already hashed
            'role'       => UserRole::STATION_MASTER->value,
            'station_id' => $stationMasterRequest->station_id,
        ]);

        // Mark request as approved
        $stationMasterRequest->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return back()->with('success', "Station Master '{$stationMasterRequest->name}' has been approved.");
    }

    /**
     * Reject a station master request.
     */
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
