@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-purple text-white">
                    <h5 class="mb-0"><i class="bi bi-train-front"></i> Add New Train</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.trains.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Train Number</label>
                            <input type="text" name="train_number" class="form-control @error('train_number') is-invalid @enderror" value="{{ old('train_number') }}" placeholder="e.g., RAJ123" required>
                            @error('train_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Train Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g., Rajdhani Express" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Type</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="express" {{ old('type') == 'express' ? 'selected' : '' }}>Express</option>
                                <option value="superfast" {{ old('type') == 'superfast' ? 'selected' : '' }}>Superfast</option>
                                <option value="passenger" {{ old('type') == 'passenger' ? 'selected' : '' }}>Passenger</option>
                                <option value="local" {{ old('type') == 'local' ? 'selected' : '' }}>Local</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Total Seats</label>
                            <input type="number" name="total_seats" class="form-control @error('total_seats') is-invalid @enderror" value="{{ old('total_seats', 100) }}" min="1" required>
                            @error('total_seats')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.trains.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-purple">Create Train</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection