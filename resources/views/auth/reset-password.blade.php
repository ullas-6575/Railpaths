<x-guest-layout>
    <div class="auth-card">
        
        <div class="text-center mb-4">
            <div class="logo-circle mb-3">
                <i class="bi bi-shield-lock text-white fs-1"></i>
            </div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.5rem;">Reset Password</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Enter your new password below
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email -->
            <div class="mb-3">
                <x-input-label for="email" :value="__('Email')" class="d-none" />
                <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="Email Address" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-3">
                <x-input-label for="password" :value="__('Password')" class="d-none" />
                <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="New Password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="d-none" />
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm New Password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </form>

    </div>
</x-guest-layout>
