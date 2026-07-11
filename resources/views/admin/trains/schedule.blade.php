@extends('layouts.admin')

@section('title', 'Train Schedule Calendar')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold text-dark mb-1">
        <i class="bi bi-calendar-week me-2 text-purple"></i>
        Train Schedule Calendar
    </h3>
    <p class="text-muted mb-0">View train schedules across stations in a calendar format.</p>
</div>

<!-- Train Selector -->
<div class="row mb-4">
    <div class="col-md-5 col-lg-4">
        <div class="card stat-card">
            <div class="card-body">
                <label class="form-label fw-semibold small text-muted mb-2">
                    <i class="bi bi-train-front me-1"></i>Select Train
                </label>
                <form method="GET" action="{{ route('admin.schedule.index') }}">
                    <div class="input-group">
                        <select name="train_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">-- Choose a Train --</option>
                            @foreach($trains as $train)
                                <option value="{{ $train->id }}" {{ $selectedTrainId == $train->id ? 'selected' : '' }}>
                                    {{ $train->train_number }} — {{ $train->name }}
                                    ({{ $train->routes_count }} stops)
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-purple btn-sm">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($selectedTrain)
    <div class="col-md-7 col-lg-8">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center flex-wrap gap-4">
                <div>
                    <div class="text-muted small">Train</div>
                    <div class="fw-bold">{{ $selectedTrain->name }}</div>
                    <div class="text-muted small">{{ $selectedTrain->train_number }}</div>
                </div>
                <div>
                    <div class="text-muted small">Route</div>
                    <div class="fw-semibold">
                        @if($selectedTrain->routes->count() > 0)
                            {{ $selectedTrain->routes->first()->name }}
                            <i class="bi bi-arrow-right mx-1 text-muted"></i>
                            {{ $selectedTrain->routes->last()->name }}
                        @else
                            <span class="text-warning">No route</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-muted small">Stops</div>
                    <div class="fw-bold fs-4 text-purple">{{ $selectedTrain->routes->count() }}</div>
                </div>
                <div class="ms-md-auto">
                    <a href="{{ route('admin.trains.routes.edit', $selectedTrain) }}" class="btn btn-outline-purple btn-sm">
                        <i class="bi bi-pencil-square me-1"></i>Edit Route
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@if($selectedTrain && $calendarData)
    <!-- Calendar Navigation -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.schedule.index', ['train_id' => $selectedTrain->id, 'month' => $calendarData['prev_month']]) }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-chevron-left me-1"></i>{{ \Carbon\Carbon::parse($calendarData['prev_month'])->format('M Y') }}
        </a>
        <h5 class="mb-0 fw-bold">{{ $calendarData['month_name'] }}</h5>
        <a href="{{ route('admin.schedule.index', ['train_id' => $selectedTrain->id, 'month' => $calendarData['next_month']]) }}"
           class="btn btn-outline-secondary btn-sm">
            {{ \Carbon\Carbon::parse($calendarData['next_month'])->format('M Y') }}<i class="bi bi-chevron-right ms-1"></i>
        </a>
    </div>

    <!-- Calendar Grid -->
    <div class="card stat-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 calendar-table">
                    <thead class="text-center">
                        <tr class="table-light">
                            <th style="width: 14.28%">Sun</th>
                            <th style="width: 14.28%">Mon</th>
                            <th style="width: 14.28%">Tue</th>
                            <th style="width: 14.28%">Wed</th>
                            <th style="width: 14.28%">Thu</th>
                            <th style="width: 14.28%">Fri</th>
                            <th style="width: 14.28%">Sat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($calendarData['weeks'] as $week)
                            <tr>
                                @foreach($week as $day)
                                    <td class="calendar-day {{ !$day['is_current_month'] ? 'text-muted bg-light' : '' }} {{ $day['is_today'] ? 'bg-info bg-opacity-10' : '' }}"
                                        data-date="{{ $day['date'] }}"
                                        onclick="showDayDetail('{{ $day['date'] }}')">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <span class="fw-semibold small {{ $day['is_today'] ? 'badge bg-purple rounded-pill' : '' }}">
                                                {{ $day['day'] }}
                                            </span>
                                            @if(count($day['events']) > 0)
                                                <span class="badge bg-success bg-opacity-75" style="font-size: 0.6rem;">{{ count($day['events']) }}</span>
                                            @endif
                                        </div>
                                        @if(count($day['events']) > 0)
                                            <div class="schedule-mini">
                                                <div class="text-truncate small" style="font-size: 0.7rem;">
                                                    <i class="bi bi-geo-alt-fill text-success me-1"></i>
                                                    {{ $day['events'][0]['station_name'] }}
                                                </div>
                                                @if(count($day['events']) > 1)
                                                    <div class="text-truncate text-muted" style="font-size: 0.65rem;">
                                                        <i class="bi bi-arrow-down-short"></i>
                                                        {{ $day['events'][count($day['events'])-1]['station_name'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@elseif($selectedTrain)
    <div class="card stat-card">
        <div class="card-body text-center py-5">
            <i class="bi bi-signpost fs-1 text-muted"></i>
            <h5 class="mt-3 text-muted">No route defined for this train</h5>
            <a href="{{ route('admin.trains.routes.create', $selectedTrain) }}" class="btn btn-purple btn-sm mt-2">
                <i class="bi bi-plus-lg me-1"></i>Build Route
            </a>
        </div>
    </div>
@else
    <div class="card stat-card">
        <div class="card-body text-center py-5">
            <i class="bi bi-calendar-x fs-1 text-muted"></i>
            <h5 class="mt-3 text-muted">Select a train to view its schedule</h5>
        </div>
    </div>
@endif

<!-- Day Detail Modal -->
<div class="modal fade" id="dayDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--rail-purple);">
                <h5 class="modal-title text-white">
                    <i class="bi bi-calendar-event me-2"></i>
                    <span id="modal-date"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modal-loading" class="text-center py-4">
                    <div class="spinner-border text-purple" role="status"></div>
                    <p class="mt-2 text-muted small">Loading schedule...</p>
                </div>
                <div id="modal-content" class="d-none"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .calendar-table { table-layout: fixed; }
    .calendar-table th {
        font-weight: 600;
        color: #475569;
        padding: 10px;
        font-size: 0.85rem;
    }
    .calendar-day {
        height: 110px;
        vertical-align: top;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 6px;
    }
    .calendar-day:hover {
        background-color: #ede9fe !important;
    }
    .schedule-mini {
        margin-top: 2px;
    }

    /* Timeline */
    .timeline {
        position: relative;
        padding-left: 28px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, var(--rail-purple), #a78bfa, var(--rail-purple));
    }
    .timeline-item {
        position: relative;
        padding-bottom: 16px;
    }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-dot {
        position: absolute;
        left: -24px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px var(--rail-purple);
    }
    .timeline-dot.source {
        background-color: #10b981;
        box-shadow: 0 0 0 2px #10b981;
    }
    .timeline-dot.destination {
        background-color: #ef4444;
        box-shadow: 0 0 0 2px #ef4444;
    }
    .timeline-dot.stop {
        background-color: var(--rail-purple);
    }
    .timeline-content {
        background: #f8fafc;
        border-radius: 8px;
        padding: 10px 14px;
        border-left: 3px solid var(--rail-purple);
    }
    .timeline-content.source { border-left-color: #10b981; }
    .timeline-content.destination { border-left-color: #ef4444; }
    .time-badge {
        font-family: 'SF Mono', monospace;
        font-size: 0.8rem;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .calendar-day { height: 70px; padding: 3px; }
        .schedule-mini { display: none; }
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    const modalDate = document.getElementById('modal-date');
    const modalLoading = document.getElementById('modal-loading');
    const modalContent = document.getElementById('modal-content');
    const bsModal = new bootstrap.Modal(document.getElementById('dayDetailModal'));

    window.showDayDetail = function(date) {
        modalDate.textContent = new Date(date).toLocaleDateString('en-US', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
        modalLoading.classList.remove('d-none');
        modalContent.classList.add('d-none');
        bsModal.show();

        fetch(`{{ route('admin.schedule.api', $selectedTrain->id ?? 0) }}?date=${date}`)
            .then(r => r.json())
            .then(data => {
                renderTimeline(data.events);
                modalLoading.classList.add('d-none');
                modalContent.classList.remove('d-none');
            })
            .catch(() => {
                modalContent.innerHTML = '<div class="alert alert-danger small"><i class="bi bi-exclamation-triangle me-2"></i>Failed to load schedule.</div>';
                modalLoading.classList.add('d-none');
                modalContent.classList.remove('d-none');
            });
    };

    function renderTimeline(events) {
        if (events.length === 0) {
            modalContent.innerHTML = '<div class="text-center py-4 text-muted"><i class="bi bi-calendar-x fs-1"></i><p class="mt-2 small">No scheduled stops.</p></div>';
            return;
        }

        let html = '<div class="timeline">';
        events.forEach((e, i) => {
            const dotClass = e.is_source ? 'source' : (e.is_destination ? 'destination' : 'stop');
            const contentClass = e.is_source ? 'source' : (e.is_destination ? 'destination' : '');
            const label = e.is_source ? '<span class="badge bg-success mb-1" style="font-size:0.65rem;">SOURCE</span>' :
                         (e.is_destination ? '<span class="badge bg-danger mb-1" style="font-size:0.65rem;">DESTINATION</span>' :
                          `<span class="badge bg-secondary mb-1" style="font-size:0.65rem;">STOP #${e.stop_order}</span>`);

            html += `
                <div class="timeline-item">
                    <div class="timeline-dot ${dotClass}"></div>
                    <div class="timeline-content ${contentClass}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                ${label}
                                <h6 class="mb-0 fw-bold" style="font-size:0.9rem;">${e.station_name}</h6>
                                <small class="text-muted" style="font-size:0.75rem;">${e.station_code}</small>
                            </div>
                            <div class="text-end">
                                <div class="time-badge text-purple"><i class="bi bi-clock me-1"></i>${e.arrival_time}</div>
                                ${!e.is_destination ? `<div class="time-badge text-success mt-1" style="font-size:0.7rem;"><i class="bi bi-box-arrow-right me-1"></i>${e.departure_time}</div>` : ''}
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-top">
                            <small class="text-muted" style="font-size:0.7rem;">
                                <i class="bi bi-rulers me-1"></i>${e.distance_from_source} km from source
                            </small>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        modalContent.innerHTML = html;
    }
})();
</script>
@endpush