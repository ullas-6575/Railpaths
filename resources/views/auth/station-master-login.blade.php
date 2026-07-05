<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Station Master Login - Track Rail</title>
    @include('partials.rail-auth-head')
</head>
<body>

    <div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">

                    <div class="text-center mb-4">
                        <div class="logo-circle green mx-auto mb-3">
                            <i class="bi bi-building text-white fs-1"></i>
                        </div>
                        <h2 class="fw-bold text-success mb-1" style="font-size: 1.5rem;">Station Master Portal</h2>
                        <p class="text-success fw-medium mb-0" style="font-size: 0.9rem; opacity: 0.8;">
                            Checkpoint Tracking System
                        </p>
                    </div>

                    <div class="card auth-card">
                        <div class="card-body p-4 p-md-5">

                            {{-- Back to Passenger --}}
                            <div class="mb-3">
                                <a href="{{ route('login') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-arrow-left"></i> Back to Passenger Login
                                </a>
                            </div>

                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('station-master.login') }}">
                                @csrf
                                <input type="hidden" name="role" value="station_master">

                                <div class="mb-3">
                                    <input type="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required
                                           autofocus
                                           autocomplete="username"
                                           placeholder="Station Master Email"
                                           class="form-control form-control-rail @error('email') is-invalid @enderror">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <input type="password"
                                           name="password"
                                           required
                                           autocomplete="current-password"
                                           placeholder="Password"
                                           class="form-control form-control-rail @error('password') is-invalid @enderror">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                        <label class="form-check-label text-muted small" for="remember">
                                            Remember me
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-rail-green w-100 py-3 fw-semibold fs-5 rounded-3">
                                    Station Master Login
                                </button>
                            </form>

                            <div class="text-center mt-4 pt-3 border-top">
                                <p class="text-muted mb-0">
                                    Don't have an account?
                                    <a href="{{ route('station-master.register') }}" class="text-decoration-none fw-bold text-success">
                                        Sign Up
                                    </a>
                                </p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
