@extends('layouts.app')

@section('title', 'สแกน QR Code')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/@zxing/library@0.20.0"></script>
<style>
    #cameraPreview {
        width: 100%;
        max-width: 400px;
        border-radius: 8px;
        border: 3px solid #3498db;
    }

    .scanner-container {
        text-align: center;
        padding: 20px;
    }

    .attendance-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .attendance-item {
        padding: 12px;
        background: #f8f9fa;
        border-left: 4px solid #27ae60;
        margin-bottom: 8px;
        border-radius: 4px;
        animation: slideIn 0.3s ease-in;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .scan-result {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .scan-result.success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .scan-result.error {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .counter {
        font-size: 3rem;
        color: #3498db;
        font-weight: 700;
        text-align: center;
        padding: 20px;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="page-title"><i class="fas fa-qrcode me-2"></i>สแกน QR Code</h2>
        <small class="text-muted">{{ $trip->route->name }} • {{ $trip->vehicle->license_plate }}</small>
    </div>
    <span class="badge bg-success fs-5">
        <i class="fas fa-circle fa-beat"></i> กำลังดำเนินการ
    </span>
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

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-camera me-2"></i>กล้องสแกน QR Code
            </div>
            <div class="card-body">
                <div class="scanner-container">
                    <video id="cameraPreview"></video>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="switchCamera()">
                            <i class="fas fa-sync-alt"></i> เปลี่ยนกล้อง
                        </button>
                    </div>
                </div>

                <div id="scanResult"></div>

                <div class="mt-3">
                    <label class="form-label"><i class="fas fa-keyboard me-1"></i>หรือพิมพ์โทเค็น:</label>
                    <input type="text" id="qrcodeInput" class="form-control form-control-lg" placeholder="พิมพ์ QR code token แล้วกด Enter" autofocus>
                </div>

                <div class="mt-3 d-grid gap-2">
                    <button type="button" class="btn btn-danger btn-lg" onclick="completeTrip()">
                        <i class="fas fa-stop-circle me-1"></i>ปิดรอบและคำนวณค่าโดยสาร
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <i class="fas fa-users me-2"></i>ผู้โดยสาร
            </div>
            <div class="card-body text-center">
                <div class="counter" id="passengerCount">{{ $tripSummary['total_passengers'] ?? 0 }}</div>
                <p class="text-muted">คน / {{ $trip->vehicle->capacity }} ที่นั่ง</p>
                
                <div class="progress mb-3" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" id="capacityProgress" 
                         style="width: {{ $trip->vehicle->capacity > 0 ? round((($tripSummary['total_passengers'] ?? 0) / $trip->vehicle->capacity) * 100) : 0 }}%">
                        {{ $trip->vehicle->capacity > 0 ? round((($tripSummary['total_passengers'] ?? 0) / $trip->vehicle->capacity) * 100) : 0 }}%
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-list me-2"></i>รายชื่อล่าสุด
            </div>
            <div class="card-body p-0">
                <div class="attendance-list" id="attendanceList">
                    @if(!empty($recentRecords) && count($recentRecords) > 0)
                        @foreach($recentRecords as $record)
                            <div class="attendance-item">
                                <strong>{{ $record['employee_code'] }}</strong> - {{ $record['employee_name'] }}<br>
                                <small class="text-muted">{{ $record['scanned_at'] }}</small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center py-4">ยังไม่มีการสแกน</p>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <form action="{{ route('driver.trip.cancel-record', $trip) }}" method="POST">
                    @csrf
                    <input type="hidden" name="reason" id="cancelReason" value="ยกเลิกจากหน้าจอของคนขับ">
                    <button type="button" class="btn btn-warning btn-sm w-100" onclick="cancelLastRecord()">
                        <i class="fas fa-undo me-1"></i>ยกเลิกรายการล่าสุด
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<form id="completeTripForm" action="{{ route('driver.trip.complete', $trip) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="notes" value="">
</form>

@endsection

@section('scripts')
<script>
    const tripId = {{ $trip->id }};
    let codeReader;

    // Initialize camera scanning
    async function initCamera() {
        try {
            codeReader = new ZXing.BrowserMultiFormatReader();
            const videoElement = document.getElementById('cameraPreview');

            const result = await codeReader.decodeOnceFromVideoDevice(undefined, videoElement);
            if (result) {
                processQrcodeToken(result.text);
                // Re-scan after processing
                initCamera();
            }
        } catch (err) {
            if (err.name === 'NotAllowedError') {
                showScanResult('ต้องอนุญาติการเข้าถึงกล้อง', 'error');
            }
            // Retry scanning
            setTimeout(initCamera, 1000);
        }
    }

    // Handle manual input
    document.getElementById('qrcodeInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            processQrcodeToken(this.value.trim());
            this.value = '';
        }
    });

    function processQrcodeToken(token) {
        fetch(`{{ route('driver.trip.scan-process', $trip) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                qrcode_token: token,
                latitude: null,
                longitude: null
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showScanResult(`✓ ${data.data.employee_name}`, 'success');
                updateAttendanceList();
            } else {
                showScanResult(data.message, data.type === 'duplicate' ? 'warning' : 'error');
            }
        })
        .catch(err => {
            showScanResult('เกิดข้อผิดพลาด: ' + err.message, 'error');
        });
    }

    function showScanResult(message, type) {
        const resultDiv = document.getElementById('scanResult');
        resultDiv.innerHTML = `<div class="scan-result ${type}">${message}</div>`;
        setTimeout(() => {
            resultDiv.innerHTML = '';
        }, 3000);
    }

    function updateAttendanceList() {
        fetch(`{{ route('driver.trip.recent-records', $trip) }}`)
            .then(res => res.json())
            .then(data => {
                const count = data.passenger_count;
                const capacity = {{ $trip->vehicle->capacity }};
                
                document.getElementById('passengerCount').innerText = count;
                
                // Update progress bar
                const percentage = capacity > 0 ? Math.round((count / capacity) * 100) : 0;
                const progressBar = document.getElementById('capacityProgress');
                progressBar.style.width = percentage + '%';
                progressBar.innerText = percentage + '%';
                
                // Change color based on capacity
                progressBar.className = 'progress-bar ' + 
                    (percentage >= 90 ? 'bg-danger' : (percentage >= 70 ? 'bg-warning' : 'bg-success'));
                
                const listHtml = data.records.map(r =>
                    `<div class="attendance-item">
                        <strong>${r.employee_code}</strong> - ${r.employee_name}<br>
                        <small class="text-muted">${r.scanned_at}</small>
                    </div>`
                ).join('');
                document.getElementById('attendanceList').innerHTML = listHtml || '<p class="text-muted text-center py-4">ยังไม่มีการสแกน</p>';
            });
    }

    function switchCamera() {
        // Stop current camera
        if (codeReader) {
            codeReader.reset();
        }
        // Restart with different camera
        initCamera();
    }

    function cancelLastRecord() {
        if (confirm('ยกเลิกรายการสแกนล่าสุด?')) {
            const reason = prompt('เหตุผล:', 'ยกเลิกจากหน้าจอของคนขับ');
            if (reason) {
                document.getElementById('cancelReason').value = reason;
                document.querySelector('[onclick="cancelLastRecord()"]').closest('form').submit();
            }
        }
    }

    function completeTrip() {
        if (confirm('ปิดรอบนี้?')) {
            document.getElementById('completeTripForm').submit();
        }
    }

    // Start camera on load
    window.addEventListener('load', initCamera);
</script>
@endsection
