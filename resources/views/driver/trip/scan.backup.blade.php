@extends('layouts.guest')

@section('title', 'สแกน QR Code')

@section('content')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<style>
    body {
        background: #f5f7fa;
        margin: 0;
        padding: 0;
    }

    .scan-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 15px;
        min-height: 100vh;
    }

    /* Minimal Header */
    .minimal-header {
        background: white;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-info {
        flex: 1;
    }

    .header-title {
        font-size: 0.95rem;
        color: #64748b;
        margin: 0;
    }

    .header-detail {
        font-size: 0.85rem;
        color: #94a3b8;
        margin-top: 0.2rem;
    }

    .btn-back {
        background: #f1f5f9;
        border: none;
        color: #475569;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    /* Compact Counter */
    .compact-counter {
        background: white;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .counter-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #10b981;
        line-height: 1;
    }

    .counter-label {
        font-size: 0.9rem;
        color: #64748b;
        margin-left: 1rem;
    }

    /* MAIN: QR Scanner Section - Most Prominent */
    .scanner-main {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 1.5rem;
        border-radius: 15px;
        margin-bottom: 1rem;
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
    }

    .scanner-title {
        color: white;
        font-size: 1.3rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 1rem;
    }

    .scanner-title i {
        font-size: 1.5rem;
        margin-right: 0.5rem;
    }

    #qr-reader {
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        border: 4px solid rgba(255,255,255,0.3);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    #qr-reader video {
        width: 100% !important;
        height: auto !important;
    }

    #qr-reader__dashboard_section_csr {
        display: none !important;
    }

    .camera-btn {
        background: white;
        color: #667eea;
        border: none;
        padding: 1rem;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 700;
        width: 100%;
        margin-top: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .camera-btn i {
        margin-right: 0.5rem;
        font-size: 1.2rem;
    }

    .input-token {
        background: rgba(255,255,255,0.95);
        border: 2px solid rgba(255,255,255,0.5);
        color: #1e293b;
        padding: 1rem;
        border-radius: 10px;
        font-size: 1rem;
        width: 100%;
        margin-top: 1rem;
    }

    .input-token::placeholder {
        color: #94a3b8;
    }

    .input-token:focus {
        outline: none;
        border-color: white;
        background: white;
    }

    /* Minimal Passenger List */
    .passenger-list-minimal {
        background: white;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .list-header {
        font-size: 0.85rem;
        color: #94a3b8;
        margin-bottom: 0.8rem;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .passenger-item-minimal {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.7rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .passenger-item-minimal:last-child {
        border-bottom: none;
    }

    .passenger-name-minimal {
        font-size: 0.95rem;
        color: #475569;
        font-weight: 500;
    }

    .btn-remove-minimal {
        background: #fee2e2;
        color: #dc2626;
        border: none;
        padding: 0.3rem 0.7rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* Complete Button - Secondary */
    .btn-complete-minimal {
        background: white;
        border: 2px solid #e5e7eb;
        color: #6b7280;
        padding: 1rem;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        width: 100%;
        margin-top: 1rem;
    }

    .btn-complete-minimal:active {
        background: #f9fafb;
    }

    /* Empty State */
    .empty-minimal {
        text-align: center;
        padding: 1.5rem;
        color: #cbd5e1;
        font-size: 0.9rem;
    }

    /* Modal */
    .modal-content {
        border-radius: 15px;
    }

    .modal-header {
        border-radius: 15px 15px 0 0;
    }
</style>

<div class="scan-container">
    <!-- Minimal Header -->
    <div class="minimal-header">
        <div class="header-info">
            <div class="header-title">{{ $trip->route->name }}</div>
            <div class="header-detail">{{ $trip->vehicle->license_plate }}</div>
        </div>
        <a href="{{ route('driver.work-center') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="border-radius: 10px; margin-bottom: 1rem; font-size: 0.9rem;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="border-radius: 10px; margin-bottom: 1rem; font-size: 0.9rem;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Compact Counter -->
    <div class="compact-counter">
        <div>
            <span class="counter-number" id="passengerCount">{{ $tripSummary['passenger_count'] ?? 0 }}</span>
            <span class="counter-label">/ {{ $trip->vehicle->capacity }} คน</span>
        </div>
        <div class="counter-label">ผู้โดยสาร</div>
    </div>

    <!-- MAIN: QR Scanner (Most Prominent) -->
    <div class="scanner-main">
        <div class="scanner-title">
            <i class="fas fa-qrcode"></i>สแกน QR Code
        </div>

        <div id="qr-reader"></div>

        <div id="camera-status" style="margin-top: 1rem;">
            <button type="button" class="camera-btn" onclick="requestCameraPermission()">
                <i class="fas fa-camera"></i>เปิดกล้อง
            </button>
        </div>

        <input type="text" id="qrcodeInput" class="input-token" 
               placeholder="หรือพิมพ์โทเค็น แล้วกด Enter">
    </div>

    <!-- Minimal Passenger List -->
    @if($recentRecords && count($recentRecords) > 0)
        <div class="passenger-list-minimal">
            <div class="list-header">รายชื่อผู้โดยสาร</div>
            <div id="passengerListMinimal">
                @foreach($recentRecords as $record)
                    <div class="passenger-item-minimal">
                        <div class="passenger-name-minimal">{{ $record->employee->name }}</div>
                        <button class="btn-remove-minimal" onclick="cancelSpecificRecord({{ $record->id }}, '{{ $record->employee->name }}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Complete Button (Minimal) -->
    <button type="button" class="btn-complete-minimal" onclick="completeTrip()">
        <i class="fas fa-check-circle me-2"></i>ปิดรอบนี้
    </button>

    <form id="completeTripForm" action="{{ route('driver.trip.complete', $trip) }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmScanModal" tabindex="-1" aria-labelledby="confirmScanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="confirmScanModalLabel">
                    <i class="fas fa-check-circle me-2"></i>สแกนสำเร็จ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-4x text-success"></i>
                </div>
                <h4 class="mb-2" id="modalEmployeeName"></h4>
                <p class="text-muted mb-0">รหัส: <span id="modalEmployeeCode"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal" onclick="cancelScan()" style="font-size: 1.2rem; padding: 15px 30px;">
                    <i class="fas fa-times me-2"></i>ยกเลิก
                </button>
                <button type="button" class="btn btn-success btn-lg" onclick="confirmScan()" style="font-size: 1.2rem; padding: 15px 30px;">
                    <i class="fas fa-check me-2"></i>ยืนยัน
                </button>
            </div>
        </div>
    </div>
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
    // Force reload - v1.1
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
        // เติมข้อมูลใน modal
        document.getElementById('modalEmployeeName').textContent = employeeData.employee_name;
        document.getElementById('modalEmployeeCode').textContent = employeeData.employee_code;
        
        // เปิด modal
        const modal = new bootstrap.Modal(document.getElementById('confirmScanModal'));
        modal.show();
        
        // เล่นเสียง
        try {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBSmBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBSmBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBSmBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBSmBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBSmBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBSmBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBSmBzvLZiTYIGWS56+OhUA0PUajn77tuGwU+ldv0xXksBA==');
            audio.play().catch(() => {});
        } catch(e) {}
    }

    function confirmScan() {
        if (!pendingRecord) return;

        // เรียก API ยืนยันการสแกน
        fetch(`{{ route('driver.trip.confirm-scan', $trip) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                employee_id: pendingRecord.employee_id,
                latitude: null,
                longitude: null
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // ปิด modal และลบ backdrop (แก้ปัญหาจอดำบนมือถือ)
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmScanModal'));
                if (modal) {
                    modal.hide();
                }
                
                // ลบ backdrop และ class ที่เหลือค้าง
                setTimeout(() => {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }, 200);
                
                // แสดงข้อความสำเร็จพร้อมปุ่มยกเลิก
                const successHtml = `
                    <div class="scan-result success">
                        <h4 class="mb-3">✓ บันทึก ${pendingRecord.employee_name} แล้ว</h4>
                        <button type="button" class="btn btn-danger btn-sm" onclick="cancelLastRecord()">
                            <i class="fas fa-undo me-2"></i>ยกเลิกรายการนี้
                        </button>
                    </div>
                `;
                document.getElementById('scanResult').innerHTML = successHtml;
                setTimeout(() => {
                    document.getElementById('scanResult').innerHTML = '';
                }, 5000);
                
                updateAttendanceList();
            } else {
                // ปิด modal และลบ backdrop ก่อนแสดง error
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmScanModal'));
                if (modal) {
                    modal.hide();
                }
                setTimeout(() => {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }, 200);
                
                // แสดง error message
                const errorType = data.type === 'duplicate' ? 'warning' : 'error';
                showScanResult(data.message || 'เกิดข้อผิดพลาด', errorType);
            }
            pendingRecord = null;
        })
        .catch(err => {
            // ปิด modal และลบ backdrop ก่อนแสดง error
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmScanModal'));
            if (modal) {
                modal.hide();
            }
            setTimeout(() => {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 200);
            
            showScanResult('เกิดข้อผิดพลาด: ' + err.message, 'error');
            pendingRecord = null;
        });
    }

    function cancelScan() {
        // ปิด modal และลบ backdrop (แก้ปัญหาจอดำบนมือถือ)
        const modal = bootstrap.Modal.getInstance(document.getElementById('confirmScanModal'));
        if (modal) {
            modal.hide();
        }
        
        // ลบ backdrop และ class ที่เหลือค้าง
        setTimeout(() => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }, 200);
        
        if (pendingRecord) {
            showScanResult('ยกเลิกการสแกนของ ' + pendingRecord.employee_name, 'warning');
        }
        pendingRecord = null;
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
                console.log('API Response:', data); // Debug
                console.log('Records:', data.records); // Debug
                
                const count = data.passenger_count !== undefined ? data.passenger_count : 0;
                const capacity = {{ $trip->vehicle->capacity }};
                
                console.log('Passenger count:', count); // Debug
                
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
                        `<div class="attendance-item" style="display: flex; align-items: center; justify-content: space-between; gap: 10px; padding-right: 5px;">
                            <div style="flex: 1; min-width: 0;">
                                <div class="fw-bold fs-5">${r.employee_code}</div>
                                <div style="overflow: hidden; text-overflow: ellipsis;">${r.employee_name}</div>
                                <small class="text-muted"><i class="fas fa-clock me-1"></i>${r.scanned_at}</small>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm" onclick="cancelSpecificRecord(${r.id}, '${r.employee_name.replace(/'/g, "\\'")}')"
                                style="white-space: nowrap; flex-shrink: 0; min-width: 40px; height: 40px;">
                                <i class="fas fa-times"></i> ยกเลิก
                            </button>
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
        // ลบ confirm dialog เพราะเพิ่งยืนยันเสร็จ
        document.getElementById('scanResult').innerHTML = '<div class="scan-result">กำลังยกเลิก...</div>';

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
                showScanResult('✓ ยกเลิกรายการล่าสุดเรียบร้อย', 'warning');
                updateAttendanceList();
            } else {
                showScanResult(data.message || 'ไม่สามารถยกเลิกได้', 'error');
            }
        })
        .catch(err => {
            showScanResult('เกิดข้อผิดพลาด', 'error');
        });
    }

    function cancelSpecificRecord(recordId, employeeName) {
        if (!confirm(`ต้องการยกเลิกรายการของ ${employeeName}?`)) {
            return;
        }

        fetch(`{{ url('driver/trip/' . $trip->id . '/cancel-specific-record') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                record_id: recordId,
                reason: 'ยกเลิกจากรายชื่อ'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showScanResult(`✓ ยกเลิก ${employeeName} เรียบร้อย`, 'warning');
                updateAttendanceList();
            } else {
                showScanResult(data.message || 'ไม่สามารถยกเลิกได้', 'error');
            }
        })
        .catch(err => {
            showScanResult('เกิดข้อผิดพลาด: ' + err.message, 'error');
        });
    }

    function completeTrip() {
        if (confirm('ปิดรอบนี้?')) {
            document.getElementById('completeTripForm').submit();
        }
    }

    // Start camera on load - but don't force it
    window.addEventListener('load', () => {
        // ไม่เรียก updateAttendanceList() ทันที ให้ใช้ข้อมูลจาก Blade ก่อน
        // จะ update ก็ต่อเมื่อมีการสแกนใหม่
        
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
