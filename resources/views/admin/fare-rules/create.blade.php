@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-coins me-2"></i>เพิ่มกฎค่าโดยสาร</h2>
    <a href="{{ route('admin.fare-rules.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>กลับ
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.fare-rules.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">ชื่อกฎ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label">ประเภท <span class="text-danger">*</span></label>
                    <select class="form-select @error('type') is-invalid @enderror" 
                            id="type" name="type" required>
                        <option value="">-- เลือกประเภท --</option>
                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed (ค่าคงที่)</option>
                        <option value="distance_based" {{ old('type') == 'distance_based' ? 'selected' : '' }}>Distance Based (คำนวณตามระยะทาง)</option>
                        <option value="special" {{ old('type') == 'special' ? 'selected' : '' }}>Special (พิเศษ)</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="route_id" class="form-label">สายรถ</label>
                    <select class="form-select @error('route_id') is-invalid @enderror" 
                            id="route_id" name="route_id">
                        <option value="">-- ทุกสาย --</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                {{ $route->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('route_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="base_fare" class="form-label">ค่าโดยสารพื้นฐาน (฿)</label>
                    <input type="number" step="0.01" class="form-control @error('base_fare') is-invalid @enderror" 
                           id="base_fare" name="base_fare" value="{{ old('base_fare') }}">
                    @error('base_fare')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">ใช้สำหรับประเภท Fixed</small>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="calculation_mode" class="form-label">โหมดการคำนวณ <span class="text-danger">*</span></label>
                    <select class="form-select @error('calculation_mode') is-invalid @enderror" 
                            id="calculation_mode" name="calculation_mode" required>
                        <option value="">-- เลือกโหมด --</option>
                        <option value="per_passenger" {{ old('calculation_mode') == 'per_passenger' ? 'selected' : '' }}>ต่อผู้โดยสาร</option>
                        <option value="per_trip" {{ old('calculation_mode') == 'per_trip' ? 'selected' : '' }}>ต่อรอบ</option>
                        <option value="per_km" {{ old('calculation_mode') == 'per_km' ? 'selected' : '' }}>ต่อกิโลเมตร</option>
                    </select>
                    @error('calculation_mode')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="effective_from" class="form-label">มีผลตั้งแต่ <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('effective_from') is-invalid @enderror" 
                           id="effective_from" name="effective_from" value="{{ old('effective_from', date('Y-m-d')) }}" required>
                    @error('effective_from')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label for="effective_until" class="form-label">มีผลถึง</label>
                    <input type="date" class="form-control @error('effective_until') is-invalid @enderror" 
                           id="effective_until" name="effective_until" value="{{ old('effective_until') }}">
                    @error('effective_until')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">ไม่ระบุ = ไม่มีวันหมดอายุ</small>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">คำอธิบาย</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                           value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">เปิดใช้งาน</label>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.fare-rules.index') }}" class="btn btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>บันทึก
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
