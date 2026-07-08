@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-purple fw-bold mb-0">🚆 Train Management</h2>
        <a href="{{ route('admin.trains.create') }}" class="btn btn-purple">
            <i class="bi bi-plus-lg"></i> Add New Train
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-purple text-white">
                        <tr>
                            <th>Train #</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Seats</th>
                            <th>Routes</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trains as $train)
                        <tr>
                            <td class="fw-bold">{{ $train->train_number }}</td>
                            <td>{{ $train->name }}</td>
                            <td>
                                <span class="badge bg-{{ $train->type === 'superfast' ? 'danger' : ($train->type === 'express' ? 'warning text-dark' : 'info') }}">
                                    {{ ucfirst($train->type) }}
                                </span>
                            </td>
                            <td>{{ $train->total_seats }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $train->routes_count }}</span>
                            </td>
                            <td>
                                @if($train->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.trains.routes', $train) }}" class="btn btn-sm btn-outline-purple" title="Manage Routes">
                                    <i class="bi bi-map"></i>
                                </a>
                                <a href="{{ route('admin.trains.edit', $train) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.trains.destroy', $train) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this train?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No trains found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($trains->hasPages())
        <div class="card-footer bg-white">
            {{ $trains->links() }}
        </div>
        @endif
    </div>
</div>
@endsection