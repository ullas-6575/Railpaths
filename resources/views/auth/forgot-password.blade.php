<x-guest-layout>
    <div class="auth-card">
        
        <div class="text-center mb-4">
            <div class="logo-circle mb-3">
                <i class="bi bi-key text-white fs-1"></i>
            </div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.5rem;">Forgot Password?</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Enter your email and we'll send you a reset link
            </p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" class="d-none" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="Email Address" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <x-primary-button>
                {{ __('Send Reset Link') }}
            </x-primary-button>
        </form>

        <div class="text-center mt-4 pt-3 border-top">
            <a href="{{ route('login') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i> Back to Login
            </a>
        </div>

    </div>
</x-guest-layout>
