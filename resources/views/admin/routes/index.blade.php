@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-road me-2"></i>Routes Management</h2>
    <a href="{{ route('admin.routes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Add Route
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
                    <th>From</th>
                    <th>To</th>
                    <th>Distance (km)</th>
                    <th>Duration</th>
                    <th>Trips</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($routes as $route)
                    <tr>
                        <td><strong>{{ $route->name }}</strong></td>
                        <td>{{ $route->pickupLocation->name ?? '-' }}</td>
                        <td>{{ $route->dropoffLocation->name ?? '-' }}</td>
                        <td>{{ $route->distance_km ?? '-' }} km</td>
                        <td>{{ $route->estimated_duration_minutes ?? '-' }} min</td>
                        <td><span class="badge bg-primary">{{ $route->trips_count }}</span></td>
                        <td>
                            <a href="{{ route('admin.routes.edit', $route) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No routes found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $routes->links() }}
</div>
@endsection
