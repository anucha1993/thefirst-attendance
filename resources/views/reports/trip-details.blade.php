@extends('layouts.app')

@section('title', 'รายละเอียดรอบ #' . $trip->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-file-alt me-2"></i>รายละเอียดรอบ #{{ $trip->id }}</h2>
    <div>
        <a href="{{ route('reports.export-trip-excel', $trip) }}" class="btn btn-success me-2">
            <i class="fas fa-file-excel me-1"></i>Export Excel
        </a>
        <button onclick="window.print()" class="btn btn-info me-2">
            <i class="fas fa-print me-1"></i>พิมพ์
        </button>
        <a href="{{ route('reports.daily', ['date' => $trip->started_at->toDateString()]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>กลับ
        </a>
    </div>
</div>

<!-- Trip Information -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>ข้อมูลรอบ</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-sm-5">รหัสรอบ:</dt>
                            <dd class="col-sm-7"><strong>#{{ $trip->id }}</strong></dd>

                            <dt class="col-sm-5">วันที่:</dt>
                            <dd class="col-sm-7">{{ $trip->started_at->format('d/m/Y') }}</dd>

                            <dt class="col-sm-5">เวลาเริ่ม:</dt>
                            <dd class="col-sm-7">{{ $trip->started_at->format('H:i น.') }}</dd>

                            <dt class="col-sm-5">เวลาสิ้นสุด:</dt>
                            <dd class="col-sm-7">
                                @if($trip->completed_at)
                                    {{ $trip->completed_at->format('H:i น.') }}
                                @else
                                    <span class="badge bg-warning">ยังไม่เสร็จสิ้น</span>
                                @endif
                            </dd>

                            <dt class="col-sm-5">ระยะเวลา:</dt>
                            <dd class="col-sm-7">
                                @if($trip->completed_at)
                                    {{ $trip->started_at->diffInMinutes($trip->completed_at) }} นาที
                                @else
                                    -
                                @endif
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="row mb-0">
                            <dt class="col-sm-5">สายรถ:</dt>
                            <dd class="col-sm-7"><span class="badge bg-primary">{{ $trip->route->name }}</span></dd>

                            <dt class="col-sm-5">จุดรับ:</dt>
                            <dd class="col-sm-7">{{ $trip->route->pickupLocation->name }}</dd>

                            <dt class="col-sm-5">จุดส่ง:</dt>
                            <dd class="col-sm-7">{{ $trip->route->dropoffLocation->name }}</dd>

                            <dt class="col-sm-5">ป้ายทะเบียน:</dt>
                            <dd class="col-sm-7">{{ $trip->vehicle->license_plate }}</dd>

                            <dt class="col-sm-5">คนขับ:</dt>
                            <dd class="col-sm-7">{{ $trip->driver->name }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center h-100">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>สรุป</h6>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="mb-3">
                    <h1 class="display-3 text-success mb-0">{{ $records->count() }}</h1>
                    <p class="text-muted mb-0">ผู้โดยสาร</p>
                    <small class="text-muted">/ {{ $trip->vehicle->capacity }} ที่นั่ง</small>
                </div>
                <div class="progress mb-3" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $trip->vehicle->capacity > 0 ? round(($records->count() / $trip->vehicle->capacity) * 100) : 0 }}%">
                        {{ $trip->vehicle->capacity > 0 ? round(($records->count() / $trip->vehicle->capacity) * 100) : 0 }}%
                    </div>
                </div>
                <h2 class="text-warning mb-0">{{ number_format($trip->total_fare ?? 0, 2) }}</h2>
                <p class="text-muted mb-0">บาท</p>
            </div>
        </div>
    </div>
</div>

<!-- Passenger List -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-users me-2"></i>รายชื่อผู้โดยสาร ({{ $records->count() }} คน)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">#</th>
                        <th>รหัสพนักงาน</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>แผนก</th>
                        <th>ตำแหน่ง</th>
                        <th>เวลาสแกน</th>
                        <th class="text-end">ค่าโดยสาร (บาท)</th>
                        <th class="text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $index => $record)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $record->employee->employee_code }}</strong></td>
                            <td>{{ $record->employee->first_name }} {{ $record->employee->last_name }}</td>
                            <td>{{ $record->employee->department ?? '-' }}</td>
                            <td>{{ $record->employee->position ?? '-' }}</td>
                            <td>
                                <small>{{ $record->scanned_at->format('H:i:s') }}</small>
                            </td>
                            <td class="text-end">
                                <strong>{{ number_format($trip->passenger_count > 0 ? $trip->total_fare / $trip->passenger_count : 0, 2) }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">ปกติ</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                ไม่มีผู้โดยสาร
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($records->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="6" class="text-end">รวมทั้งหมด:</th>
                            <th class="text-end">
                                <h5 class="mb-0 text-success">{{ number_format($trip->total_fare ?? 0, 2) }}</h5>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Audit Log -->
@if($auditLogs->count() > 0)
    <div class="card">
        <div class="card-header bg-warning">
            <h6 class="mb-0"><i class="fas fa-history me-2"></i>ประวัติการเปลี่ยนแปลง ({{ $auditLogs->count() }} รายการ)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="150">เวลา</th>
                            <th width="120">การกระทำ</th>
                            <th>ผู้ดำเนินการ</th>
                            <th>เหตุผล</th>
                            <th>รายละเอียด</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($auditLogs as $log)
                            <tr>
                                <td><small>{{ $log->created_at->format('d/m/Y H:i:s') }}</small></td>
                                <td>
                                    <span class="badge bg-{{ $log->action === 'created' ? 'success' : ($log->action === 'cancelled' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td>{{ $log->user->name ?? 'System' }}</td>
                                <td><small>{{ $log->reason ?? '-' }}</small></td>
                                <td>
                                    @if($log->attendanceRecord)
                                        <small>
                                            {{ $log->attendanceRecord->employee->employee_code }} - 
                                            {{ $log->attendanceRecord->employee->first_name }} {{ $log->attendanceRecord->employee->last_name }}
                                        </small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

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

<style>
    @media print {
        .btn, .no-print {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }
    }
</style>

@endsection
