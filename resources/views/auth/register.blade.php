<x-guest-layout>
    <div class="auth-card">
        
        <div class="text-center mb-4">
            <div class="logo-circle mb-3">
                <i class="bi bi-person-plus text-white fs-1"></i>
            </div>
            <h2 class="fw-bold text-dark mb-0" style="font-size: 1.5rem;">Create Account</h2>
        </div>

        {{-- Role Toggle --}}
        <div class="row g-2 mb-4">
            <div class="col-6">
                <a href="{{ route('register', ['role' => 'passenger']) }}" 
                   class="role-btn w-100 {{ $role === 'passenger' ? 'active-passenger' : 'border-secondary text-secondary' }}">
                    <i class="bi bi-person"></i>
                    <span>Passenger</span>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('register', ['role' => 'station_master']) }}" 
                   class="role-btn w-100 {{ $role === 'station_master' ? 'active-station' : 'border-secondary text-secondary' }}">
                    <i class="bi bi-building"></i>
                    <span>Station Master</span>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <input type="hidden" name="role" value="{{ $role ?? 'passenger' }}">

            {{-- Name --}}
            <div class="mb-3">
                <x-input-label for="name" :value="__('Name')" class="d-none" />
                <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Full Name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <x-input-label for="email" :value="__('Email')" class="d-none" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Email Address" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Phone --}}
            <div class="mb-3">
                <x-input-label for="phone" :value="__('Phone')" class="d-none" />
                <x-text-input id="phone" type="tel" name="phone" :value="old('phone')" placeholder="Phone Number (optional)" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            {{-- Station (Station Master Only) --}}
            @if($role === 'station_master')
                <div class="mb-3">
                    <label for="station_id" class="form-label fw-semibold text-success">Assigned station</label>
                    <select name="station_id" id="station_id" required
                            class="form-select form-control-rail {{ $errors->has('station_id') ? 'is-invalid' : '' }}">
                        <option value="">Select Assigned Station</option>
                        @foreach($stations as $station)
                            <option value="{{ $station->id }}" {{ old('station_id') == $station->id ? 'selected' : '' }}>
                                {{ $station->railOrder() }}. {{ $station->name }} ({{ $station->code }})
                            </option>
                        @endforeach
                    </select>
                    @if($stations->isEmpty())
                        <div class="alert alert-warning mt-2 mb-0 py-2 small"><i class="bi bi-exclamation-triangle me-1"></i>No stations are configured yet. Please contact the administrator.</div>
                    @endif
                    <x-input-error :messages="$errors->get('station_id')" class="mt-2" />
                </div>
            @endif

            {{-- Password --}}
            <div class="mb-3">
                <x-input-label for="password" :value="__('Password')" class="d-none" />
                <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Confirm Password --}}
            <div class="mb-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="d-none" />
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password" />
            </div>

            <button type="submit" 
                    class="btn w-100 py-3 fw-semibold fs-5 rounded-3 text-white
                    {{ $role === 'station_master' ? 'btn-rail-green' : 'btn-rail-blue' }}">
                {{ $role === 'station_master' ? 'Register as Station Master' : 'Create Passenger Account' }}
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top">
            <p class="text-muted mb-0">
                Already have an account? 
                <a href="{{ route('login', ['role' => $role ?? 'passenger']) }}" class="text-decoration-none fw-bold text-primary">
                    Log in
                </a>
            </p>
        </div>

    </div>
</x-guest-layout>
