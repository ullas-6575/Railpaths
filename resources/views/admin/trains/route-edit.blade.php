@extends('layouts.admin')

@section('title', 'Edit Route — ' . $train->name)

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.trains.index') }}">Trains</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.trains.routes.show', $train) }}">{{ $train->name }}</a></li>
            <li class="breadcrumb-item active">Edit Route</li>
        </ol>
    </nav>
    <h3 class="fw-bold text-dark mb-1">
        <i class="bi bi-pencil-square me-2 text-purple"></i>
        Edit Route: {{ $train->name }}
    </h3>
    <p class="text-muted mb-0">Drag stations to reorder. Drag from Available Stations to add. Edit times inline.</p>
</div>

<div class="row g-4">
    <!-- Available Stations Pool -->
    <div class="col-lg-3">
        <div class="card stat-card">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                <h6 class="fw-bold mb-1">
                    <i class="bi bi-collection me-2 text-primary"></i>Available Stations
                </h6>
                <small class="text-muted">Drag to add to route</small>
            </div>
            <div class="card-body pt-0" style="max-height: 600px; overflow-y: auto;">
                <div id="station-pool">
                    @forelse($availableStations as $station)
                        <div class="station-pool-card"
                             draggable="true"
                             data-station-id="{{ $station->id }}"
                             data-station-name="{{ $station->name }}"
                             data-station-code="{{ $station->code }}">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-grip-vertical me-2 text-muted"></i>
                                <div>
                                    <div class="fw-medium small">{{ $station->name }}</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $station->code }}</small>
                                </div>
                            </div>
                            <i class="bi bi-plus-circle text-success"></i>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3"></i>
                            <p class="mb-0 mt-2 small">All stations in use</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Route Builder -->
    <div class="col-lg-9">
        <form id="route-edit-form" action="{{ route('admin.trains.routes.update', $train) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card stat-card">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1">
                            <i class="bi bi-signpost-split me-2 text-purple"></i>Route Stations
                        </h6>
                        <small class="text-muted">Drag rows to reorder</small>
                    </div>
                    <span class="badge bg-secondary" id="station-count">{{ count($routeStations) }} stations</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle" id="route-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45px;" class="text-center">#</th>
                                    <th style="width: 40px;"></th>
                                    <th>Station</th>
                                    <th style="width: 130px;">Arrival</th>
                                    <th style="width: 130px;">Departure</th>
                                    <th style="width: 110px;">Dist (km)</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="route-stations-list">
                                @foreach($routeStations as $station)
                                    <tr class="route-station-row"
                                        draggable="true"
                                        data-station-id="{{ $station['id'] }}">
                                        <td class="text-center">
                                            <span class="badge bg-purple stop-number">{{ $loop->iteration }}</span>
                                        </td>
                                        <td class="drag-handle text-center">
                                            <i class="bi bi-grip-vertical text-muted"></i>
                                        </td>
                                        <td>
                                            <div class="fw-medium">{{ $station['name'] }}</div>
                                            <small class="text-muted">{{ $station['code'] }}</small>
                                            <input type="hidden" name="stations[{{ $loop->index }}][id]" value="{{ $station['id'] }}">
                                        </td>
                                        <td>
                                            <input type="time"
                                                   class="form-control form-control-sm"
                                                   name="stations[{{ $loop->index }}][arrival_time]"
                                                   value="{{ $station['arrival_time'] }}"
                                                   required>
                                        </td>
                                        <td>
                                            <input type="time"
                                                   class="form-control form-control-sm"
                                                   name="stations[{{ $loop->index }}][departure_time]"
                                                   value="{{ $station['departure_time'] }}"
                                                   required>
                                        </td>
                                        <td>
                                            <input type="number"
                                                   step="0.01"
                                                   min="0"
                                                   class="form-control form-control-sm"
                                                   name="stations[{{ $loop->index }}][distance_from_source]"
                                                   value="{{ $station['distance_from_source'] }}"
                                                   required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-link text-danger p-1 remove-station" title="Remove">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(count($routeStations) === 0)
                        <div class="text-center py-5 text-muted" id="empty-state">
                            <i class="bi bi-signpost fs-1"></i>
                            <p class="mt-2">Drag stations here to build the route</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center pt-0 pb-4 px-4">
                    <a href="{{ route('admin.trains.routes.show', $train) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </a>
                    <div>
                        <button type="button" class="btn btn-outline-danger btn-sm me-2" id="clear-all-btn">
                            <i class="bi bi-trash me-1"></i>Clear All
                        </button>
                        <button type="submit" class="btn btn-purple btn-sm">
                            <i class="bi bi-check-lg me-1"></i>Update Route
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .station-pool-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        margin-bottom: 8px;
        cursor: grab;
        transition: all 0.2s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .station-pool-card:hover {
        border-color: var(--rail-purple);
        box-shadow: 0 2px 8px rgba(124, 58, 237, 0.12);
        transform: translateY(-1px);
    }
    .station-pool-card.dragging {
        opacity: 0.5;
        cursor: grabbing;
    }

    .route-station-row {
        cursor: grab;
        transition: background-color 0.2s;
    }
    .route-station-row:hover {
        background-color: #f8fafc;
    }
    .route-station-row.dragging {
        opacity: 0.6;
        background-color: #ede9fe;
        outline: 2px dashed var(--rail-purple);
        outline-offset: -2px;
    }
    .route-station-row.drag-over {
        border-top: 3px solid var(--rail-purple) !important;
    }

    .drag-handle {
        cursor: grab;
    }
    .drag-handle:active {
        cursor: grabbing;
    }

    .stop-number {
        font-size: 0.85rem;
        padding: 0.35em 0.6em;
    }

    #empty-state { display: none; }
    #route-stations-list:empty + #empty-state { display: block; }
