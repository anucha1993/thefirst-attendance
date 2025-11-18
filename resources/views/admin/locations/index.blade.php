@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-map-marker-alt me-2"></i>Locations Management</h2>
    <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Add Location
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Coordinates</th>
                    <th>Routes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $location)
                    <tr>
                        <td><strong>{{ $location->name }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $location->type === 'pickup' ? 'info' : ($location->type === 'dropoff' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($location->type) }}
                            </span>
                        </td>
                        <td>
                            @if($location->latitude && $location->longitude)
                                {{ number_format($location->latitude, 4) }}, {{ number_format($location->longitude, 4) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $location->pickup_routes_count + $location->dropoff_routes_count ?? 0 }}</td>
                        <td>
                            <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No locations found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $locations->links() }}
</div>
@endsection
