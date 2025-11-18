@extends('layouts.app')

@section('title', 'แก้ไขพนักงาน')

@section('content')
<div class="page-title">
    <i class="fas fa-user-edit me-2"></i>
    <h2>แก้ไขข้อมูลพนักงาน</h2>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-edit me-2"></i>ข้อมูลพนักงาน
            </div>
            <div class="card-body">
                <form action="{{ route('admin.employees.update', $employee) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="employee_code" class="form-label">รหัสพนักงาน *</label>
                                <input type="text" name="employee_code" id="employee_code" class="form-control @error('employee_code') is-invalid @enderror" value="{{ old('employee_code', $employee->employee_code) }}" required>
                                @error('employee_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">ชื่อ *</label>
                                <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $employee->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">นามสกุล *</label>
                                <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $employee->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="department" class="form-label">แผนก</label>
                                <input type="text" name="department" id="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department', $employee->department) }}">
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="position" class="form-label">ตำแหน่ง</label>
                                <input type="text" name="position" id="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', $employee->position) }}">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">อีเมล</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $employee->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                                <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $employee->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="is_active" class="form-label">สถานะ</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        ใช้งาน
                                    </label>
                                </div>
                                @error('is_active')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>
                            <strong>QR Code Token:</strong> {{ $employee->qrcode_token }}<br>
                            <strong>สร้างเมื่อ:</strong> {{ $employee->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>บันทึกการแก้ไข
                    </button>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">ยกเลิก</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-qrcode me-2"></i>QR Code
            </div>
            <div class="card-body text-center">
                <p class="text-muted small mb-3">
                    <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong><br>
                    {{ $employee->employee_code }}
                </p>
                <a href="{{ route('admin.employees.qrcode', $employee) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-eye me-1"></i>ดูรหัส QR
                </a>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>ข้อมูล
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5 small">ID:</dt>
                    <dd class="col-sm-7 small">{{ $employee->id }}</dd>

                    <dt class="col-sm-5 small">User ID:</dt>
                    <dd class="col-sm-7 small">{{ $employee->user_id }}</dd>

                    <dt class="col-sm-5 small">สถานะ:</dt>
                    <dd class="col-sm-7 small">
                        <span class="badge bg-{{ $employee->is_active ? 'success' : 'danger' }}">
                            {{ $employee->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                        </span>
                    </dd>

                    <dt class="col-sm-5 small">สร้างเมื่อ:</dt>
                    <dd class="col-sm-7 small">{{ $employee->created_at->format('d/m/Y H:i') }}</dd>

                    <dt class="col-sm-5 small">แก้ไขล่าสุด:</dt>
                    <dd class="col-sm-7 small">{{ $employee->updated_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

@endsection
