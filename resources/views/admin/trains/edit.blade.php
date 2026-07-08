@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-purple text-white">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Train: {{ $train->train_number }}</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.trains.update', $train) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Train Number</label>
                            <input type="text" name="train_number" class="form-control @error('train_number') is-invalid @enderror" value="{{ old('train_number', $train->train_number) }}" required>
                            @error('train_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Train Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $train->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Type</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="express" {{ old('type', $train->type) == 'express' ? 'selected' : '' }}>Express</option>
                                <option value="superfast" {{ old('type', $train->type) == 'superfast' ? 'selected' : '' }}>Superfast</option>
                                <option value="passenger" {{ old('type', $train->type) == 'passenger' ? 'selected' : '' }}>Passenger</option>
                                <option value="local" {{ old('type', $train->type) == 'local' ? 'selected' : '' }}>Local</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Total Seats</label>
                            <input type="number" name="total_seats" class="form-control @error('total_seats') is-invalid @enderror" value="{{ old('total_seats', $train->total_seats) }}" min="1" required>
                            @error('total_seats')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $train->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_active">Active</label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.trains.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-purple">Update Train</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection