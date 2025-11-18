@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-coins me-2"></i>Fare Rules Management</h2>
    <a href="{{ route('admin.fare-rules.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Add Fare Rule
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
                    <th>Route</th>
                    <th>Base Fare</th>
                    <th>Mode</th>
                    <th>Effective From</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fareRules as $rule)
                    <tr>
                        <td><strong>{{ $rule->name }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $rule->type === 'fixed' ? 'success' : ($rule->type === 'distance_based' ? 'info' : 'warning') }}">
                                {{ ucfirst(str_replace('_', ' ', $rule->type)) }}
                            </span>
                        </td>
                        <td>{{ $rule->route->name ?? 'All Routes' }}</td>
                        <td>{{ $rule->base_fare ? '฿' . number_format($rule->base_fare, 2) : '-' }}</td>
                        <td>{{ ucfirst($rule->calculation_mode) }}</td>
                        <td>{{ $rule->effective_from->format('Y-m-d') }}</td>
                        <td>
                            <span class="badge bg-{{ $rule->is_active ? 'success' : 'danger' }}">
                                {{ $rule->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.fare-rules.edit', $rule) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.fare-rules.destroy', $rule) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No fare rules found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $fareRules->links() }}
</div>
@endsection
