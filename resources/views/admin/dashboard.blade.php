@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-title">
    <i class="bi bi-speedometer2"></i>
    <div>
        <h2>Admin Dashboard</h2>
        <small class="text-muted">ส่วนประกอบควบคุมระบบ</small>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-number">{{ $total_employees }}</div>
            <div class="stat-label">พนักงานทั้งหมด</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-number">{{ $total_vehicles }}</div>
            <div class="stat-label">รถทั้งหมด</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-number">{{ $total_routes }}</div>
            <div class="stat-label">สายรถทั้งหมด</div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-number">{{ $today_trips }}</div>
            <div class="stat-label">รอบวันนี้</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-gear"></i> ตั้งค่าระบบ
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.locations.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        จุดรับ–ส่ง
                        <span class="badge bg-secondary">{{ \App\Models\Location::count() }}</span>
                    </a>
                    <a href="{{ route('admin.routes.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        สายรถ
                        <span class="badge bg-secondary">{{ \App\Models\Route::count() }}</span>
                    </a>
                    <a href="{{ route('admin.vehicles.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        รถและคนขับ
                        <span class="badge bg-secondary">{{ \App\Models\Vehicle::count() }}</span>
                    </a>
                    <a href="{{ route('admin.fare-rules.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        กฎค่าโดยสาร
                        <span class="badge bg-secondary">{{ \App\Models\FareRule::count() }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-people"></i> จัดการพนักงาน
            </div>
            <div class="card-body">
                <p>จัดการข้อมูลพนักงานและ QR Code</p>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-people"></i> ไปยังหน้าจัดการพนักงาน
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
