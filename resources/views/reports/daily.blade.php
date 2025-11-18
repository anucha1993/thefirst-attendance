@extends('layouts.app')

@section('title', 'รายงานรายวัน')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-calendar-day me-2"></i>รายงานรายวัน</h2>
    <div>
        <a href="{{ route('reports.export-daily-excel', ['date' => $date]) }}" class="btn btn-success me-2">
            <i class="fas fa-file-excel me-1"></i>Export Excel
        </a>
        <a href="{{ route('reports.export-daily-pdf', ['date' => $date]) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf me-1"></i>Export PDF
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-filter me-2"></i>เลือกวันที่
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label for="date" class="form-label">วันที่</label>
                <input type="date" name="date" id="date" class="form-control form-control-lg" value="{{ $date }}" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="fas fa-search me-1"></i>ค้นหา
                </button>
            </div>
            <div class="col-md-3">
                <a href="{{ route('reports.export-daily-excel', ['date' => $date]) }}" class="btn btn-success btn-lg w-100">
                    <i class="fas fa-file-excel me-1"></i>Excel
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center border-primary">
            <div class="card-body">
                <i class="fas fa-route fa-3x text-primary mb-3"></i>
                <h2 class="text-primary mb-0">{{ $summary['total_trips'] ?? 0 }}</h2>
                <p class="text-muted mb-0">รอบทั้งหมด</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-info">
            <div class="card-body">
                <i class="fas fa-users fa-3x text-info mb-3"></i>
                <h2 class="text-info mb-0">{{ $summary['total_passengers'] ?? 0 }}</h2>
                <p class="text-muted mb-0">พนักงานทั้งหมด</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <i class="fas fa-coins fa-3x text-success mb-3"></i>
                <h2 class="text-success mb-0">{{ number_format($summary['total_fare'] ?? 0, 2) }}</h2>
                <p class="text-muted mb-0">ยอดค่ารถทั้งหมด (บาท)</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body">
                <i class="fas fa-bus fa-3x text-warning mb-3"></i>
                <h2 class="text-warning mb-0">{{ count($summary['trips_by_vehicle'] ?? []) }}</h2>
                <p class="text-muted mb-0">รถที่ใช้</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header bg-info text-white">
                <i class="fas fa-bus me-2"></i>สรุปตามรถ
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ทะเบียน</th>
                                <th class="text-center">รอบ</th>
                                <th class="text-center">ผู้โดยสาร</th>
                                <th class="text-end">ค่าโดยสาร (฿)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summary['trips_by_vehicle'] ?? [] as $key => $vehicle_data)
                                <tr>
                                    <td><strong>{{ $vehicle_data['vehicle']->license_plate }}</strong></td>
                                    <td class="text-center"><span class="badge bg-primary">{{ $vehicle_data['trip_count'] ?? 0 }}</span></td>
                                    <td class="text-center"><span class="badge bg-info">{{ $vehicle_data['passenger_count'] ?? 0 }}</span></td>
                                    <td class="text-end"><strong>{{ number_format($vehicle_data['total_fare'] ?? 0, 2) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">ไม่มีข้อมูล</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <i class="fas fa-route me-2"></i>สรุปตามสาย
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>สายรถ</th>
                                <th class="text-center">รอบ</th>
                                <th class="text-center">ผู้โดยสาร</th>
                                <th class="text-end">ค่าโดยสาร (฿)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($summary['trips_by_route'] ?? [] as $key => $route_data)
                                <tr>
                                    <td><strong>{{ $route_data['route']->name }}</strong></td>
                                    <td class="text-center"><span class="badge bg-primary">{{ $route_data['trip_count'] ?? 0 }}</span></td>
                                    <td class="text-center"><span class="badge bg-info">{{ $route_data['passenger_count'] ?? 0 }}</span></td>
                                    <td class="text-end"><strong>{{ number_format($route_data['total_fare'] ?? 0, 2) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">ไม่มีข้อมูล</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-dark text-white">
        <i class="fas fa-list-ul me-2"></i>รายละเอียดรอบทั้งหมด ({{ $trips->count() }} รอบ)
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="80">เวลา</th>
                        <th>สายรถ</th>
                        <th>เส้นทาง</th>
                        <th>รถ</th>
                        <th>คนขับ</th>
                        <th class="text-center">ผู้โดยสาร</th>
                        <th class="text-end">ค่าโดยสาร (฿)</th>
                        <th width="80">สถานะ</th>
                        <th width="80"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trips as $trip)
                        <tr>
                            <td><strong>{{ $trip->started_at->format('H:i') }}</strong></td>
                            <td><span class="badge bg-primary">{{ $trip->route->name }}</span></td>
                            <td>
                                <small class="text-muted">
                                    {{ $trip->route->pickupLocation->name }} → {{ $trip->route->dropoffLocation->name }}
                                </small>
                            </td>
                            <td>{{ $trip->vehicle->license_plate }}</td>
                            <td>{{ $trip->driver->name }}</td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $trip->attendanceRecords->count() }}</span>
                            </td>
                            <td class="text-end"><strong>{{ number_format($trip->total_fare ?? 0, 2) }}</strong></td>
                            <td>
                                <span class="badge bg-{{ $trip->status === 'completed' ? 'success' : 'warning' }}">
                                    {{ $trip->status === 'completed' ? 'เสร็จสิ้น' : 'กำลังดำเนินการ' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('reports.trip-details', $trip) }}" class="btn btn-sm btn-outline-primary" title="ดูรายละเอียด">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                ไม่มีข้อมูลรอบในวันนี้
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
