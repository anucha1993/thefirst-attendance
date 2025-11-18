@extends('layouts.app')

@section('title', 'Dashboard - Driver')

@section('content')
<style>
    .mobile-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .mobile-hero h2 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .time-display {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 1rem 0;
    }

    .action-button {
        display: block;
        padding: 2rem 1rem;
        margin-bottom: 1rem;
        border-radius: 12px;
        text-decoration: none;
        color: white;
        text-align: center;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }

    .action-button:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.2);
        color: white;
    }

    .action-button i {
        font-size: 3rem;
        display: block;
        margin-bottom: 1rem;
    }

    .action-button .title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .action-button .subtitle {
        font-size: 0.95rem;
        opacity: 0.9;
    }

    .action-start {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .action-view {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .mini-stat {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .mini-stat i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        display: block;
    }

    .mini-stat .number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }

    .mini-stat .label {
        font-size: 0.95rem;
        color: #718096;
        margin-top: 0.25rem;
    }

    .vehicle-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 1rem;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        display: flex;
        align-items: center;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .vehicle-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .vehicle-card i {
        font-size: 3rem;
        color: rgba(255,255,255,0.9);
        margin-right: 1.25rem;
        position: relative;
        z-index: 1;
    }

    .vehicle-info {
        flex: 1;
        position: relative;
        z-index: 1;
    }

    .vehicle-info h5 {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
        font-weight: 700;
        color: white;
    }

    .vehicle-info .model {
        font-size: 1rem;
        color: rgba(255,255,255,0.85);
    }

    .vehicle-status {
        text-align: right;
        position: relative;
        z-index: 1;
    }

    .vehicle-status .badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }

    .vehicle-status .capacity {
        font-size: 0.95rem;
        margin-top: 0.5rem;
        color: rgba(255,255,255,0.9);
    }

    .tips-card {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        color: #2d3748;
        padding: 1.5rem;
        border-radius: 12px;
        margin-top: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .tips-card h6 {
        font-weight: 700;
        margin-bottom: 1rem;
        color: #2d3748;
    }

    .tips-card ol, .tips-card ul {
        padding-left: 1.25rem;
        margin-bottom: 0;
    }

    .tips-card li {
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        color: #4a5568;
    }

    .tips-card hr {
        border-color: rgba(45, 55, 72, 0.2);
    }

    .tips-card .small {
        color: #4a5568;
    }

    @media (min-width: 768px) {
        .mobile-hero {
            padding: 3rem 2rem;
        }

        .stat-row {
            grid-template-columns: repeat(2, 1fr);
        }

        .action-button {
            display: inline-block;
            width: 48%;
            margin-right: 1%;
        }
    }
</style>

<!-- Mobile Hero Header -->
<div class="mobile-hero">
    <h2><i class="fas fa-user-circle me-2"></i>{{ auth()->user()->name }}</h2>
    <div class="time-display">{{ now()->format('H:i') }}</div>
    <div>{{ now()->locale('th')->translatedFormat('l, d F Y') }}</div>
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

<!-- Statistics Mini Cards - Only show meaningful stats -->
<div class="stat-row">
    <div class="mini-stat">
        <i class="fas fa-car text-primary"></i>
        <div class="number">{{ $vehicles->count() }}</div>
        <div class="label">รถของคุณ</div>
    </div>
    <div class="mini-stat">
        <i class="fas fa-route text-success"></i>
        <div class="number">{{ $todayTrips }}</div>
        <div class="label">รอบวันนี้</div>
    </div>
</div>

<!-- Quick Action Buttons -->
<a href="{{ route('driver.trip.start-form') }}" class="action-button action-start">
    <i class="fas fa-play-circle"></i>
    <div class="title">เริ่มรอบใหม่</div>
    <div class="subtitle">เริ่มรอบขับรถรับส่งพนักงาน</div>
</a>

<a href="{{ route('driver.today-trips') }}" class="action-button action-view">
    <i class="fas fa-list"></i>
    <div class="title">ดูรอบวันนี้</div>
    <div class="subtitle">ดูประวัติรอบที่ขับวันนี้ ({{ $todayTrips }} รอบ)</div>
</a>

<!-- Assigned Vehicles -->
<h5 class="mb-3 fw-bold"><i class="fas fa-car me-2"></i>รถที่มอบหมายให้คุณ</h5>

@forelse($vehicles as $vehicle)
    <div class="vehicle-card">
        <i class="fas fa-car"></i>
        <div class="vehicle-info">
            <h5>{{ $vehicle->license_plate }}</h5>
            <div class="model">{{ $vehicle->vehicle_model }}</div>
        </div>
        <div class="vehicle-status">
            <span class="badge bg-{{ $vehicle->status === 'active' ? 'success' : 'light text-dark' }}">
                {{ $vehicle->status === 'active' ? '✓ พร้อมใช้' : 'ไม่พร้อม' }}
            </span>
            <div class="capacity"><i class="fas fa-users me-1"></i>{{ $vehicle->capacity }} ที่นั่ง</div>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-5">
        <i class="fas fa-info-circle fa-3x mb-3"></i>
        <p>ยังไม่มีรถที่มอบหมายให้คุณ<br>กรุณาติดต่อผู้ดูแลระบบ</p>
    </div>
@endforelse

<!-- Tips Card -->
<div class="tips-card">
    <h6><i class="fas fa-lightbulb me-2"></i>คำแนะนำการใช้งาน</h6>
    <ol class="mb-3">
        <li>กดปุ่ม "เริ่มรอบใหม่" เพื่อเริ่มงาน</li>
        <li>เลือกรถและเส้นทาง</li>
        <li>สแกน QR Code พนักงานที่ขึ้นรถ</li>
        <li>กดปุ่ม "ปิดรอบ" เมื่อเสร็จสิ้น</li>
        <li>ระบบคำนวณค่าโดยสารอัตโนมัติ</li>
    </ol>
    <hr style="border-color: rgba(255,255,255,0.3); margin: 1rem 0;">
    <div class="small">
        <strong>💡 เคล็ดลับ:</strong> สามารถยกเลิกรายการที่สแกนผิดได้ทันที
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
