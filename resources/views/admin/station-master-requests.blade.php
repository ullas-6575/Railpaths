<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Station Master Requests - Track Rail Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --rail-purple: #7c3aed; }
        body { background-color: #f8f9fc; }
        .navbar-admin { background-color: var(--rail-purple); }
        .badge-pending { background-color: #f59e0b; }
        .badge-approved { background-color: #10b981; }
        .badge-rejected { background-color: #ef4444; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark navbar-admin shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-shield-lock-fill"></i> Track Rail Admin
            </a>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="text-white-50 text-decoration-none small">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light">
                        <i class="bi bi-box-arrow-right"></i> Log Out
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h2 class="fw-bold mb-1">Station Master Requests</h2>
        <p class="text-muted mb-4">Review and manage station master registration applications.</p>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-x-circle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Station</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                                <tr>
                                    <td class="ps-4 fw-medium">{{ $req->name }}</td>
                                    <td>{{ $req->email }}</td>
                                    <td>{{ $req->phone ?? '—' }}</td>
                                    <td>{{ $req->station->name ?? '—' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $req->status }} text-white px-2 py-1">
                                            {{ ucfirst($req->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $req->created_at->diffForHumans() }}</td>
                                    <td class="pe-4 text-end">
                                        @if($req->isPending())
                                            <form method="POST" action="{{ route('admin.station-master-requests.approve', $req) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success rounded-2"
                                                        onclick="return confirm('Approve {{ $req->name }} as Station Master?')">
                                                    <i class="bi bi-check-lg"></i> Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.station-master-requests.reject', $req) }}" class="d-inline ms-1">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-2"
                                                        onclick="return confirm('Reject request from {{ $req->name }}?')">
                                                    <i class="bi bi-x-lg"></i> Reject
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small">{{ $req->status === 'approved' ? 'Approved' : 'Rejected' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                        No station master requests yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $requests->links() }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
