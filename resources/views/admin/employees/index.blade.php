@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-users me-2"></i>Employees Management</h2>
    <div>
        <button type="button" id="printSelectedBtn" class="btn btn-info me-2" style="display:none;">
            <i class="fas fa-print me-1"></i>Print Selected QR Codes (<span id="selectedCount">0</span>)
        </button>
        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Add Employee
        </a>
    </div>
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
                    <th width="40">
                        <input type="checkbox" id="selectAll" class="form-check-input">
                    </th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input employee-checkbox" value="{{ $employee->id }}" data-code="{{ $employee->employee_code }}" data-name="{{ $employee->first_name }} {{ $employee->last_name }}">
                        </td>
                        <td><strong>{{ $employee->employee_code }}</strong></td>
                        <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                        <td>{{ $employee->department ?? '-' }}</td>
                        <td>{{ $employee->position ?? '-' }}</td>
                        <td>{{ $employee->email ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $employee->is_active ? 'success' : 'danger' }}">
                                {{ $employee->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.employees.qrcode', $employee) }}" class="btn btn-sm btn-info" title="View QR Code">
                                <i class="fas fa-qrcode"></i>
                            </a>
                            <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No employees found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $employees->links() }}
</div>

@endsection

@section('scripts')
<script>
(function() {
    'use strict';
    
    console.log('Employee selection script loaded');
    
    const selectAllCheckbox = document.getElementById('selectAll');
    const printSelectedBtn = document.getElementById('printSelectedBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    
    if (!selectAllCheckbox || !printSelectedBtn || !selectedCountSpan) {
        console.error('Required elements not found');
        return;
    }

    // Function to get all employee checkboxes
    function getEmployeeCheckboxes() {
        return document.querySelectorAll('.employee-checkbox');
    }

    // Update print button visibility and count
    function updatePrintButton() {
        const checkedCheckboxes = document.querySelectorAll('.employee-checkbox:checked');
        const count = checkedCheckboxes.length;
        
        console.log('Checked count:', count);
        
        if (count > 0) {
            printSelectedBtn.style.display = 'inline-block';
            selectedCountSpan.textContent = count;
        } else {
            printSelectedBtn.style.display = 'none';
        }
    }

    // Update select all checkbox state
    function updateSelectAllCheckbox() {
        const employeeCheckboxes = getEmployeeCheckboxes();
        const checkedCount = document.querySelectorAll('.employee-checkbox:checked').length;
        const totalCount = employeeCheckboxes.length;
        
        selectAllCheckbox.checked = checkedCount === totalCount && totalCount > 0;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
    }

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        const employeeCheckboxes = getEmployeeCheckboxes();
        employeeCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updatePrintButton();
    });

    // Individual checkbox change
    const employeeCheckboxes = getEmployeeCheckboxes();
    employeeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllCheckbox();
            updatePrintButton();
        });
    });

    // Print selected QR codes
    printSelectedBtn.addEventListener('click', function() {
        const checkedCheckboxes = document.querySelectorAll('.employee-checkbox:checked');
        const employeeIds = Array.from(checkedCheckboxes).map(cb => cb.value);
        
        console.log('Printing for IDs:', employeeIds);
        
        if (employeeIds.length > 0) {
            const url = '{{ route("admin.employees.qrcode-bulk") }}?ids=' + employeeIds.join(',');
            window.open(url, '_blank', 'width=1200,height=800');
        }
    });

    // Initial update
    updatePrintButton();
    
    console.log('Found', employeeCheckboxes.length, 'employee checkboxes');
})();
</script>
@endsection
