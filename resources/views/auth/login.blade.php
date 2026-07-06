<x-guest-layout>
    <div class="auth-card">
        
        {{-- Logo (Like Image 1) --}}
        <div class="text-center mb-4">
            <div class="logo-circle mb-3">
                <i class="bi bi-lightning-charge-fill text-white fs-1"></i>
            </div>
            <h2 class="fw-bold text-primary mb-1" style="font-size: 1.5rem;">বাংলাদেশ রেলওয়ে</h2>
            <p class="text-primary fw-medium mb-0" style="font-size: 0.9rem; opacity: 0.8;">
                Bangladesh Railway Department
            </p>
        </div>

        {{-- Role Selection (Like Social Login in Image 2) --}}
        @if(!$isAdmin)
            <div class="row g-2 mb-4">
                <div class="col-6">
                    <a href="{{ route('login', ['role' => 'passenger']) }}" 
                       class="role-btn w-100 {{ $role === 'passenger' ? 'active-passenger' : 'border-secondary text-secondary' }}">
                        <i class="bi bi-person"></i>
                        <span>Passenger</span>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('station-master.login') }}" 
                       class="role-btn w-100 {{ $role === 'station_master' ? 'active-station' : 'border-secondary text-secondary' }}">
                        <i class="bi bi-building"></i>
                        <span>Station Master</span>
                    </a>
                </div>
            </div>
        @else
            <div class="text-center mb-4">
                <span class="badge text-white px-3 py-2 fs-6" style="background-color: var(--rail-purple);">
                    <i class="bi bi-shield-lock me-1"></i> Administrator Access
                </span>
            </div>
        @endif

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf
            @if($role && !$isAdmin)
                <input type="hidden" name="role" value="{{ $role }}">
            @endif

            {{-- Email --}}
            <div class="mb-3">
                <x-input-label for="email" :value="__('Email')" class="d-none" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Email Address" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <x-input-label for="password" :value="__('Password')" class="d-none" />
                <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Remember + Forgot --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label text-muted small" for="remember">Remember me</label>
                </div>
                @if (Route::has('password.request'))
                    <a class="text-muted small text-decoration-none" href="{{ route('password.request') }}">
                        Forgot Password?
                    </a>
                @endif
            </div>

            <x-primary-button>
                {{ __('Log In') }}
            </x-primary-button>
        </form>

        {{-- Sign Up Link --}}
        <div class="text-center mt-4 pt-3 border-top">
            <p class="text-muted mb-0">
                Don't have an account? 
                <a href="{{ route('register', ['role' => $role ?? 'passenger']) }}" class="text-decoration-none fw-bold text-primary">
                    Sign Up
                </a>
            </p>
        </div>

    </div>
</x-guest-layout>