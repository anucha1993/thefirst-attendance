@extends('layouts.app')

@section('title', 'สแกน QR Code')

@section('content')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<style>
    /* Mobile-First Design */
    body {
        background: #f5f5f5;
    }
    
    #qr-reader {
        width: 100%;
        max-width: 100%;
        border-radius: 12px;
        overflow: hidden;
        border: 3px solid #3498db;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    #qr-reader video {
        width: 100% !important;
        height: auto !important;
        border-radius: 8px;
    }
    
    #qr-reader__dashboard_section_csr {
        display: none !important;
    }

    .scanner-container {
        text-align: center;
        padding: 15px;
        background: white;
    }

    .attendance-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .attendance-item {
        padding: 15px;
        background: white;
        border-left: 5px solid #27ae60;
        margin-bottom: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        animation: slideIn 0.3s ease-in;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .scan-result {
        padding: 20px;
        border-radius: 12px;
        margin: 15px 0;
        font-weight: 600;
        font-size: 1.1rem;
        text-align: center;
    }

    .scan-result.success {
        background: #d4edda;
        border: 2px solid #28a745;
        color: #155724;
    }

    .scan-result.error {
        background: #f8d7da;
        border: 2px solid #dc3545;
        color: #721c24;
    }

    .counter {
        font-size: 5rem;
        color: #3498db;
        font-weight: 700;
        text-align: center;
        padding: 30px 20px;
        line-height: 1;
    }
    
    .counter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        margin-bottom: 15px;
    }
    
    .counter-card .counter {
        color: white;
        font-size: 4.5rem;
        margin: 0;
    }
    
    .btn-xl {
        padding: 18px 24px;
        font-size: 1.3rem;
        border-radius: 12px;
        font-weight: 600;
    }
    
    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 15px;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
        padding: 15px 20px;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    /* Fixed bottom action bar */
    .action-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 15px;
        box-shadow: 0 -4px 12px rgba(0,0,0,0.1);
        z-index: 1000;
    }
    
    @media (min-width: 768px) {
        .action-bar {
            position: relative;
            box-shadow: none;
        }
    }
    
    .page-content {
        padding-bottom: 100px; /* Space for fixed button */
    }
    
    @media (min-width: 768px) {
        .page-content {
            padding-bottom: 20px;
        }
    }
</style>

<!-- Content Start -->
<div class="page-content">
    <!-- Mobile Header -->
    <div class="mb-3">
        <h3 class="fw-bold mb-1"><i class="fas fa-qrcode me-2"></i>สแกน QR Code</h3>
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">{{ $trip->route->name }} • {{ $trip->vehicle->license_plate }}</small>
            <span class="badge bg-success fs-6">
                <i class="fas fa-circle fa-beat"></i> กำลังทำงาน
            </span>
        </div>
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

    <!-- Passenger Counter - Full width on mobile -->
    <div class="counter-card">
        <div class="counter" id="passengerCount">{{ $tripSummary['total_passengers'] ?? 0 }}</div>
        <h5 class="mb-2">ผู้โดยสาร</h5>
        <p class="mb-3 opacity-75">{{ $trip->vehicle->capacity }} ที่นั่ง</p>
        
        <div class="progress" style="height: 30px; background: rgba(255,255,255,0.3);">
            <div class="progress-bar bg-success" role="progressbar" id="capacityProgress" 
                 style="width: {{ $trip->vehicle->capacity > 0 ? round((($tripSummary['total_passengers'] ?? 0) / $trip->vehicle->capacity) * 100) : 0 }}%; font-size: 1.1rem; font-weight: 600;">
                {{ $trip->vehicle->capacity > 0 ? round((($tripSummary['total_passengers'] ?? 0) / $trip->vehicle->capacity) * 100) : 0 }}%
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Camera Scanner -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-camera me-2"></i>กล้องสแกน QR Code
                </div>
                <div class="card-body p-2">
                    <div class="scanner-container">
                        <div id="qr-reader"></div>
                        <div class="mt-3" id="camera-status">
                            <div class="alert alert-info">
                                <i class="fas fa-camera me-2"></i>
                                <strong>กำลังเตรียมกล้อง...</strong><br>
                                <small>หากไม่แสดงกล้อง กรุณากดปุ่มด้านล่าง</small>
                            </div>
                            <button type="button" class="btn btn-primary btn-lg w-100" onclick="requestCameraPermission()">
                                <i class="fas fa-video me-2"></i>เปิดกล้อง
                            </button>
                        </div>
                        <div class="mt-3" id="camera-controls" style="display:none;">
                            <button type="button" class="btn btn-outline-secondary btn-lg" onclick="switchCamera()">
                                <i class="fas fa-sync-alt me-1"></i> เปลี่ยนกล้อง
                            </button>
                        </div>
                    </div>

                    <div id="scanResult"></div>

                    <div class="mt-3">
                        <label class="form-label fw-bold"><i class="fas fa-keyboard me-1"></i>หรือพิมพ์โทเค็น:</label>
                        <input type="text" id="qrcodeInput" class="form-control form-control-lg" 
                               placeholder="พิมพ์ QR code token" autofocus 
                               style="font-size: 1.2rem; padding: 15px;">
                        <small class="text-muted">กด Enter เพื่อยืนยัน</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Scans List -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-list me-2"></i>รายชื่อล่าสุด
                    <button type="button" class="btn btn-sm btn-warning float-end" onclick="cancelLastRecord()">
                        <i class="fas fa-undo me-1"></i>ยกเลิกล่าสุด
                    </button>
                </div>
                <div class="card-body p-2">
                    <div class="attendance-list" id="attendanceList">
                        @if(!empty($recentRecords) && count($recentRecords) > 0)
                            @foreach($recentRecords as $record)
                                <div class="attendance-item">
                                    <div class="fw-bold fs-5">{{ $record['employee_code'] }}</div>
                                    <div>{{ $record['employee_name'] }}</div>
                                    <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $record['scanned_at'] }}</small>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>ยังไม่มีการสแกน</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fixed Bottom Action Bar (Mobile) -->
