@extends('layouts.admin')

@section('title', 'Station Master Requests - Track Rail')

@section('content')
<div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
        <div class="text-uppercase text-purple fw-semibold small letter-spacing-1">Access management</div>
        <h1 class="h3 fw-bold mb-1">Station Master Requests</h1>
        <p class="text-muted mb-0">Review applications and assign approved staff to their station.</p>
    </div>
    <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis px-3 py-2">
        <i class="bi bi-hourglass-split me-1"></i>{{ $requests->where('status', 'pending')->count() }} shown pending
    </span>
</div>

<div class="card stat-card border-0 overflow-hidden">
    <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="h5 fw-bold mb-1">Applications</h2>
            <p class="small text-muted mb-0">Approve only verified station assignments.</p>
        </div>
        <span class="text-muted small">{{ $requests->total() }} total</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Applicant</th>
                    <th>Contact</th>
                    <th>Assigned station</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th class="pe-4 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold">{{ $req->name }}</div>
                            <div class="small text-muted">{{ $req->email }}</div>
                        </td>
                        <td class="small">{{ $req->phone ?? 'Not provided' }}</td>
                        <td><span class="badge rounded-pill bg-light text-dark border"><i class="bi bi-geo-alt me-1"></i>{{ $req->station->name ?? 'Not assigned' }}</span></td>
                        <td>
                            <span class="badge rounded-pill px-3 py-2 bg-{{ $req->status === 'approved' ? 'success' : ($req->status === 'rejected' ? 'danger' : 'warning') }}{{ $req->status === 'pending' ? ' text-dark' : '' }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $req->created_at->diffForHumans() }}</td>
                        <td class="pe-4 text-end">
                            @if($req->isPending())
                                <div class="d-inline-flex gap-2">
                                    <form method="POST" action="{{ route('admin.station-master-requests.approve', $req) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success px-3" onclick="return confirm('Approve {{ addslashes($req->name) }} as a Station Master?')">
                                            <i class="bi bi-check-lg me-1"></i>Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.station-master-requests.reject', $req) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-3" onclick="return confirm('Reject this request?')">
                                            <i class="bi bi-x-lg me-1"></i>Reject
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="small text-muted"><i class="bi bi-check-circle me-1"></i>Processed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>No station master requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
        <div class="card-footer bg-white border-0 p-4">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
