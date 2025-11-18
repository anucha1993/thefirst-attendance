@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="page-title"><i class="fas fa-bus me-2"></i>Edit Vehicle</h2>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.vehicles.update', $vehicle) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">License Plate *</label>
                        <input type="text" class="form-control @error('license_plate') is-invalid @enderror" 
                               name="license_plate" value="{{ old('license_plate', $vehicle->license_plate) }}" required>
                        @error('license_plate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Vehicle Model</label>
                        <input type="text" class="form-control @error('vehicle_model') is-invalid @enderror" 
                               name="vehicle_model" value="{{ old('vehicle_model', $vehicle->vehicle_model) }}">
                        @error('vehicle_model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Capacity *</label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                               name="capacity" value="{{ old('capacity', $vehicle->capacity) }}" min="1" required>
                        @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Status *</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                            <option value="active" {{ old('status', $vehicle->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $vehicle->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ old('status', $vehicle->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="3">{{ old('description', $vehicle->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update
                        </button>
                        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
