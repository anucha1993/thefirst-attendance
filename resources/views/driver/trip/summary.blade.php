@extends('layouts.app')

@section('title', 'สรุปรอบ')

@section('content')
<style>
    .summary-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .summary-header h2 {
        font-size: 1.5rem;
        margin: 0;
        font-weight: 700;
    }

    .action-bar {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .action-bar .btn {
        flex: 1;
        padding: 0.75rem;
        font-weight: 600;
    }

    .info-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        margin-bottom: 1rem;
    }

    .info-card h5 {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #2d3748;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f7fafc;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-row .label {
        font-weight: 600;
        color: #718096;
        font-size: 0.95rem;
    }

    .info-row .value {
        font-weight: 600;
        color: #2d3748;
        text-align: right;
        font-size: 0.95rem;
    }

    .route-display {
        background: #f7fafc;
        padding: 1.25rem;
        border-radius: 8px;
        margin-top: 1rem;
    }

    .route-display .location {
        text-align: center;
        margin-bottom: 1rem;
    }

    .route-display .location:last-child {
        margin-bottom: 0;
    }

    .route-display .location i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .route-display .location h6 {
        font-weight: 700;
        margin: 0.5rem 0 0.25rem 0;
    }

    .route-display .arrow {
        text-align: center;
        font-size: 2rem;
        color: #a0aec0;
        margin: 1rem 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .stat-card-mobile {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 12px;
        text-align: center;
    }

    .stat-card-mobile i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .stat-card-mobile .number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-card-mobile .label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .passenger-list {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .passenger-list h5 {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #2d3748;
    }

    .passenger-item {
        background: #f7fafc;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        border-left: 4px solid #667eea;
    }

    .passenger-item:last-child {
        margin-bottom: 0;
    }

    .passenger-item .code {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }

    .passenger-item .name {
        color: #718096;
        margin-bottom: 0.25rem;
    }

    .passenger-item .time {
        font-size: 0.85rem;
        color: #a0aec0;
    }

    .passenger-item .fare {
        font-size: 1rem;
        font-weight: 700;
        color: #48bb78;
        text-align: right;
    }

    @media (min-width: 768px) {
        .summary-header {
            padding: 2rem;
        }

        .summary-header h2 {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .route-display {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .route-display .location {
            flex: 1;
            margin-bottom: 0;
        }

        .route-display .arrow {
            margin: 0 2rem;
        }
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .summary-header,
        .info-card,
        .passenger-list {
            box-shadow: none;
            border: 1px solid #e2e8f0;
        }
    }
</style>

<!-- Content Start -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-file-alt me-2"></i>สรุปรอบขับรถ</h2>
    <div>
        <button onclick="window.print()" class="btn btn-info me-2">
            <i class="fas fa-print me-1"></i>พิมพ์
        </button>
        <a href="{{ route('driver.work-center') }}" class="btn btn-secondary">
            <i class="fas fa-home me-1"></i>หน้าหลัก
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Trip Header -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>ข้อมูลรอบ</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">รหัสรอบ:</dt>
                    <dd class="col-sm-8"><strong>#{{ $trip->id }}</strong></dd>

                    <dt class="col-sm-4">สายรถ:</dt>
                    <dd class="col-sm-8"><strong class="text-primary">{{ $trip->route->name }}</strong></dd>

                    <dt class="col-sm-4">ป้ายทะเบียน:</dt>
                    <dd class="col-sm-8">{{ $trip->vehicle->license_plate }} ({{ $trip->vehicle->vehicle_model }})</dd>

                    <dt class="col-sm-4">คนขับ:</dt>
                    <dd class="col-sm-8">{{ $trip->driver->name }}</dd>
                </dl>
            </div>
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">เริ่มรอบ:</dt>
                    <dd class="col-sm-8">{{ $trip->started_at->format('d/m/Y H:i น.') }}</dd>

                    <dt class="col-sm-4">สิ้นสุดรอบ:</dt>
                    <dd class="col-sm-8">
                        @if($trip->completed_at)
                            {{ $trip->completed_at->format('d/m/Y H:i น.') }}
                        @else
                            <span class="badge bg-warning">ยังไม่เสร็จสิ้น</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">ระยะเวลา:</dt>
                    <dd class="col-sm-8">
                        @if($trip->completed_at)
                            {{ $trip->started_at->diffInMinutes($trip->completed_at) }} นาที
                        @else
                            -
                        @endif
                    </dd>

                    <dt class="col-sm-4">สถานะ:</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-{{ $trip->status === 'completed' ? 'success' : 'warning' }}">
                            {{ $trip->status === 'completed' ? 'เสร็จสิ้น' : 'กำลังดำเนินการ' }}
                        </span>
                    </dd>
                </dl>
            </div>
        </div>

        <!-- Route Info -->
        <div class="mt-3 p-3 bg-light rounded">
            <div class="row align-items-center">
                <div class="col-md-5 text-center">
                    <i class="fas fa-map-marker-alt fa-2x text-success mb-2"></i>
                    <h6>{{ $trip->route->pickupLocation->name }}</h6>
                    <small class="text-muted">จุดรับ</small>
                </div>
                <div class="col-md-2 text-center">
                    <i class="fas fa-arrow-right fa-2x text-muted"></i>
                    <div class="mt-2">
                        <small class="text-muted">{{ $trip->route->distance_km }} km</small>
                    </div>
                </div>
                <div class="col-md-5 text-center">
                    <i class="fas fa-map-marker-alt fa-2x text-danger mb-2"></i>
                    <h6>{{ $trip->route->dropoffLocation->name }}</h6>
                    <small class="text-muted">จุดส่ง</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                <h2 class="text-primary mb-0">{{ $summary['total_passengers'] ?? 0 }}</h2>
                <p class="text-muted mb-0">ผู้โดยสารทั้งหมด</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-chair fa-3x text-success mb-3"></i>
                <h2 class="text-success mb-0">{{ $trip->vehicle->capacity }}</h2>
                <p class="text-muted mb-0">ที่นั่งทั้งหมด</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-percentage fa-3x text-info mb-3"></i>
                <h2 class="text-info mb-0">
                    {{ $trip->vehicle->capacity > 0 ? round(($summary['total_passengers'] ?? 0) / $trip->vehicle->capacity * 100) : 0 }}%
                </h2>
                <p class="text-muted mb-0">อัตราการใช้ที่นั่ง</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-coins fa-3x text-warning mb-3"></i>
                <h2 class="text-warning mb-0">{{ number_format($summary['total_fare'] ?? 0, 2) }}</h2>
                <p class="text-muted mb-0">บาท</p>
            </div>
        </div>
    </div>
</div>

<!-- Passenger List -->
<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>รายชื่อผู้โดยสาร ({{ $summary['total_passengers'] ?? 0 }} คน)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="60">#</th>
                        <th>รหัสพนักงาน</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>แผนก</th>
                        <th>เวลาสแกน</th>
                        <th class="text-end">ค่าโดยสาร (บาท)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trip->attendanceRecords()->with('employee')->orderBy('scanned_at')->get() as $index => $record)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $record->employee->employee_code }}</strong></td>
                            <td>{{ $record->employee->first_name }} {{ $record->employee->last_name }}</td>
                            <td>{{ $record->employee->department ?? '-' }}</td>
                            <td>
                                <small>{{ $record->scanned_at->format('H:i:s') }}</small>
                            </td>
                            <td class="text-end">
                                <strong>{{ number_format($trip->passenger_count > 0 ? $trip->total_fare / $trip->passenger_count : 0, 2) }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                ไม่มีผู้โดยสาร
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($summary['passenger_count'] > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">รวมทั้งหมด:</th>
                            <th class="text-end">
                                <h5 class="mb-0 text-success">{{ number_format($summary['total_fare'] ?? 0, 2) }} บาท</h5>
                            </th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Notes -->
@if($trip->notes)
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>หมายเหตุ</h6>
        </div>
        <div class="card-body">
            <p class="mb-0">{{ $trip->notes }}</p>
        </div>
    </div>
@endif

<!-- Print Styles -->
<style>
    @media print {
        .no-print, .navbar, .sidebar, .btn {
            display: none !important;
        }
        
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }
        
        @page {
            size: A4;
            margin: 15mm;
        }
    }
</style>

@endsection
