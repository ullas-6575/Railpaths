@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-purple fw-bold mb-0">🗺️ Routes for {{ $train->name }}</h2>
            <small class="text-muted">Train #{{ $train->train_number }}</small>
        </div>
        <div>
            <a href="{{ route('admin.trains.routes.create', $train) }}" class="btn btn-purple">
                <i class="bi bi-plus-lg"></i> Build New Route
            </a>
            <a href="{{ route('admin.trains.index') }}" class="btn btn-outline-secondary ms-2">Back to Trains</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @forelse($train->routes as $route)
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">{{ $route->route_name }}</h5>
                <small class="text-muted">
                    Departure: {{ $route->departure_time }} | Arrival: {{ $route->arrival_time }}
                </small>
            </div>
            <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this route?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash"></i> Delete Route
                </button>
            </form>
        </div>
        <div class="card-body">
            @if($route->stations->count())
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px">Stop #</th>
                            <th>Station</th>
                            <th>City</th>
                            <th>Arrival</th>
                            <th>Departure</th>
                            <th>Distance (km)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($route->stations as $station)
                        <tr>
                            <td class="text-center fw-bold">{{ $station->pivot->stop_order }}</td>
                            <td>{{ $station->name }} <small class="text-muted">({{ $station->code }})</small></td>
                            <td>{{ $station->city }}</td>
                            <td>{{ $station->pivot->arrival_time ?? '-' }}</td>
                            <td>{{ $station->pivot->departure_time ?? '-' }}</td>
                            <td>{{ $station->pivot->distance_from_source }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted mb-0">No stations added to this route yet.</p>
            @endif
        </div>
    </div>
    @empty
    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-map display-4 mb-3 d-block"></i>
            <h5>No routes configured yet</h5>
            <p>Click "Build New Route" to create a route with drag-drop station ordering.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