<div class="action-bar">
    <button type="button" class="btn btn-danger btn-xl w-100" onclick="completeTrip()">
        <i class="fas fa-stop-circle me-2"></i>ปิดรอบและคำนวณค่าโดยสาร
    </button>
</div>

<form id="completeTripForm" action="{{ route('driver.trip.complete', $trip) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="notes" value="">
</form>

<form id="cancelRecordForm" action="{{ route('driver.trip.cancel-record', $trip) }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="reason" value="ยกเลิกจากหน้าจอของคนขับ">
</form>

@endsection

@section('scripts')
<script>
    const tripId = {{ $trip->id }};
    let html5QrCode;
    let cameras = [];
    let currentCameraIndex = 0;
    let cameraPermissionGranted = false;
    let pendingRecord = null;

    // Request camera permission manually
    async function requestCameraPermission() {
        try {
            document.getElementById('camera-status').innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    <strong>กำลังขออนุญาตกล้อง...</strong><br>
                    <small>กรุณากด "อนุญาต" เมื่อ browser ถาม</small>
                </div>
            `;
            
            // Force request permission
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'environment' } 
            });
            
            // Stop the stream (we just needed permission)
            stream.getTracks().forEach(track => track.stop());
            
            cameraPermissionGranted = true;
            document.getElementById('camera-status').style.display = 'none';
            
            // Now start the QR scanner
            await initCamera();
            
        } catch (err) {
            console.error('Permission error:', err);
            let errorMsg = '';
            
            if (err.name === 'NotAllowedError') {
                errorMsg = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>ไม่ได้รับอนุญาตกล้อง</strong><br>
                        <small>วิธีแก้ไข:</small><br>
                        <ol class="mb-0 mt-2" style="text-align: left;">
                            <li>คลิกไอคอนกุญแจ/กล้อง ข้างซ้าย URL bar</li>
                            <li>เปลี่ยน Camera เป็น "Allow"</li>
                            <li>Reload หน้านี้</li>
                        </ol>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg w-100 mt-2" onclick="location.reload()">
                        <i class="fas fa-sync me-2"></i>Reload หน้า
                    </button>
                `;
            } else if (err.name === 'NotFoundError') {
                errorMsg = `
                    <div class="alert alert-warning">
                        <i class="fas fa-camera-slash me-2"></i>
                        <strong>ไม่พบกล้อง</strong><br>
                        <small>อุปกรณ์นี้ไม่มีกล้อง หรือกล้องถูกปิดใช้งาน</small>
                    </div>
                `;
            } else if (err.name === 'NotReadableError') {
                errorMsg = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>กล้องถูกใช้งานโดยแอปอื่น</strong><br>
                        <small>กรุณาปิดแอปที่ใช้กล้องอยู่แล้วลองใหม่</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg w-100 mt-2" onclick="requestCameraPermission()">
                        <i class="fas fa-redo me-2"></i>ลองอีกครั้ง
                    </button>
                `;
            } else {
                errorMsg = `
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        <strong>เกิดข้อผิดพลาด</strong><br>
                        <small>${err.message || 'ไม่สามารถเปิดกล้องได้'}</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg w-100 mt-2" onclick="requestCameraPermission()">
                        <i class="fas fa-redo me-2"></i>ลองอีกครั้ง
                    </button>
                `;
            }
            
            document.getElementById('camera-status').innerHTML = errorMsg;
        }
    }

    // Initialize camera scanning
    async function initCamera() {
        try {
            html5QrCode = new Html5Qrcode("qr-reader");
            
            // Get camera list
            const devices = await Html5Qrcode.getCameras();
            console.log('Available cameras:', devices);
            cameras = devices;
            
            if (devices && devices.length > 0) {
                // Show camera switch button if multiple cameras
                if (devices.length > 1) {
                    document.getElementById('camera-controls').style.display = 'block';
                }
                
                // Start with back camera (usually index 0 on mobile)
                const cameraId = devices[currentCameraIndex].id;
                
                await html5QrCode.start(
                    cameraId,
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    },
                    (decodedText, decodedResult) => {
                        console.log('Decoded:', decodedText);
                        processQrcodeToken(decodedText);
                    },
                    (errorMessage) => {
                        // Ignore continuous scanning errors
                    }
                );
                
                showScanResult('📷 กล้องพร้อมใช้งาน', 'success');
            } else {
                showScanResult('❌ ไม่พบกล้อง', 'error');
            }
        } catch (err) {
            console.error('Camera init error:', err);
            let errorMsg = '❌ ไม่สามารถเปิดกล้องได้';
            
            if (err.message) {
                if (err.message.includes('Permission')) {
                    errorMsg = '❌ กรุณาอนุญาตการเข้าถึงกล้อง<br><small>ตั้งค่า → อนุญาตกล้อง → Reload หน้านี้</small>';
                } else if (err.message.includes('NotFound')) {
                    errorMsg = '❌ ไม่พบกล้อง';
                } else if (err.message.includes('NotReadable')) {
                    errorMsg = '❌ กล้องถูกใช้งานโดยแอปอื่น';
                }
            }
            
            showScanResult(errorMsg, 'error');
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
        if (!token) return;
        
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
                // แสดงปุ่มยืนยัน
                pendingRecord = data.data;
                showConfirmationDialog(data.data);
            } else if (data.type === 'duplicate') {
                // สแกนซ้ำ - แสดง warning แบบเดิม ไม่มีปุ่มยืนยัน
                showScanResult(data.message, 'warning');
            } else {
                showScanResult(data.message, 'error');
            }
        })
        .catch(err => {
            showScanResult('เกิดข้อผิดพลาด: ' + err.message, 'error');
        });
    }

    function showConfirmationDialog(employeeData) {
        const confirmHtml = `
            <div class="scan-result success">
                <h4 class="mb-3">✓ สแกนสำเร็จ</h4>
                <div class="mb-3">
                    <strong style="font-size: 1.2rem;">${employeeData.employee_name}</strong><br>
                    <small>รหัส: ${employeeData.employee_code}</small>
                </div>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success btn-lg" onclick="confirmScan()">
                        <i class="fas fa-check me-2"></i>ยืนยัน
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="cancelScan()">
                        <i class="fas fa-times me-2"></i>ยกเลิก
                    </button>
                </div>
            </div>
        `;
        document.getElementById('scanResult').innerHTML = confirmHtml;
    }

    function confirmScan() {
        if (pendingRecord) {
            showScanResult(`✓ บันทึก ${pendingRecord.employee_name} แล้ว`, 'success');
            updateAttendanceList();
            pendingRecord = null;
        }
    }

    function cancelScan() {
        pendingRecord = null;
        document.getElementById('scanResult').innerHTML = '';
    }

    function showScanResult(message, type) {
        const resultDiv = document.getElementById('scanResult');
        const typeClass = type === 'warning' ? 'error' : type;
        resultDiv.innerHTML = `<div class="scan-result ${typeClass}">${message}</div>`;
        setTimeout(() => {
            resultDiv.innerHTML = '';
        }, 3000);
        
        // Play sound for success
        if (type === 'success') {
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBSmBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBSmBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBSmBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBSmBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBSmBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBSmBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBSmBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBA==');
                audio.play().catch(() => {});
            } catch(e) {}
        }
    }

    function updateAttendanceList() {
        fetch(`{{ route('driver.trip.recent-records', $trip) }}`)
            .then(res => res.json())
            .then(data => {
                const count = data.passenger_count || 0;
                const capacity = {{ $trip->vehicle->capacity }};
                
                // Update counter
                document.getElementById('passengerCount').innerText = count;
                
                // Update progress bar
                const percentage = capacity > 0 ? Math.round((count / capacity) * 100) : 0;
                const progressBar = document.getElementById('capacityProgress');
                progressBar.style.width = percentage + '%';
                progressBar.innerText = percentage + '%';
                
                // Change color based on capacity
                progressBar.className = 'progress-bar ' + 
                    (percentage >= 90 ? 'bg-danger' : (percentage >= 70 ? 'bg-warning' : 'bg-success'));
                
                // Update list - แสดงทั้งหมด
                if (data.records && data.records.length > 0) {
                    const listHtml = data.records.map(r =>
                        `<div class="attendance-item">
                            <div class="fw-bold fs-5">${r.employee_code}</div>
                            <div>${r.employee_name}</div>
                            <small class="text-muted"><i class="fas fa-clock me-1"></i>${r.scanned_at}</small>
                        </div>`
                    ).join('');
                    document.getElementById('attendanceList').innerHTML = listHtml;
                } else {
                    document.getElementById('attendanceList').innerHTML = 
                        `<div class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>ยังไม่มีการสแกน</p>
                        </div>`;
                }
            })
            .catch(err => {
                console.error('Update list error:', err);
            });
    }

    async function switchCamera() {
        try {
            if (cameras.length <= 1) return;
            
            // Stop current camera
            if (html5QrCode) {
                await html5QrCode.stop();
            }
            
            // Switch to next camera
            currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
            
            showScanResult('🔄 กำลังเปลี่ยนกล้อง...', 'success');
            
            // Start with new camera
            const cameraId = cameras[currentCameraIndex].id;
            await html5QrCode.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                (decodedText, decodedResult) => {
                    processQrcodeToken(decodedText);
                },
                (errorMessage) => {
                    // Ignore
                }
            );
            
                    showScanResult('✓ เปลี่ยนกล้องแล้ว', 'success');
        } catch (err) {
            console.error('Switch camera error:', err);
            showScanResult('❌ ไม่สามารถเปลี่ยนกล้องได้', 'error');
        }
    }

    function cancelLastRecord() {
        if (!confirm('ต้องการยกเลิกรายการสแกนล่าสุด?')) {
            return;
        }

        fetch(`{{ route('driver.trip.cancel-record', $trip) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                reason: 'ยกเลิกจากหน้าจอของคนขับ'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showScanResult('✓ ยกเลิกเรียบร้อย', 'success');
                updateAttendanceList();
            } else {
                showScanResult(data.message || 'ไม่สามารถยกเลิกได้', 'error');
            }
        })
        .catch(err => {
            showScanResult('เกิดข้อผิดพลาด', 'error');
        });
    }

    function completeTrip() {
        if (confirm('ปิดรอบนี้?')) {
            document.getElementById('completeTripForm').submit();
        }
    }

    // Start camera on load - but don't force it
    window.addEventListener('load', () => {
        // Check if permission was previously granted
        navigator.permissions.query({ name: 'camera' }).then(permission => {
            if (permission.state === 'granted') {
                cameraPermissionGranted = true;
                document.getElementById('camera-status').style.display = 'none';
                setTimeout(initCamera, 500);
            }
            // If denied or prompt, show the manual button
        }).catch(() => {
            // Permissions API not supported, just show the button
        });
    });
    
    // Stop camera when leaving page
    window.addEventListener('beforeunload', () => {
        if (html5QrCode) {
            html5QrCode.stop().catch(() => {});
        }
    });
</script>
@endsection
