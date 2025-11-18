@extends('layouts.app')

@section('title', 'รอบวันนี้')

@section('content')
<style>
    .trips-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }

    .trips-header h2 {
        font-size: 1.5rem;
        margin: 0 0 0.5rem 0;
        font-weight: 700;
    }

    .trips-header .date {
        font-size: 1rem;
        opacity: 0.95;
    }

    .trips-header .count-badge {
        display: inline-block;
        background: rgba(255,255,255,0.3);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        margin-top: 0.75rem;
        font-weight: 600;
    }

    .trip-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .trip-card.active {
        border-color: #38ef7d;
        box-shadow: 0 8px 20px rgba(56, 239, 125, 0.2);
    }

    .trip-card.completed {
        border-color: #e2e8f0;
    }

    .trip-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f7fafc;
    }

    .trip-header .route-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .status-badge.active {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .status-badge.completed {
        background: #48bb78;
        color: white;
    }

    .trip-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .info-item {
        text-align: center;
    }

    .info-item .label {
        font-size: 0.85rem;
        color: #718096;
        margin-bottom: 0.25rem;
    }

    .info-item .value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2d3748;
    }

    .route-display {
        background: #f7fafc;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .route-display .location {
        flex: 1;
        text-align: center;
        font-size: 0.9rem;
    }

    .route-display .arrow {
        font-size: 1.25rem;
        color: #a0aec0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .stat-box {
        background: #f7fafc;
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
    }

    .stat-box .number {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-box .label {
        font-size: 0.8rem;
        color: #718096;
    }

    .stat-box.primary .number { color: #667eea; }
    .stat-box.success .number { color: #48bb78; }
    .stat-box.info .number { color: #4299e1; }

    .action-button {
        display: block;
        width: 100%;
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .action-button.scan {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .action-button.view {
        background: #667eea;
        color: white;
    }

    .action-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .empty-state i {
        font-size: 4rem;
        color: #cbd5e0;
        margin-bottom: 1.5rem;
    }

    .empty-state h5 {
        color: #718096;
        margin-bottom: 1rem;
    }

    .summary-card {
        background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-top: 1.5rem;
    }

    .summary-card h5 {
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .summary-item {
        text-align: center;
    }

    .summary-item i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .summary-item .number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .summary-item .label {
        font-size: 0.9rem;
        opacity: 0.85;
    }

    @media (min-width: 768px) {
        .trips-header {
            padding: 2rem;
        }

        .trips-header h2 {
            font-size: 2rem;
        }

        .summary-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>

<!-- Content Start -->
<!-- Header -->
<div class="trips-header">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <h2><i class="fas fa-list me-2"></i>รอบวันนี้</h2>
        <a href="{{ route('driver.dashboard') }}" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-1"></i>กลับ
        </a>
    </div>
    <div class="date">{{ now()->locale('th')->translatedFormat('l, d F Y') }}</div>
    <div class="count-badge">
        <i class="fas fa-route me-2"></i>ทั้งหมด {{ $trips->count() }} รอบ
    </div>
</div>

@if($trips->isEmpty())
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h5>ยังไม่มีรอบวันนี้</h5>
        <p class="text-muted mb-4">เริ่มรอบใหม่เพื่อเริ่มทำงาน</p>
        <a href="{{ route('driver.trip.start-form') }}" class="btn btn-success btn-lg">
            <i class="fas fa-play-circle me-2"></i>เริ่มรอบใหม่
        </a>
    </div>
@else
    @foreach($trips as $trip)
        <div class="trip-card {{ $trip->status }}">
            <!-- Trip Header -->
            <div class="trip-header">
                <div class="route-name">
                    <i class="fas fa-route me-2"></i>{{ $trip->route->name }}
                </div>
                <div class="status-badge {{ $trip->status }}">
                    @if($trip->status === 'active')
                        <i class="fas fa-spinner fa-spin me-1"></i>กำลังทำงาน
                    @elseif($trip->status === 'completed')
                        <i class="fas fa-check-circle me-1"></i>เสร็จสิ้น
                    @endif
                </div>
            </div>

            <!-- Trip Info Grid -->
            <div class="trip-info">
                <div class="info-item">
                    <div class="label">รถ</div>
                    <div class="value">{{ $trip->vehicle->license_plate }}</div>
                </div>
                <div class="info-item">
                    <div class="label">เวลาเริ่ม</div>
                    <div class="value">{{ $trip->started_at->format('H:i') }} น.</div>
                </div>
            </div>

            <!-- Route Display -->
            <div class="route-display">
                <div class="location">
                    <i class="fas fa-map-marker-alt text-success me-1"></i>
                    {{ $trip->route->pickupLocation->name }}
                </div>
                <div class="arrow">→</div>
                <div class="location">
                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                    {{ $trip->route->dropoffLocation->name }}
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-box primary">
                    <div class="number">{{ $trip->attendanceRecords->count() }}</div>
                    <div class="label">ผู้โดยสาร</div>
                </div>
                <div class="stat-box success">
                    <div class="number">{{ $trip->vehicle->capacity }}</div>
                    <div class="label">ที่นั่ง</div>
                </div>
                <div class="stat-box info">
                    <div class="number">{{ number_format($trip->total_fare ?? 0, 0) }}</div>
                    <div class="label">บาท</div>
                </div>
            </div>

            @if($trip->completed_at)
                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle me-1"></i>
                    <small>เสร็จสิ้นเมื่อ {{ $trip->completed_at->format('H:i') }} น.</small>
                </div>
            @endif

            <!-- Action Button -->
            @if($trip->status === 'active')
                <a href="{{ route('driver.trip.scan', $trip) }}" class="action-button scan">
                    <i class="fas fa-qrcode me-2"></i>เปิดหน้าสแกน
                </a>
            @elseif($trip->status === 'completed')
                <a href="{{ route('driver.trip-summary', $trip) }}" class="action-button view">
                    <i class="fas fa-file-alt me-2"></i>ดูสรุปรอบ
                </a>
            @endif
        </div>
    @endforeach

    <!-- Summary Card -->
    <div class="summary-card">
        <h5><i class="fas fa-chart-bar me-2"></i>สรุปวันนี้</h5>
        <div class="summary-grid">
            <div class="summary-item">
                <i class="fas fa-route text-primary"></i>
                <div class="number">{{ $trips->count() }}</div>
                <div class="label">รอบทั้งหมด</div>
            </div>
            <div class="summary-item">
                <i class="fas fa-check-circle text-success"></i>
                <div class="number">{{ $trips->where('status', 'completed')->count() }}</div>
                <div class="label">เสร็จสิ้น</div>
            </div>
            <div class="summary-item">
                <i class="fas fa-users text-info"></i>
                <div class="number">{{ $trips->sum(fn($t) => $t->attendanceRecords->count()) }}</div>
                <div class="label">ผู้โดยสาร</div>
            </div>
            <div class="summary-item">
                <i class="fas fa-coins text-warning"></i>
                <div class="number">{{ number_format($trips->sum('total_fare') ?? 0, 0) }}</div>
                <div class="label">บาท</div>
            </div>
        </div>
    </div>
@endif

@endsection
