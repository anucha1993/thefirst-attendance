@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="page-title"><i class="fas fa-map-marker-alt me-2"></i>Edit Location</h2>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.locations.update', $location) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Location Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name', $location->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Type *</label>
                        <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                            <option value="pickup" {{ old('type', $location->type) === 'pickup' ? 'selected' : '' }}>Pickup</option>
                            <option value="dropoff" {{ old('type', $location->type) === 'dropoff' ? 'selected' : '' }}>Dropoff</option>
                            <option value="both" {{ old('type', $location->type) === 'both' ? 'selected' : '' }}>Both</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Latitude</label>
                            <input type="number" step="0.0001" class="form-control @error('latitude') is-invalid @enderror" 
                                   name="latitude" value="{{ old('latitude', $location->latitude) }}">
                            @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Longitude</label>
                            <input type="number" step="0.0001" class="form-control @error('longitude') is-invalid @enderror" 
                                   name="longitude" value="{{ old('longitude', $location->longitude) }}">
                            @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="3">{{ old('description', $location->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update
                        </button>
                        <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
