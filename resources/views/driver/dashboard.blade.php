@extends('layouts.app')

@section('title', 'Dashboard - Driver')

@section('content')
<div class="mb-4">
    <h2 class="page-title"><i class="fas fa-tachometer-alt me-2"></i>Driver Dashboard</h2>
    <p class="text-muted">ยินดีต้อนรับ, {{ auth()->user()->name }}</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <i class="fas fa-car fa-2x text-primary mb-3"></i>
            <div class="stat-number">{{ $vehicles->count() }}</div>
            <div class="stat-label">รถของคุณ</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <i class="fas fa-route fa-2x text-success mb-3"></i>
            <div class="stat-number">{{ $todayTrips }}</div>
            <div class="stat-label">รอบวันนี้</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <i class="fas fa-clock fa-2x text-warning mb-3"></i>
            <div class="stat-number">{{ now()->format('H:i') }}</div>
            <div class="stat-label">เวลาปัจจุบัน</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <i class="fas fa-calendar fa-2x text-info mb-3"></i>
            <div class="stat-number">{{ now()->format('d/m') }}</div>
            <div class="stat-label">{{ now()->locale('th')->translatedFormat('l') }}</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('driver.trip.start-form') }}" class="btn btn-lg btn-success w-100 py-4">
                            <i class="fas fa-play-circle fa-2x d-block mb-2"></i>
                            <strong>เริ่มรอบใหม่</strong>
                            <p class="small mb-0 mt-2">เริ่มรอบขับรถรับส่งพนักงาน</p>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('driver.today-trips') }}" class="btn btn-lg btn-info w-100 py-4">
                            <i class="fas fa-list fa-2x d-block mb-2"></i>
                            <strong>ดูรอบวันนี้</strong>
                            <p class="small mb-0 mt-2">ดูประวัติรอบที่ขับวันนี้</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Your Vehicles -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-car me-2"></i>รถที่มอบหมายให้คุณ</h5>
            </div>
            <div class="card-body">
                @forelse($vehicles as $vehicle)
                    <div class="d-flex align-items-center justify-content-between p-3 mb-2 border rounded">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-car fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0"><strong>{{ $vehicle->license_plate }}</strong></h6>
                                <small class="text-muted">{{ $vehicle->vehicle_model }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $vehicle->status === 'active' ? 'success' : 'secondary' }} mb-1">
                                {{ $vehicle->status === 'active' ? 'พร้อมใช้งาน' : 'ไม่พร้อมใช้' }}
                            </span>
                            <br>
                            <small class="text-muted">ที่นั่ง: {{ $vehicle->capacity }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <p>ยังไม่มีรถที่มอบหมายให้คุณ<br>กรุณาติดต่อผู้ดูแลระบบ</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="col-md-4">
        <!-- Today's Summary -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>สรุปวันนี้</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div style="font-size: 3rem; font-weight: bold; color: #17a2b8;">
                        {{ $todayTrips }}
                    </div>
                    <p class="text-muted mb-0">รอบทั้งหมด</p>
                </div>
                
                @if($todayTrips > 0)
                    <a href="{{ route('driver.today-trips') }}" class="btn btn-outline-info btn-sm w-100">
                        <i class="fas fa-eye me-1"></i>ดูรายละเอียด
                    </a>
                @else
                    <div class="alert alert-light text-center mb-0">
                        <i class="fas fa-moon"></i>
                        <p class="small mb-0 mt-2">ยังไม่มีรอบวันนี้</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Instructions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>คำแนะนำ</h6>
            </div>
            <div class="card-body">
                <div class="small">
                    <h6 class="text-primary">ขั้นตอนการทำงาน:</h6>
                    <ol class="ps-3">
                        <li class="mb-2">กดปุ่ม "เริ่มรอบใหม่"</li>
                        <li class="mb-2">เลือกรถและเส้นทาง</li>
                        <li class="mb-2">สแกน QR Code พนักงาน</li>
                        <li class="mb-2">กดปุ่ม "ปิดรอบ"</li>
                        <li>ระบบจะคำนวณค่าโดยสารอัตโนมัติ</li>
                    </ol>

                    <hr>

                    <h6 class="text-success mt-3">เคล็ดลับ:</h6>
                    <ul class="ps-3">
                        <li class="mb-1">ตรวจสอบรถก่อนออกรอบทุกครั้ง</li>
                        <li class="mb-1">สแกน QR Code ให้ครบทุกคน</li>
                        <li>สามารถยกเลิกรายการที่สแกนผิดได้</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Auto refresh every 5 minutes
setTimeout(function() {
    location.reload();
}, 300000);
</script>
@endsection
