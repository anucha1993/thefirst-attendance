@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="page-title"><i class="fas fa-road me-2"></i>Edit Route</h2>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.routes.update', $route) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Route Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name', $route->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Pickup Location *</label>
                            <select class="form-select @error('pickup_location_id') is-invalid @enderror" 
                                    name="pickup_location_id" required>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('pickup_location_id', $route->pickup_location_id) == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pickup_location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Dropoff Location *</label>
                            <select class="form-select @error('dropoff_location_id') is-invalid @enderror" 
                                    name="dropoff_location_id" required>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('dropoff_location_id', $route->dropoff_location_id) == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dropoff_location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Distance (km)</label>
                            <input type="number" step="0.1" class="form-control @error('distance_km') is-invalid @enderror" 
                                   name="distance_km" value="{{ old('distance_km', $route->distance_km) }}">
                            @error('distance_km') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Estimated Duration (minutes)</label>
                            <input type="number" class="form-control @error('estimated_duration_minutes') is-invalid @enderror" 
                                   name="estimated_duration_minutes" value="{{ old('estimated_duration_minutes', $route->estimated_duration_minutes) }}">
                            @error('estimated_duration_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="3">{{ old('description', $route->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update
                        </button>
                        <a href="{{ route('admin.routes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
