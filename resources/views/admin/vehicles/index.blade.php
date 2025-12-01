@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-bus me-2"></i>Vehicles Management</h2>
    <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Add Vehicle
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
                    <th>License Plate</th>
                    <th>Model</th>
                    <th>บริษัทขนส่ง</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Driver</th>
                    <th>Trips</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $vehicle)
                    <tr>
                        <td><strong>{{ $vehicle->license_plate }}</strong></td>
                        <td>{{ $vehicle->vehicle_model ?? '-' }}</td>
                        <td>{{ $vehicle->transport_company ?? '-' }}</td>
                        <td>{{ $vehicle->capacity }} seats</td>
                        <td>
                            <span class="badge bg-{{ $vehicle->status === 'active' ? 'success' : ($vehicle->status === 'maintenance' ? 'warning' : 'danger') }}">
                                {{ ucfirst($vehicle->status) }}
                            </span>
                        </td>
                        <td>{{ $vehicle->drivers->where('pivot.is_primary', true)->first()?->name ?? 'Unassigned' }}</td>
                        <td><span class="badge bg-primary">{{ $vehicle->trips_count }}</span></td>
                        <td>
                            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No vehicles found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $vehicles->links() }}
</div>
@endsection
