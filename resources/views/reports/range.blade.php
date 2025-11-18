@extends('layouts.app')

@section('title', 'รายงานช่วงเวลา')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-calendar-alt me-2"></i>รายงานช่วงเวลา</h2>
    <a href="{{ route('reports.export-range-excel', array_merge(['date_from' => $dateFrom, 'date_to' => $dateTo], $filters)) }}" class="btn btn-success">
        <i class="fas fa-file-excel me-1"></i>Export Excel
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-filter me-2"></i>ตัวกรอง
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="date_from" class="form-label">จากวันที่</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom }}" required>
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">ถึงวันที่</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo }}" required>
            </div>
            <div class="col-md-2">
                <label for="vehicle_id" class="form-label">รถ</label>
                <select name="vehicle_id" id="vehicle_id" class="form-select">
                    <option value="">-- ทั้งหมด --</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ $filters['vehicle_id'] == $vehicle->id ? 'selected' : '' }}>
                            {{ $vehicle->license_plate }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="route_id" class="form-label">สายรถ</label>
                <select name="route_id" id="route_id" class="form-select">
                    <option value="">-- ทั้งหมด --</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" {{ $filters['route_id'] == $route->id ? 'selected' : '' }}>
                            {{ $route->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="driver_id" class="form-label">คนขับ</label>
                <select name="driver_id" id="driver_id" class="form-select">
                    <option value="">-- ทั้งหมด --</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ $filters['driver_id'] == $driver->id ? 'selected' : '' }}>
                            {{ $driver->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>ค้นหา
                </button>
                <a href="{{ route('reports.range') }}" class="btn btn-secondary">
                    <i class="fas fa-redo me-1"></i>รีเซ็ต
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Statistics -->
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
                <p class="text-muted mb-0">ผู้โดยสารทั้งหมด</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <i class="fas fa-coins fa-3x text-success mb-3"></i>
                <h2 class="text-success mb-0">{{ number_format($summary['total_fare'] ?? 0, 2) }}</h2>
                <p class="text-muted mb-0">ค่าโดยสารรวม (บาท)</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body">
                <i class="fas fa-calendar-day fa-3x text-warning mb-3"></i>
                <h2 class="text-warning mb-0">{{ \Carbon\Carbon::parse($dateFrom)->diffInDays(\Carbon\Carbon::parse($dateTo)) + 1 }}</h2>
                <p class="text-muted mb-0">วัน</p>
            </div>
        </div>
    </div>
</div>

@if(isset($summary['calculations']) && count($summary['calculations']) > 0)
<!-- Trip Details -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-list me-2"></i>รายการรอบทั้งหมด
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="80">Trip ID</th>
                        <th>วันที่-เวลา</th>
                        <th>สายรถ</th>
                        <th>รถ</th>
                        <th>คนขับ</th>
                        <th class="text-center">ผู้โดยสาร</th>
                        <th class="text-end">ค่าโดยสาร (บาท)</th>
                        <th width="100"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary['calculations'] as $calc)
                        <tr>
                            <td><span class="badge bg-secondary">#{{ $calc['trip_id'] }}</span></td>
                            <td><small>{{ \Carbon\Carbon::parse($calc['started_at'])->format('d/m/Y H:i') }}</small></td>
                            <td>{{ $calc['route'] }}</td>
                            <td>{{ $calc['vehicle'] }}</td>
                            <td>{{ $calc['driver'] }}</td>
                            <td class="text-center"><span class="badge bg-info">{{ $calc['passenger_count'] }}</span></td>
                            <td class="text-end"><strong>{{ number_format($calc['total_fare'], 2) }}</strong></td>
                            <td>
                                <a href="{{ route('reports.trip-details', $calc['trip_id']) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> ดู
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="5">รวม</th>
                        <th class="text-center">{{ $summary['total_passengers'] ?? 0 }}</th>
                        <th class="text-end">{{ number_format($summary['total_fare'] ?? 0, 2) }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">ไม่พบข้อมูลในช่วงเวลาที่เลือก</h5>
            <p class="text-muted">ลองเปลี่ยนช่วงเวลาหรือตัวกรองอื่น</p>
        </div>
    </div>
@endif

@endsection
