@extends('layouts.admin')

@section('title', $train->name . ' — Route')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.trains.index') }}">Trains</a></li>
            <li class="breadcrumb-item active">{{ $train->name }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-signpost-split me-2 text-purple"></i>
                {{ $train->name }} <small class="text-muted fs-5">({{ $train->train_number }})</small>
            </h3>
            <p class="text-muted mb-0">{{ $train->routes->count() }} stations in route</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.trains.routes.edit', $train) }}" class="btn btn-purple btn-sm">
                <i class="bi bi-pencil-square me-1"></i>Edit Route
            </a>
            <a href="{{ route('admin.trains.routes.create', $train) }}" class="btn btn-outline-purple btn-sm">
                <i class="bi bi-arrow-repeat me-1"></i>Rebuild
            </a>
        </div>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">Stop #</th>
                        <th>Station</th>
                        <th>Code</th>
                        <th>Arrival</th>
                        <th>Departure</th>
                        <th>Distance (km)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($train->routes as $station)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-purple">{{ $station->pivot->stop_order }}</span>
                            </td>
                            <td class="fw-medium">{{ $station->name }}</td>
                            <td><code>{{ $station->code }}</code></td>
                            <td><span class="font-monospace">{{ $station->pivot->arrival_time }}</span></td>
                            <td><span class="font-monospace">{{ $station->pivot->departure_time }}</span></td>
                            <td>{{ $station->pivot->distance_from_source }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-signpost fs-3 d-block mb-2"></i>
                                No route defined.
                                <a href="{{ route('admin.trains.routes.create', $train) }}" class="text-purple fw-semibold">Build one now</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection