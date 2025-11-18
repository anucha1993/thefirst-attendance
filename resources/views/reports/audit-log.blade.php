@extends('layouts.app')

@section('title', 'Audit Log - ประวัติการเปลี่ยนแปลง')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title"><i class="fas fa-history me-2"></i>Audit Log - ประวัติการเปลี่ยนแปลง</h2>
</div>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-filter me-2"></i>กรองข้อมูล
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="date_from" class="form-label">วันที่เริ่มต้น</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom }}" required>
            </div>
            <div class="col-md-4">
                <label for="date_to" class="form-label">วันที่สิ้นสุด</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo }}" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i>ค้นหา
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">วันที่/เวลา</th>
                        <th style="width: 10%;">ประเภท</th>
                        <th style="width: 15%;">รายละเอียด</th>
                        <th style="width: 10%;">รอบที่</th>
                        <th style="width: 15%;">ข้อมูลเดิม</th>
                        <th style="width: 15%;">ข้อมูลใหม่</th>
                        <th style="width: 10%;">ผู้ดำเนินการ</th>
                        <th style="width: 15%;">เหตุผล</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditLogs as $log)
                        <tr>
                            <td>
                                <small>{{ $log->created_at->format('d/m/Y') }}</small><br>
                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                @if($log->action === 'create')
                                    <span class="badge bg-success">สร้าง</span>
                                @elseif($log->action === 'update')
                                    <span class="badge bg-warning">แก้ไข</span>
                                @elseif($log->action === 'delete')
                                    <span class="badge bg-danger">ลบ</span>
                                @else
                                    <span class="badge bg-secondary">{{ $log->action }}</span>
                                @endif
                            </td>
                            <td>
                                @if($log->attendanceRecord)
                                    <strong>{{ $log->attendanceRecord->employee->full_name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $log->attendanceRecord->employee->employee_code ?? 'N/A' }}</small>
                                @else
                                    <span class="text-muted">ไม่มีข้อมูล</span>
                                @endif
                            </td>
                            <td>
                                @if($log->trip)
                                    <small>
                                        รอบ #{{ $log->trip->id }}<br>
                                        {{ $log->trip->route->name ?? 'N/A' }}<br>
                                        {{ $log->trip->vehicle->license_plate ?? 'N/A' }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($log->old_values)
                                    <small>
                                        @foreach(json_decode($log->old_values, true) as $key => $value)
                                            <strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}<br>
                                        @endforeach
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($log->new_values)
                                    <small>
                                        @foreach(json_decode($log->new_values, true) as $key => $value)
                                            <strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}<br>
                                        @endforeach
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($log->user)
                                    <small>{{ $log->user->name }}</small>
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $log->reason ?? '-' }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                ไม่พบข้อมูล Audit Log ในช่วงเวลาที่เลือก
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $auditLogs->links() }}
</div>

<style>
    .table td {
        vertical-align: middle;
    }
    .table small {
        font-size: 0.875rem;
    }
</style>
@endsection
