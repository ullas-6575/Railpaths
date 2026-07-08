@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-purple fw-bold mb-0">🔧 Route Builder</h2>
            <small class="text-muted">{{ $train->name }} (#{{ $train->train_number }})</small>
        </div>
        <a href="{{ route('admin.trains.routes', $train) }}" class="btn btn-outline-secondary">Back to Routes</a>
    </div>

    <div class="row">
        <!-- Available Stations Sidebar -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-purple text-white">
                    <h6 class="mb-0"><i class="bi bi-geo-alt"></i> Available Stations</h6>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    <div class="list-group list-group-flush" id="station-pool">
                        @foreach($stations as $station)
                        <div class="list-group-item station-source d-flex align-items-center" 
                             draggable="true" 
                             data-station-id="{{ $station->id }}"
                             data-station-name="{{ $station->name }}"
                             data-station-code="{{ $station->code }}"
                             data-station-city="{{ $station->city }}">
                            <i class="bi bi-grip-vertical text-muted me-2"></i>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $station->name }}</div>
                                <small class="text-muted">{{ $station->city }} • {{ $station->code }}</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-purple add-station-btn">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Drag stations to the right or click + to add
                    </small>
                </div>
            </div>
        </div>

        <!-- Route Builder Canvas -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-purple text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-signpost-split"></i> Route Configuration</h6>
                    <span class="badge bg-white text-purple" id="station-count">0 stops</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.trains.routes.store', $train) }}" method="POST" id="route-form">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold">Route Name</label>
                                <input type="text" name="route_name" class="form-control" placeholder="e.g., Delhi - Mumbai Central" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label fw-bold">Departure Time</label>
                                <input type="time" name="departure_time" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label fw-bold">Arrival Time</label>
                                <input type="time" name="arrival_time" class="form-control" required>
                            </div>
                        </div>

                        <hr class="my-3">

                        <label class="form-label fw-bold d-block">Stops (Drag to reorder)</label>
                        
                        <!-- Drop Zone / Sortable List -->
                        <div id="route-stops" class="list-group mb-3 min-vh-50 border rounded p-2 bg-light">
                            <div class="text-center text-muted py-5" id="empty-state">
                                <i class="bi bi-arrow-left display-6"></i>
                                <p class="mt-2">Drag stations here or click the + button</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-danger" id="clear-all">
                                <i class="bi bi-trash"></i> Clear All
                            </button>
                            <button type="submit" class="btn btn-purple btn-lg" id="save-route" disabled>
                                <i class="bi bi-check-lg"></i> Save Route
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.min-vh-50 { min-height: 200px; }
.station-source { cursor: grab; transition: all 0.2s; }
.station-source:hover { background-color: #f8f9fa; }
.station-source.dragging { opacity: 0.5; cursor: grabbing; }
.route-stop { 
    cursor: grab; 
    background: white; 
    border-left: 4px solid #6f42c1 !important;
    margin-bottom: 0.5rem;
    border-radius: 0.375rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.route-stop.dragging { opacity: 0.5; }
.route-stop .stop-number {
    background: #6f42c1;
    color: white;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: bold;
    font-size: 0.85rem;
}
.remove-stop { cursor: pointer; }
.remove-stop:hover { color: #dc3545; }
#route-stops.drag-over { background-color: #e9ecef; border-style: dashed; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stationPool = document.getElementById('station-pool');
    const routeStops = document.getElementById('route-stops');
    const emptyState = document.getElementById('empty-state');
    const stationCount = document.getElementById('station-count');
    const saveBtn = document.getElementById('save-route');
    const clearBtn = document.getElementById('clear-all');
    let draggedItem = null;
    let stopCounter = 0;

    // Update stop numbers and hidden inputs
    function updateStops() {
        const stops = routeStops.querySelectorAll('.route-stop');
        stops.forEach((stop, index) => {
            stop.querySelector('.stop-number').textContent = index + 1;
            stop.querySelector('.stop-order-input').value = index + 1;
        });
        
        stationCount.textContent = stops.length + ' stop' + (stops.length !== 1 ? 's' : '');
        saveBtn.disabled = stops.length < 2;
        
        if (stops.length === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }
    }

    // Create route stop HTML
    function createRouteStop(stationData) {
        stopCounter++;
        const div = document.createElement('div');
        div.className = 'list-group-item route-stop p-3';
        div.draggable = true;
        div.dataset.stationId = stationData.id;
        
        div.innerHTML = `
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-grip-vertical text-muted"></i>
                <div class="stop-number">1</div>
                <div class="flex-grow-1">
                    <div class="fw-bold">${stationData.name} <small class="text-muted">(${stationData.code})</small></div>
                    <small class="text-muted">${stationData.city}</small>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <input type="time" class="form-control form-control-sm" style="width:110px" 
                           name="stations[${stopCounter}][arrival_time]" placeholder="Arrival">
                    <input type="time" class="form-control form-control-sm" style="width:110px" 
                           name="stations[${stopCounter}][departure_time]" placeholder="Departure">
                    <input type="number" class="form-control form-control-sm" style="width:80px" 
                           name="stations[${stopCounter}][distance]" placeholder="km" min="0" required>
                    <input type="hidden" name="stations[${stopCounter}][station_id]" value="${stationData.id}">
                    <input type="hidden" class="stop-order-input" name="stations[${stopCounter}][stop_order]" value="1">
                    <i class="bi bi-x-lg text-danger remove-stop ms-2" title="Remove"></i>
                </div>
            </div>
        `;

        // Drag events for route stop
        div.addEventListener('dragstart', function(e) {
            draggedItem = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });

        div.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            draggedItem = null;
            updateStops();
        });

        // Remove button
        div.querySelector('.remove-stop').addEventListener('click', function() {
            div.remove();
            updateStops();
        });

        return div;
    }

    // Add station via + button
    stationPool.querySelectorAll('.add-station-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const item = this.closest('.station-source');
            const data = {
                id: item.dataset.stationId,
                name: item.dataset.stationName,
                code: item.dataset.stationCode,
                city: item.dataset.stationCity
            };
            routeStops.appendChild(createRouteStop(data));
            updateStops();
        });
    });

    // Drag from station pool
    stationPool.querySelectorAll('.station-source').forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedItem = this;
            this.classList.add('dragging');
            e.dataTransfer.setData('text/plain', JSON.stringify({
                id: this.dataset.stationId,
                name: this.dataset.stationName,
                code: this.dataset.stationCode,
                city: this.dataset.stationCity
            }));
            e.dataTransfer.effectAllowed = 'copy';
        });

        item.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            draggedItem = null;
        });
    });

    // Drop zone events
    routeStops.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('drag-over');
        
        const afterElement = getDragAfterElement(routeStops, e.clientY);
        if (draggedItem && draggedItem.classList.contains('route-stop')) {
            if (afterElement == null) {
                routeStops.appendChild(draggedItem);
            } else {
                routeStops.insertBefore(draggedItem, afterElement);
            }
        }
    });

    routeStops.addEventListener('dragleave', function() {
        this.classList.remove('drag-over');
    });

    routeStops.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('drag-over');
        
        // If dropping from station pool
        if (draggedItem && draggedItem.classList.contains('station-source')) {
            try {
                const data = JSON.parse(e.dataTransfer.getData('text/plain'));
                const afterElement = getDragAfterElement(routeStops, e.clientY);
                const newStop = createRouteStop(data);
                if (afterElement == null) {
                    routeStops.appendChild(newStop);
                } else {
                    routeStops.insertBefore(newStop, afterElement);
                }
                updateStops();
            } catch(err) {
                console.error('Drop error:', err);
            }
        }
    });

    // Helper: find element to insert before
    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.route-stop:not(.dragging)')];
        
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    // Clear all
    clearBtn.addEventListener('click', function() {
        if (confirm('Clear all stations from this route?')) {
            routeStops.querySelectorAll('.route-stop').forEach(el => el.remove());
            updateStops();
        }
    });

    // Form validation before submit
    document.getElementById('route-form').addEventListener('submit', function(e) {
        const stops = routeStops.querySelectorAll('.route-stop');
        if (stops.length < 2) {
            e.preventDefault();
            alert('A route must have at least 2 stations.');
            return false;
        }
        return true;
    });
});
</script>
@endsection