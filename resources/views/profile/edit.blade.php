<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile - Track Rail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --rail-blue: #1d4ed8; --rail-blue-dark: #1e3a8a; }
        body { background-color: #f8f9fc; }
        .navbar-rail { background-color: var(--rail-blue); }
        .btn-rail-blue { background-color: var(--rail-blue); border-color: var(--rail-blue); color: white; }
        .btn-rail-blue:hover { background-color: var(--rail-blue-dark); color: white; }
        .profile-card { border: none; border-radius: 1rem; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
        .form-control-rail {
            background-color: #f3f4f6; border: none; padding: 0.85rem 1rem;
            border-radius: 0.75rem; font-size: 1rem;
        }
        .form-control-rail:focus {
            background-color: #ffffff;
            box-shadow: 0 0 0 0.2rem rgba(29, 78, 216, 0.15);
            border-color: var(--rail-blue);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark navbar-rail shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('dashboard') }}">
                <i class="bi bi-lightning-charge-fill"></i> Track Rail
            </a>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('dashboard') }}" class="text-white-50 text-decoration-none small">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light">
                        <i class="bi bi-box-arrow-right"></i> Log Out
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container py-5" style="max-width: 700px;">
        <h2 class="fw-bold mb-1">Profile Settings</h2>
        <p class="text-muted mb-4">Update your account information.</p>

        {{-- Success Message --}}
        @if(session('status') === 'profile-updated')
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle me-1"></i> Profile updated successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Profile Information --}}
        <div class="card profile-card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-person-circle me-2"></i>Profile Information</h5>
                
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium text-secondary mb-1">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                               class="form-control form-control-rail {{ $errors->has('name') ? 'is-invalid' : '' }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium text-secondary mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                               class="form-control form-control-rail {{ $errors->has('email') ? 'is-invalid' : '' }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-rail-blue px-4 rounded-3">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Update Password --}}
        <div class="card profile-card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-lock me-2"></i>Update Password</h5>
                
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-medium text-secondary mb-1">Current Password</label>
                        <input type="password" name="current_password" id="current_password"
                               class="form-control form-control-rail {{ $errors->updatePassword->has('current_password') ? 'is-invalid' : '' }}">
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium text-secondary mb-1">New Password</label>
                        <input type="password" name="password" id="password"
                               class="form-control form-control-rail {{ $errors->updatePassword->has('password') ? 'is-invalid' : '' }}">
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-medium text-secondary mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="form-control form-control-rail">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-rail-blue px-4 rounded-3">
                            <i class="bi bi-shield-check me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Delete Account --}}
        <div class="card profile-card border-danger border-opacity-25">
            <div class="card-body p-4">
                <h5 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-triangle me-2"></i>Danger Zone</h5>
                <p class="text-muted small mb-3">
                    Once your account is deleted, all of its resources and data will be permanently deleted.
                </p>
                
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <div class="mb-3">
                        <label for="delete_password" class="form-label fw-medium text-secondary mb-1">Confirm Password to Delete</label>
                        <input type="password" name="password" id="delete_password"
                               class="form-control form-control-rail {{ $errors->userDeletion->has('password') ? 'is-invalid' : '' }}"
                               placeholder="Enter your password to confirm">
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-danger rounded-3 px-4"
                            onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                        <i class="bi bi-trash me-1"></i> Delete Account
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
