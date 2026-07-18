@extends('layouts.admin')

@section('title', 'Station Log Statistics')

@section('content')
    <h2 class="fw-bold mb-1">Station Log Statistics</h2>
    <p class="text-muted mb-4">Today's train performance overview.</p>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-clipboard-data"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Logs Today</div>
                        <div class="fs-3 fw-bold">{{ $stats['total_logs_today'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Delayed Trains</div>
                        <div class="fs-3 fw-bold">{{ $stats['delayed_trains'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Avg Delay (min)</div>
                        <div class="fs-3 fw-bold">{{ number_format($stats['avg_delay'], 1) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($stats['station_wise']->isNotEmpty())
    <div class="card stat-card">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="bi bi-building me-2 text-purple"></i>Station-wise Breakdown</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Station</th>
                            <th>Total Logs</th>
                            <th>Delayed</th>
                            <th>On-time Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['station_wise'] as $row)
                        <tr>
                            <td class="fw-medium">{{ $row->station->name ?? 'N/A' }}</td>
                            <td>{{ $row->total }}</td>
                            <td>
                                @if($row->delayed > 0)
                                    <span class="badge bg-danger">{{ $row->delayed }}</span>
                                @else
                                    <span class="badge bg-success">0</span>
                                @endif
                            </td>
                            <td>
                                @php $onTime = $row->total > 0 ? round((($row->total - $row->delayed) / $row->total) * 100) : 100; @endphp
                                <div class="progress" style="height: 8px; width: 120px;">
                                    <div class="progress-bar {{ $onTime >= 80 ? 'bg-success' : ($onTime >= 50 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $onTime }}%"></div>
                                </div>
                                <small class="text-muted">{{ $onTime }}%</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="card stat-card">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            No station logs recorded today.
        </div>
    </div>
    @endif

    <div class="mt-3">
        <a href="{{ route('admin.station-logs.index') }}" class="btn btn-outline-purple">
            <i class="bi bi-arrow-left me-1"></i> Back to All Logs
        </a>
    </div>
@endsection
