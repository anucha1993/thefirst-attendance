@extends('layouts.guest')

@section('title', 'รอบวิ่งรับส่ง')

@section('content')
<style>
    body {
        background: #f0f2f5;
        margin: 0;
        padding: 0;
    }

    .container-main {
        max-width: 600px;
        margin: 0 auto;
        padding: 15px;
        min-height: 100vh;
    }

    /* Header */
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.2rem;
        border-radius: 15px;
        margin-bottom: 1rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
    }

    .btn-logout {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    /* Add Button */
    .btn-add-trip {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        border: none;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        font-size: 1.2rem;
        font-weight: 700;
        width: 100%;
        margin-bottom: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .btn-add-trip i {
        margin-right: 0.5rem;
        font-size: 1.4rem;
    }

    /* Trip Card */
    .trip-card {
        background: white;
        border-radius: 12px;
        padding: 1.2rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        cursor: pointer;
        transition: all 0.2s;
    }

    .trip-card:active {
        transform: scale(0.98);
    }

    .trip-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.8rem;
    }

    .trip-time {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2d3748;
    }

    .trip-status {
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .status-active {
        background: #d4edda;
        color: #155724;
    }

    .status-completed {
        background: #d1ecf1;
        color: #0c5460;
    }

    .trip-info {
        font-size: 1.1rem;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }

    .trip-info i {
        width: 24px;
        color: #667eea;
    }

    .trip-passenger-count {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        padding: 0.8rem;
        border-radius: 8px;
        text-align: center;
        margin-top: 0.8rem;
        font-weight: 700;
        font-size: 1.1rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #a0aec0;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    .empty-state p {
        font-size: 1.1rem;
        margin: 0;
    }

    /* Modal */
    .modal-content {
        border-radius: 15px;
    }

    .modal-header {
        border-radius: 15px 15px 0 0;
    }

    .form-control-lg {
        font-size: 1.1rem;
        padding: 0.8rem 1rem;
        border-radius: 8px;
    }

    .btn-lg {
        padding: 0.8rem 1.5rem;
        font-size: 1.2rem;
        border-radius: 10px;
        font-weight: 600;
    }
</style>

<div class="container-main">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 style="margin: 0;"><i class="fas fa-bus me-2"></i>รอบวิ่งรับส่ง</h1>
            <small style="opacity: 0.8; font-size: 0.85rem;">
                <i class="fas fa-user-circle me-1"></i>{{ auth()->user()->name }}
            </small>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="border-radius: 10px; margin-bottom: 1rem;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="border-radius: 10px; margin-bottom: 1rem;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Trip List -->
    @php
        $todayTrips = \App\Models\Trip::where('driver_id', auth()->id())
            ->whereDate('started_at', today())
            ->with(['route.pickupLocation', 'route.dropoffLocation', 'vehicle'])
            ->orderBy('started_at', 'desc')
            ->get();
        
        $hasActiveTrip = $todayTrips->where('status', 'active')->count() > 0;
    @endphp

    <!-- Add Trip Button -->
    @if($hasActiveTrip)
        <div class="alert alert-warning" style="border-radius: 10px; margin-bottom: 1rem;">
            <i class="fas fa-info-circle me-2"></i>คุณมีรอบที่กำลังดำเนินการอยู่ กรุณาปิดรอบก่อนเพื่อสร้างรอบใหม่
        </div>
    @else
        <button type="button" class="btn-add-trip" data-bs-toggle="modal" data-bs-target="#addTripModal">
            <i class="fas fa-plus-circle"></i>เพิ่มรอบใหม่
        </button>
    @endif

    @if($todayTrips->count() > 0)
        @foreach($todayTrips as $trip)
            <div class="trip-card" onclick="window.location.href='{{ route('driver.trip.scan', $trip) }}'">
                <div class="trip-card-header">
                    <div class="trip-time">
                        <i class="fas fa-clock me-2"></i>{{ $trip->started_at->format('H:i น.') }}
                    </div>
                    <div class="trip-status {{ $trip->status === 'active' ? 'status-active' : 'status-completed' }}">
                        {{ $trip->status === 'active' ? 'กำลังวิ่ง' : 'เสร็จสิ้น' }}
                    </div>
                </div>

                <div class="trip-info">
                    <i class="fas fa-route"></i>{{ $trip->route->name }}
                </div>

                <div class="trip-info">
                    <i class="fas fa-car"></i>{{ $trip->vehicle->license_plate }}
                </div>

                @if($trip->status === 'active')
                    <div class="trip-passenger-count">
                        <i class="fas fa-users me-2"></i>
                        {{ $trip->attendanceRecords()->count() }} / {{ $trip->vehicle->capacity }} คน
                    </div>
                @else
                    <div class="trip-passenger-count" style="background: #6c757d;">
                        <i class="fas fa-check-circle me-2"></i>
                        ผู้โดยสาร {{ $trip->passenger_count }} คน
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <p>ยังไม่มีรอบวันนี้</p>
            <p><small>กดปุ่ม "เพิ่มรอบใหม่" เพื่อเริ่มต้น</small></p>
        </div>
    @endif
</div>

<!-- Add Trip Modal -->
<div class="modal fade" id="addTripModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>เพิ่มรอบใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('driver.trip.start') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-car me-2 text-primary"></i>เลือกรถ
                        </label>
                        <select name="vehicle_id" class="form-control form-control-lg" required>
                            <option value="">-- เลือกรถ --</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">
                                    {{ $vehicle->license_plate }} ({{ $vehicle->capacity }} ที่นั่ง)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-route me-2 text-success"></i>เลือกสาย
                        </label>
                        <select name="route_id" class="form-control form-control-lg" required>
                            <option value="">-- เลือกสาย --</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}">{{ $route->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-check me-2"></i>เริ่มรอบ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
