<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track Rail - Bangladesh Railway Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --rail-blue: #1d4ed8; --rail-blue-dark: #1e3a8a; }
        .btn-rail-blue { background-color: var(--rail-blue); border-color: var(--rail-blue); color: white; }
        .btn-rail-blue:hover { background-color: var(--rail-blue-dark); color: white; }
        .hero-section { padding: 5rem 0; }
        .feature-icon { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    </style>
</head>
<body class="bg-light">

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark" href="{{ url('/') }}">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                Track Rail
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link text-secondary fw-medium">Log in</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a href="{{ route('register') }}" class="btn btn-rail-blue px-4 fw-medium">Sign Up</a>
                            </li>
                        @endif
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="hero-section bg-light">
        <div class="container text-center">
            <h1 class="display-4 fw-bold text-dark mb-3">
                Never Wait on a Platform<br>
                <span class="text-primary">Again</span>
            </h1>
            <p class="lead text-muted mx-auto" style="max-width: 600px;">
                Real-time train tracking for Bangladesh Railway. Check delays, book tickets, and get SMS alerts — completely free.
            </p>
            <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-rail-blue btn-lg px-5 fw-semibold">
                    Get Started as Passenger
                </a>
                <a href="{{ route('station-master.login') }}" class="btn btn-outline-dark btn-lg px-5 fw-semibold">
                    Station Master Portal
                </a>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-4 bg-light rounded-4 h-100">
                        <div class="feature-icon bg-primary bg-opacity-10 text-primary mb-3">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h5 class="fw-bold">Live Delay Tracking</h5>
                        <p class="text-muted mb-0">Station Masters update departures in real-time. See exact delays before you leave home.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-light rounded-4 h-100">
                        <div class="feature-icon bg-success bg-opacity-10 text-success mb-3">
                            <i class="bi bi-ticket-perforated"></i>
                        </div>
                        <h5 class="fw-bold">Smart Ticketing</h5>
                        <p class="text-muted mb-0">Book tickets with dynamic seat inventory. Cancel up to 2 hours before departure.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-light rounded-4 h-100">
                        <div class="feature-icon bg-warning bg-opacity-10 text-warning mb-3">
                            <i class="bi bi-phone-vibrate"></i>
                        </div>
                        <h5 class="fw-bold">SMS Alerts</h5>
                        <p class="text-muted mb-0">Get instant SMS notifications when your train is delayed by more than 15 minutes.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>