</style>
@endpush

@push('scripts')
<script>
(function() {
    'use strict';

    const stationPool = document.getElementById('station-pool');
    const routeList = document.getElementById('route-stations-list');
    const stationCount = document.getElementById('station-count');
    let draggedItem = null;
    let draggedFromPool = false;

    // ===== DRAG FROM POOL =====
    document.querySelectorAll('.station-pool-card').forEach(card => {
        card.addEventListener('dragstart', function(e) {
            draggedItem = this;
            draggedFromPool = true;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'copy';
            e.dataTransfer.setData('text/plain', JSON.stringify({
                id: this.dataset.stationId,
                name: this.dataset.stationName,
                code: this.dataset.stationCode
            }));
        });
        card.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            draggedItem = null;
            draggedFromPool = false;
        });
    });

    // ===== ROUTE ROW FUNCTIONS =====
    function initRouteRow(row) {
        row.addEventListener('dragstart', function(e) {
            draggedItem = this;
            draggedFromPool = false;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });
        row.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            document.querySelectorAll('.route-station-row').forEach(r => r.classList.remove('drag-over'));
            draggedItem = null;
            reindexStations();
        });
        row.addEventListener('dragover', function(e) {
            e.preventDefault();
            if (!draggedFromPool && draggedItem === this) return;
            this.classList.add('drag-over');
        });
        row.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });
        row.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');

            if (draggedFromPool) {
                const data = JSON.parse(e.dataTransfer.getData('text/plain'));
                const newRow = createStationRow(data);
                const rows = Array.from(routeList.querySelectorAll('.route-station-row'));
                const idx = rows.indexOf(this);
                if (idx === rows.length - 1) {
                    routeList.appendChild(newRow);
                } else {
                    routeList.insertBefore(newRow, rows[idx + 1]);
                }
                draggedItem.remove();
            } else {
                if (draggedItem === this) return;
                const rows = Array.from(routeList.querySelectorAll('.route-station-row'));
                const draggedIdx = rows.indexOf(draggedItem);
                const targetIdx = rows.indexOf(this);
                if (draggedIdx < targetIdx) {
                    this.after(draggedItem);
                } else {
                    this.before(draggedItem);
                }
            }
            reindexStations();
        });

        // Remove button
        row.querySelector('.remove-station').addEventListener('click', function() {
            const id = row.dataset.stationId;
            const name = row.querySelector('.fw-medium').textContent;
            const code = row.querySelector('small.text-muted').textContent;

            const poolCard = document.createElement('div');
            poolCard.className = 'station-pool-card';
            poolCard.draggable = true;
            poolCard.dataset.stationId = id;
            poolCard.dataset.stationName = name;
            poolCard.dataset.stationCode = code;
            poolCard.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-grip-vertical me-2 text-muted"></i>
                    <div>
                        <div class="fw-medium small">${name}</div>
                        <small class="text-muted" style="font-size: 0.7rem;">${code}</small>
                    </div>
                </div>
                <i class="bi bi-plus-circle text-success"></i>
            `;

            poolCard.addEventListener('dragstart', function(e) {
                draggedItem = this;
                draggedFromPool = true;
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'copy';
                e.dataTransfer.setData('text/plain', JSON.stringify({
                    id: this.dataset.stationId,
                    name: this.dataset.stationName,
                    code: this.dataset.stationCode
                }));
            });
            poolCard.addEventListener('dragend', function() {
                this.classList.remove('dragging');
                draggedItem = null;
                draggedFromPool = false;
            });

            stationPool.appendChild(poolCard);
            row.remove();
            reindexStations();
        });
    }

    document.querySelectorAll('.route-station-row').forEach(initRouteRow);

    // ===== CREATE NEW ROW =====
    function createStationRow(data) {
        const idx = routeList.querySelectorAll('.route-station-row').length;
        const row = document.createElement('tr');
        row.className = 'route-station-row';
        row.draggable = true;
        row.dataset.stationId = data.id;
        row.innerHTML = `
            <td class="text-center"><span class="badge bg-purple stop-number">${idx + 1}</span></td>
            <td class="drag-handle text-center"><i class="bi bi-grip-vertical text-muted"></i></td>
            <td>
                <div class="fw-medium">${data.name}</div>
                <small class="text-muted">${data.code}</small>
                <input type="hidden" name="stations[${idx}][id]" value="${data.id}">
            </td>
            <td><input type="time" class="form-control form-control-sm" name="stations[${idx}][arrival_time]" required></td>
            <td><input type="time" class="form-control form-control-sm" name="stations[${idx}][departure_time]" required></td>
            <td><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="stations[${idx}][distance_from_source]" required></td>
            <td class="text-center">
                <button type="button" class="btn btn-link text-danger p-1 remove-station"><i class="bi bi-trash3"></i></button>
            </td>
        `;
        initRouteRow(row);
        return row;
    }

    // ===== REINDEX =====
    function reindexStations() {
        const rows = routeList.querySelectorAll('.route-station-row');
        stationCount.textContent = rows.length + ' station' + (rows.length !== 1 ? 's' : '');

        rows.forEach((row, idx) => {
            row.querySelector('.stop-number').textContent = idx + 1;
            row.querySelectorAll('input[name^="stations["]').forEach(input => {
                const name = input.getAttribute('name');
                input.setAttribute('name', name.replace(/stations\[\d+\]/, `stations[${idx}]`));
            });
        });
    }

    // ===== DROP ON EMPTY LIST =====
    routeList.addEventListener('dragover', function(e) {
        if (routeList.querySelectorAll('.route-station-row').length === 0) e.preventDefault();
    });
    routeList.addEventListener('drop', function(e) {
        if (routeList.querySelectorAll('.route-station-row').length === 0 && draggedFromPool) {
            e.preventDefault();
            const data = JSON.parse(e.dataTransfer.getData('text/plain'));
            routeList.appendChild(createStationRow(data));
            draggedItem.remove();
            reindexStations();
        }
    });

    // ===== CLEAR ALL =====
    document.getElementById('clear-all-btn').addEventListener('click', function() {
        if (!confirm('Remove all stations from this route?')) return;

        routeList.querySelectorAll('.route-station-row').forEach(row => {
            const id = row.dataset.stationId;
            const name = row.querySelector('.fw-medium').textContent;
            const code = row.querySelector('small.text-muted').textContent;

            const poolCard = document.createElement('div');
            poolCard.className = 'station-pool-card';
            poolCard.draggable = true;
            poolCard.dataset.stationId = id;
            poolCard.dataset.stationName = name;
            poolCard.dataset.stationCode = code;
            poolCard.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-grip-vertical me-2 text-muted"></i>
                    <div>
                        <div class="fw-medium small">${name}</div>
                        <small class="text-muted" style="font-size: 0.7rem;">${code}</small>
                    </div>
                </div>
                <i class="bi bi-plus-circle text-success"></i>
            `;
            stationPool.appendChild(poolCard);
        });

        routeList.innerHTML = '';
        reindexStations();
    });

    // ===== FORM VALIDATION =====
    document.getElementById('route-edit-form').addEventListener('submit', function(e) {
        const rows = routeList.querySelectorAll('.route-station-row');
        if (rows.length < 2) {
            e.preventDefault();
            alert('A route must have at least 2 stations.');
            return false;
        }
        const ids = Array.from(rows).map(r => r.dataset.stationId);
        if ([...new Set(ids)].length !== ids.length) {
            e.preventDefault();
            alert('Each station can only appear once in a route.');
            return false;
        }
    });
})();
</script>
@endpush