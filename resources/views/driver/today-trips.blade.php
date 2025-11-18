@extends('layouts.app')

@section('title', 'รอบวันนี้')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-list me-2"></i>รอบวันนี้</h2>
    <a href="{{ route('driver.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>กลับ
    </a>
</div>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="fas fa-calendar-day me-2"></i>{{ now()->locale('th')->translatedFormat('l, d F Y') }}</span>
            <span class="badge bg-light text-dark">ทั้งหมด {{ $trips->count() }} รอบ</span>
        </div>
    </div>
</div>

@if($trips->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">ยังไม่มีรอบวันนี้</h5>
            <p class="text-muted mb-4">เริ่มรอบใหม่เพื่อเริ่มทำงาน</p>
            <a href="{{ route('driver.trip.start-form') }}" class="btn btn-success">
                <i class="fas fa-play-circle me-1"></i>เริ่มรอบใหม่
            </a>
        </div>
    </div>
@else
    <div class="row">
        @foreach($trips as $trip)
            <div class="col-md-6 mb-4">
                <div class="card h-100 {{ $trip->status === 'active' ? 'border-success' : '' }}">
                    <div class="card-header {{ $trip->status === 'active' ? 'bg-success text-white' : 'bg-light' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>
                                <i class="fas fa-route me-1"></i>
                                {{ $trip->route->name }}
                            </strong>
                            <span class="badge bg-{{ $trip->status === 'active' ? 'warning' : ($trip->status === 'completed' ? 'success' : 'secondary') }}">
                                @if($trip->status === 'active')
                                    <i class="fas fa-spinner fa-spin me-1"></i>กำลังดำเนินการ
                                @elseif($trip->status === 'completed')
                                    <i class="fas fa-check-circle me-1"></i>เสร็จสิ้น
                                @else
                                    {{ ucfirst($trip->status) }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Trip Details -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">รถ</small>
                                <strong>{{ $trip->vehicle->license_plate }}</strong>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted d-block">เวลาเริ่ม</small>
                                <strong>{{ $trip->started_at->format('H:i น.') }}</strong>
                            </div>
                        </div>

                        <!-- Route Info -->
                        <div class="mb-3 p-2 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-map-marker-alt text-success"></i>
                                    <small>{{ $trip->route->pickupLocation->name }}</small>
                                </div>
                                <div>
                                    <i class="fas fa-arrow-right text-muted"></i>
                                </div>
                                <div>
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    <small>{{ $trip->route->dropoffLocation->name }}</small>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-road me-1"></i>{{ $trip->route->distance_km }} km
                                    <i class="fas fa-clock ms-2 me-1"></i>~{{ $trip->route->estimated_duration_minutes }} นาที
                                </small>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="p-2 bg-primary bg-opacity-10 rounded">
                                    <div class="h4 mb-0 text-primary">{{ $trip->attendanceRecords->count() }}</div>
                                    <small class="text-muted">ผู้โดยสาร</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 bg-success bg-opacity-10 rounded">
                                    <div class="h4 mb-0 text-success">{{ $trip->vehicle->capacity }}</div>
                                    <small class="text-muted">ที่นั่งทั้งหมด</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 bg-info bg-opacity-10 rounded">
                                    <div class="h4 mb-0 text-info">
                                        {{ number_format($trip->total_fare ?? 0, 0) }}
                                    </div>
                                    <small class="text-muted">บาท</small>
                                </div>
                            </div>
                        </div>

                        @if($trip->completed_at)
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle me-1"></i>
                                <small>เสร็จสิ้นเมื่อ {{ $trip->completed_at->format('H:i น.') }}</small>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-light">
                        @if($trip->status === 'active')
                            <a href="{{ route('driver.trip.scan', $trip) }}" class="btn btn-success btn-sm w-100">
                                <i class="fas fa-qrcode me-1"></i>เปิดหน้าสแกน
                            </a>
                        @elseif($trip->status === 'completed')
                            <a href="{{ route('driver.trip-summary', $trip) }}" class="btn btn-info btn-sm w-100">
                                <i class="fas fa-file-alt me-1"></i>ดูสรุปรอบ
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<!-- Summary Card -->
@if($trips->isNotEmpty())
    <div class="card mt-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>สรุปวันนี้</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="stat-card">
                        <i class="fas fa-route fa-2x text-primary mb-2"></i>
                        <div class="stat-number">{{ $trips->count() }}</div>
                        <div class="stat-label">รอบทั้งหมด</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <div class="stat-number">{{ $trips->where('status', 'completed')->count() }}</div>
                        <div class="stat-label">รอบเสร็จสิ้น</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <i class="fas fa-users fa-2x text-info mb-2"></i>
                        <div class="stat-number">{{ $trips->sum(fn($t) => $t->attendanceRecords->count()) }}</div>
                        <div class="stat-label">ผู้โดยสารทั้งหมด</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <i class="fas fa-coins fa-2x text-warning mb-2"></i>
                        <div class="stat-number">{{ number_format($trips->sum('total_fare') ?? 0, 0) }}</div>
                        <div class="stat-label">บาท</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
