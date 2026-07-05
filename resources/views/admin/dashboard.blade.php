<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Track Rail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --rail-purple: #7c3aed; }
        body { background-color: #f8f9fc; }
        .navbar-admin { background-color: var(--rail-purple); }
        .stat-card { border: none; border-radius: 1rem; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
        .stat-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark navbar-admin shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-shield-lock-fill"></i> Track Rail Admin
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small">{{ auth()->user()->name }}</span>
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
        <h2 class="fw-bold mb-1">Welcome back, {{ auth()->user()->name }}</h2>
        <p class="text-muted mb-4">Here's what's happening across Track Rail.</p>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Total Users</div>
                            <div class="fs-3 fw-bold">{{ $totalUsers }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Passengers</div>
                            <div class="fs-3 fw-bold">{{ $totalPassengers }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Station Masters</div>
                            <div class="fs-3 fw-bold">{{ $totalStationMasters }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card stat-card mt-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Next steps</h5>
                <p class="text-muted mb-0">
                    This is the admin dashboard shell. Add station management, train scheduling,
                    and user administration tools here as the project grows.
                </p>
            </div>
        </div>
    </div>

</body>
</html>
