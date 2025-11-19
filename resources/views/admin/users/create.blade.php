@extends('layouts.app')

@section('title', 'เพิ่มผู้ใช้งาน')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-user-plus me-2"></i>เพิ่มผู้ใช้งาน</h2>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>กลับ
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">อีเมล <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">รหัสผ่าน <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">ต้องมีอย่างน้อย 8 ตัวอักษร</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" 
                           id="password_confirmation" name="password_confirmation" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">บทบาท <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" 
                            id="role" name="role" required>
                        <option value="">-- เลือกบทบาท --</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>ผู้ดูแลระบบ (Admin)</option>
                        <option value="driver" {{ old('role') == 'driver' ? 'selected' : '' }}>คนขับ (Driver)</option>
                        <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}>หัวหน้า/HR (Supervisor)</option>
                        <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>พนักงาน (Employee)</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">สถานะ</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                               value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">เปิดใช้งาน</label>
                    </div>
                </div>
            </div>

            <!-- Vehicle Selection (Only for Driver) -->
            <div class="mb-3" id="vehicleSection" style="display: none;">
                <label class="form-label">รถที่สามารถใช้งานได้ <i class="fas fa-car ms-1"></i></label>
                <select class="form-select" id="vehicleSelect" name="vehicles[]" multiple="multiple">
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" 
                                {{ in_array($vehicle->id, old('vehicles', [])) ? 'selected' : '' }}>
                            {{ $vehicle->license_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">เลือกรถที่คนขับคนนี้สามารถใช้งานได้ (เลือกได้มากกว่า 1 คัน)</small>
            </div>

            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>คำอธิบายบทบาท:</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>Admin:</strong> สามารถเข้าถึงและจัดการทุกอย่างในระบบ</li>
                    <li><strong>Driver:</strong> สามารถเริ่มรอบ สแกน QR Code พนักงาน และปิดรอบ</li>
                    <li><strong>Supervisor:</strong> สามารถดูรายงานและข้อมูลพนักงาน</li>
                    <li><strong>Employee:</strong> ใช้งานเฉพาะการแสดง QR Code สำหรับสแกน</li>
                </ul>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>บันทึก
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 with Bootstrap 5 theme
        $('#vehicleSelect').select2({
            theme: 'bootstrap-5',
            placeholder: 'เลือกรถ...',
            allowClear: true,
            width: '100%'
        });

        // Show/hide vehicle section based on role selection
        $('#role').on('change', function() {
            const vehicleSection = $('#vehicleSection');
            if (this.value === 'driver') {
                vehicleSection.show();
            } else {
                vehicleSection.hide();
                // Clear selection when not driver
                $('#vehicleSelect').val(null).trigger('change');
            }
        });

        // Trigger on page load to check initial value
        if ($('#role').val() === 'driver') {
            $('#vehicleSection').show();
        }
    });
</script>
@endpush
@endsection
