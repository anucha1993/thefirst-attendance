@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-users me-2"></i>Employees Management</h2>
    <div>
        <button type="button" id="printSelectedBtn" class="btn btn-info me-2" style="display:none;">
            <i class="fas fa-print me-1"></i>Print Selected QR Codes (<span id="selectedCount">0</span>)
        </button>
        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-excel me-1"></i>Import Excel
        </button>
        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Add Employee
        </a>
    </div>
</div>

<!-- Search and Filter Form -->
<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('admin.employees.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label"><i class="fas fa-search me-1"></i>ค้นหา</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="รหัสพนักงาน, ชื่อ, แผนก, ตำแหน่ง, อีเมล" 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-building me-1"></i>แผนก</label>
                <select name="department" class="form-select">
                    <option value="">-- ทั้งหมด --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label"><i class="fas fa-toggle-on me-1"></i>สถานะ</label>
                <select name="status" class="form-select">
                    <option value="">-- ทั้งหมด --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i>ค้นหา
                </button>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo me-1"></i>รีเซ็ต
                </a>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('import_errors'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-triangle me-2"></i>รายละเอียดข้อผิดพลาด:</strong>
        <ul class="mb-0 mt-2">
            @foreach(session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
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
                        <td colspan="8" class="text-center text-muted py-4">
                            @if(request()->hasAny(['search', 'department', 'status']))
                                <i class="fas fa-search me-2"></i>ไม่พบข้อมูลที่ค้นหา
                            @else
                                No employees found
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $employees->links() }}
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-excel me-2"></i>นำเข้าข้อมูลพนักงานจาก Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.employees.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>รูปแบบไฟล์:</strong>
                        <ul class="mb-0 mt-2">
                            <li>ไฟล์ Excel (.xlsx, .xls) หรือ CSV</li>
                            <li>ขนาดไฟล์ไม่เกิน 10MB</li>
                            <li>คอลัมน์ต้องมี: employee_code, first_name, last_name</li>
                            <li>QR Code จะถูกสร้างอัตโนมัติถ้าไม่ระบุ</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <label for="importFile" class="form-label">เลือกไฟล์ Excel</label>
                        <input type="file" class="form-control" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('admin.employees.template') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-1"></i>ดาวน์โหลดไฟล์ตัวอย่าง
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload me-1"></i>นำเข้าข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
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
