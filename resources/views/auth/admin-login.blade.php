<x-guest-layout>
    <div class="auth-card">
        
        <div class="text-center mb-4">
            <div class="logo-circle purple mb-3">
                <i class="bi bi-shield-lock text-white fs-1"></i>
            </div>
            <h2 class="fw-bold mb-1" style="font-size: 1.5rem; color: var(--rail-purple);">Administration</h2>
            <p class="fw-medium mb-0" style="font-size: 0.9rem; color: var(--rail-purple); opacity: 0.8;">
                BD Railway Control Panel
            </p>
        </div>

        <div class="text-center mb-4">
            <span class="badge text-white px-3 py-2 fs-6" style="background-color: var(--rail-purple);">
                <i class="bi bi-shield-lock me-1"></i> Administrator Only
            </span>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="role" value="admin">

            <div class="mb-3">
                <x-input-label for="email" :value="__('Admin Email')" class="d-none" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Admin Email" />
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

            <button type="submit" class="btn btn-rail-purple w-100 py-3 fw-semibold fs-5 rounded-3 text-white">
                Admin Login
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left"></i> Back to main login
            </a>
        </div>

    </div>
</x-guest-layout>