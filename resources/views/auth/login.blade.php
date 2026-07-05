<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log In - Track Rail</title>
    @include('partials.rail-auth-head')
</head>
<body>

    <div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">

                    {{-- Logo --}}
                    <div class="text-center mb-4">
                        <div class="logo-circle mx-auto mb-3">
                            <i class="bi bi-lightning-charge-fill text-white fs-1"></i>
                        </div>
                        <h2 class="fw-bold text-primary mb-1" style="font-size: 1.5rem;">বাংলাদেশ রেলওয়ে</h2>
                        <p class="text-primary fw-medium mb-0" style="font-size: 0.9rem; opacity: 0.8;">
                            Bangladesh Railway Department
                        </p>
                    </div>

                    <div class="card auth-card">
                        <div class="card-body p-4 p-md-5">

                            {{-- Role Selection Tabs --}}
                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <a href="{{ route('login', ['role' => 'passenger']) }}"
                                       class="role-btn d-flex align-items-center justify-content-center gap-2 w-100
                                       {{ $role === 'passenger' ? 'active-passenger' : 'border-secondary text-secondary' }}">
                                        <i class="bi bi-person"></i>
                                        <span>Passenger</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('station-master.login') }}"
                                       class="role-btn d-flex align-items-center justify-content-center gap-2 w-100
                                       {{ $role === 'station_master' ? 'active-station' : 'border-secondary text-secondary' }}">
                                        <i class="bi bi-building"></i>
                                        <span>Station Master</span>
                                    </a>
                                </div>
                            </div>

                            {{-- Session Status --}}
                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <input type="hidden" name="role" value="{{ $role }}">

                                {{-- Email --}}
                                <div class="mb-3">
                                    <input type="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required
                                           autofocus
                                           autocomplete="username"
                                           placeholder="Email Address"
                                           class="form-control form-control-rail @error('email') is-invalid @enderror">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Password --}}
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

                                {{-- Remember + Forgot --}}
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                        <label class="form-check-label text-muted small" for="remember">
                                            Remember me
                                        </label>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-muted small text-decoration-none">
                                            Forgot Password?
                                        </a>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-rail-blue w-100 py-3 fw-semibold fs-5 rounded-3">
                                    Log In
                                </button>
                            </form>

                            {{-- Sign Up Link --}}
                            <div class="text-center mt-4 pt-3 border-top">
                                <p class="text-muted mb-0">
                                    Don't have an account?
                                    <a href="{{ route('register', ['role' => $role]) }}" class="text-decoration-none fw-bold text-primary">
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
