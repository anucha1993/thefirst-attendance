@extends('layouts.app')

@section('title', 'เริ่มรอบใหม่')

@section('content')
<style>
    .start-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        padding: 2rem 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .start-header h2 {
        font-size: 1.75rem;
        margin: 0;
        font-weight: 700;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.75rem;
        font-size: 1.1rem;
    }

    .form-select {
        padding: 1rem;
        font-size: 1.1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        background-color: #f7fafc;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        border-color: #11998e;
        box-shadow: 0 0 0 3px rgba(17, 153, 142, 0.1);
        background-color: white;
    }

    .btn-start {
        padding: 1.25rem;
        font-size: 1.3rem;
        font-weight: 700;
        border-radius: 12px;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        box-shadow: 0 8px 20px rgba(17, 153, 142, 0.3);
        transition: all 0.3s ease;
    }

    .btn-start:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(17, 153, 142, 0.4);
    }

    .btn-start i {
        font-size: 1.5rem;
        margin-right: 0.5rem;
    }

    .instruction-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .instruction-card h6 {
        font-weight: 700;
        margin-bottom: 1rem;
        font-size: 1.15rem;
    }

    .instruction-card ol, .instruction-card ul {
        padding-left: 1.25rem;
        margin-bottom: 1rem;
    }

    .instruction-card li {
        margin-bottom: 0.75rem;
        font-size: 1rem;
    }

    .instruction-card hr {
        border-color: rgba(255,255,255,0.3);
        margin: 1.5rem 0;
    }

    @media (min-width: 768px) {
        .start-header {
            padding: 3rem 2rem;
        }

        .start-header h2 {
            font-size: 2.5rem;
        }
    }
</style>

<!-- Content Start -->
<!-- Header -->
<div class="start-header">
    <i class="fas fa-play-circle fa-3x mb-3"></i>
    <h2>เริ่มรอบใหม่</h2>
    <p class="mb-0 opacity-90">เลือกรถและสายรถเพื่อเริ่มรอบ</p>
</div>

<!-- Form Card -->
<div class="form-card">
    <form action="{{ route('driver.trip.start') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="vehicle_id" class="form-label">
                <i class="fas fa-car me-2 text-primary"></i>เลือกรถ
            </label>
            <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                <option value="">-- เลือกรถของคุณ --</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>
                        {{ $vehicle->license_plate }} - {{ $vehicle->vehicle_model }} ({{ $vehicle->capacity }} ที่นั่ง)
                    </option>
                @endforeach
            </select>
            @error('vehicle_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="route_id" class="form-label">
                <i class="fas fa-route me-2 text-success"></i>เลือกสายรถ
            </label>
            <select name="route_id" id="route_id" class="form-select @error('route_id') is-invalid @enderror" required>
                <option value="">-- เลือกสายรถ --</option>
                @foreach($routes as $route)
                    <option value="{{ $route->id }}" @selected(old('route_id') == $route->id)>
                        {{ $route->name }} ({{ $route->pickupLocation->name }} → {{ $route->dropoffLocation->name }})
                    </option>
                @endforeach
            </select>
            @error('route_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success btn-start w-100">
            <i class="fas fa-play-fill"></i>เริ่มรอบ
        </button>
    </form>
</div>

<!-- Instructions -->
<div class="instruction-card">
    <h6><i class="fas fa-info-circle me-2"></i>ขั้นตอนการเริ่มรอบ</h6>
    <ol>
        <li>เลือกรถที่คุณจะขับ</li>
        <li>เลือกสายรถที่จะวิ่ง</li>
        <li>กดปุ่ม "เริ่มรอบ"</li>
        <li>จะนำคุณไปยังหน้าสแกน QR Code</li>
    </ol>
    
    <hr>
    
    <h6><i class="fas fa-qrcode me-2"></i>ระหว่างรอบ</h6>
    <ul>
        <li>สแกน QR Code ของพนักงาน</li>
        <li>ติดตามจำนวนผู้โดยสารแบบเรียลไทม์</li>
        <li>สามารถยกเลิกรายการผิดพลาดได้ทันที</li>
        <li>กดปิดรอบเมื่อเสร็จสิ้น</li>
    </ul>
</div>
@endsection
