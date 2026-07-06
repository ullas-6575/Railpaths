<x-guest-layout>
    <div class="auth-card">
        
        <div class="text-center mb-4">
            <div class="logo-circle green mb-3">
                <i class="bi bi-hourglass-split text-white fs-1"></i>
            </div>
            <h2 class="fw-bold text-success mb-1" style="font-size: 1.5rem;">Application Pending</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Your Station Master request has been submitted
            </p>
        </div>

        <div class="alert alert-info border-0 rounded-3" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            Your application is currently under review by the administration. 
            You will receive an email notification once your request has been processed.
        </div>

        <div class="text-center mt-4">
            <p class="text-muted small mb-3">
                This process typically takes 1–2 business days.
            </p>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-3 px-4">
                <i class="bi bi-arrow-left me-1"></i> Back to Home
            </a>
        </div>

    </div>
</x-guest-layout>
