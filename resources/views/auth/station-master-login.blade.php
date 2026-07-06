<x-guest-layout>
    <div class="auth-card">
        
        <div class="text-center mb-4">
            <div class="logo-circle green mb-3">
                <i class="bi bi-building text-white fs-1"></i>
            </div>
            <h2 class="fw-bold text-success mb-1" style="font-size: 1.5rem;">Station Master Portal</h2>
            <p class="text-success fw-medium mb-0" style="font-size: 0.9rem; opacity: 0.8;">
                Checkpoint Tracking System
            </p>
        </div>

        <div class="mb-3">
            <a href="{{ route('login') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Back to Passenger Login
            </a>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="role" value="station_master">

            <div class="mb-3">
                <x-input-label for="email" :value="__('Email')" class="d-none" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Station Master Email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mb-3">
                <x-input-label for="password" :value="__('Password')" class="d-none" />
                <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label text-muted small" for="remember">Remember me</label>
                </div>
            </div>

            <button type="submit" class="btn btn-rail-green w-100 py-3 fw-semibold fs-5 rounded-3 text-white">
                Station Master Login
            </button>
        </form>

    </div>
</x-guest-layout>