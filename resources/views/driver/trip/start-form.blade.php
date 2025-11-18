@extends('layouts.app')

@section('title', 'เริ่มรอบใหม่')

@section('content')
<div class="page-title">
    <i class="bi bi-play-circle"></i>
    <h2>เริ่มรอบใหม่</h2>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clipboard-check"></i> กำลังจะเริ่มรอบ
            </div>
            <div class="card-body">
                <form action="{{ route('driver.trip.start') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="vehicle_id" class="form-label">เลือกรถ</label>
                        <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                            <option value="">-- เลือกรถของคุณ --</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>
                                    {{ $vehicle->license_plate }} ({{ $vehicle->vehicle_model }}) - ที่นั่ง: {{ $vehicle->capacity }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="route_id" class="form-label">เลือกสายรถ</label>
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

                    <button type="submit" class="btn btn-success btn-lg w-100">
                        <i class="bi bi-play-fill"></i> เริ่มรอบ
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> คำแนะนำ
            </div>
            <div class="card-body">
                <h6>ขั้นตอนการเริ่มรอบ:</h6>
                <ol>
                    <li>เลือกรถที่คุณจะขับ</li>
                    <li>เลือกสายรถที่จะวิ่ง</li>
                    <li>กดปุ่ม "เริ่มรอบ"</li>
                    <li>จะนำคุณไปยังหน้าสแกน QR Code</li>
                </ol>
                <hr>
                <h6>ระหว่างรอบ:</h6>
                <ul>
                    <li>สแกน QR Code ของพนักงาน</li>
                    <li>ติดตามจำนวนคนในรอบ</li>
                    <li>สามารถยกเลิกรายการผิดพลาดได้</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
