@props(['status'])

@if ($status)
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ $status }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif