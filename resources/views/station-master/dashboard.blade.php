<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Station Master Dashboard - Track Rail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fc; }
        .navbar-station { background-color: #198754; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark navbar-station shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('station-master.dashboard') }}">
                <i class="bi bi-building"></i> Track Rail - Station Master
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small">{{ $user->name }}</span>
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
        <h2 class="fw-bold mb-1">Welcome, {{ $user->name }}</h2>
        <p class="text-muted mb-4">Manage live departures and delays for your station here.</p>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Next steps</h5>
                <p class="text-muted mb-0">
                    This is the station master dashboard shell. Add departure updates,
                    delay reporting, and station-specific tools here as the project grows.
                </p>
            </div>
        </div>
    </div>

</body>
</html>
