@extends('layouts.admin')

@section('title', 'Admin Dashboard - Track Rail')

@section('content')
    <h2 class="fw-bold mb-1">Welcome back, {{ auth()->user()->name }}</h2>
    <p class="text-muted mb-4">Here's what's happening across Track Rail.</p>

    <div class="row g-4">
        {{-- Total Users --}}
        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Users</div>
                        <div class="fs-3 fw-bold">{{ $totalUsers ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Passengers --}}
        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Passengers</div>
                        <div class="fs-3 fw-bold">{{ $totalPassengers ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Station Masters --}}
        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Station Masters</div>
                        <div class="fs-3 fw-bold">{{ $totalStationMasters ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Trains --}}
        <div class="col-md-4">
            <div class="card stat-card h-100 border-start border-4 border-info">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-train-front"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Trains</div>
                        <div class="fs-3 fw-bold">{{ $totalTrains ?? 0 }}</div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pt-0">
                    <a href="{{ route('admin.trains.index') }}" class="text-info text-decoration-none small fw-bold">
                        Manage Trains →
                    </a>
                </div>
            </div>
        </div>

        {{-- Total Routes --}}
        <div class="col-md-4">
            <div class="card stat-card h-100 border-start border-4 border-secondary">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="bi bi-signpost-split"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Routes</div>
                        <div class="fs-3 fw-bold">{{ $totalRoutes ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending SM Requests --}}
        <div class="col-md-4">
            <div class="card stat-card h-100 border-start border-4 border-danger">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Pending Requests</div>
                        <div class="fs-3 fw-bold">{{ $pendingRequests ?? 0 }}</div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pt-0">
                    <a href="{{ route('admin.station-master-requests') }}" class="text-danger text-decoration-none small fw-bold">
                        Review Requests →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card stat-card mt-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge-fill text-purple me-2"></i>Quick Actions</h5>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.trains.create') }}" class="btn btn-purple">
                    <i class="bi bi-plus-lg me-1"></i> Add Train
                </a>
                <a href="{{ route('admin.station-master-requests') }}" class="btn btn-outline-purple">
                    <i class="bi bi-person-check me-1"></i> Review SM Requests
                </a>
            </div>
        </div>
    </div>
@endsection