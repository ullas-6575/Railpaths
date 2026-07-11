<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Track Rail Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --rail-purple: #7c3aed; }
        body { background-color: #f8f9fc; }
        .navbar-admin { background-color: var(--rail-purple); }
        .stat-card { border: none; border-radius: 1rem; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
        .stat-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .bg-purple { background-color: var(--rail-purple) !important; }
        .text-purple { color: var(--rail-purple) !important; }
        .btn-purple { background-color: var(--rail-purple); color: white; border-color: var(--rail-purple); }
        .btn-purple:hover { background-color: #6d28d9; color: white; }
        .btn-outline-purple { color: var(--rail-purple); border-color: var(--rail-purple); }
        .btn-outline-purple:hover { background-color: var(--rail-purple); color: white; }
        .nav-link.active { background-color: rgba(124, 58, 237, 0.1); color: var(--rail-purple) !important; border-radius: 0.5rem; }
    </style>
    @stack('styles')
</head>
<body>

    <nav class="navbar navbar-dark navbar-admin shadow-sm sticky-top">
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

    <div class="container py-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3 mb-4">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.station-master-requests') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('admin.station-master-requests') ? 'active' : '' }}">
                        <i class="bi bi-person-check me-2"></i> SM Requests
                    </a>
                    <a href="{{ route('admin.trains.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('admin.trains.*') ? 'active' : '' }}">
                        <i class="bi bi-train-front me-2"></i> Trains & Routes
                    </a>
                    <a href="{{ route('admin.schedule.index') }}" class="list-group-item list-group-item-action border-0 {{ request()->routeIs('admin.schedule.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-week me-2"></i> Schedule
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10 col-md-9">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>