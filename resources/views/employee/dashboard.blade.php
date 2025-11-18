@extends('layouts.app')

@section('title', 'Dashboard - พนักงาน')

@section('content')
<div class="page-title">
    <i class="bi bi-person-badge"></i>
    <div>
        <h2>Dashboard พนักงาน</h2>
        <small class="text-muted">{{ auth()->user()->name }}</small>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-qr-code" style="font-size: 3rem; color: #3498db;"></i>
                <h5 class="mt-3">QR Code ของคุณ</h5>
                <p class="text-muted">สแกนเพื่อขึ้นรถ</p>
                <a href="{{ route('employee.qrcode') }}" class="btn btn-primary">
                    <i class="bi bi-qr-code"></i> ดูหรือพิมพ์
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-clock-history" style="font-size: 3rem; color: #27ae60;"></i>
                <h5 class="mt-3">ประวัติการขึ้นรถ</h5>
                <p class="text-muted">30 วันล่าสุด</p>
                <a href="{{ route('employee.attendance-history') }}" class="btn btn-success">
                    <i class="bi bi-list-check"></i> ดูรายละเอียด
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-calendar-event"></i> การขึ้นรถล่าสุด
    </div>
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>วันที่</th>
                <th>เวลา</th>
                <th>สายรถ</th>
                <th>รถ</th>
                <th>คนขับ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendanceRecords->take(10) as $record)
                <tr>
                    <td>{{ $record->scanned_at->format('d/m/Y') }}</td>
                    <td>{{ $record->scanned_at->format('H:i:s') }}</td>
                    <td>{{ $record->trip->route->name }}</td>
                    <td>{{ $record->trip->vehicle->license_plate }}</td>
                    <td>{{ $record->trip->driver->name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        ยังไม่มีประวัติการขึ้นรถ
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
