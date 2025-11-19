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

    .passenger-info {
        flex: 1;
    }

    .passenger-name-minimal {
        font-size: 0.95rem;
        color: #475569;
        font-weight: 600;
        margin-bottom: 0.2rem;
    }

    .passenger-time {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .passenger-time i {
        margin-right: 0.3rem;
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
            <div class="header-detail">
                {{ $trip->vehicle->license_plate }}
                @if($trip->status === 'completed')
                    <span style="margin-left: 0.5rem; padding: 0.2rem 0.6rem; background: #d1ecf1; color: #0c5460; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                        <i class="fas fa-check-circle"></i> เสร็จสิ้น
                    </span>
                @endif
            </div>
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
    @if($trip->status === 'active')
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

            <div id="camera-controls" style="margin-top: 1rem; display: none;">
                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" class="camera-btn" onclick="stopCamera()" style="background: #ef4444; color: white;">
                        <i class="fas fa-times-circle"></i>ปิดกล้อง
                    </button>
                    <button type="button" class="camera-btn" onclick="location.reload()" style="background: #f59e0b; color: white;">
                        <i class="fas fa-sync-alt"></i>รีเฟรช
                    </button>
                </div>
            </div>

            <input type="text" id="qrcodeInput" class="input-token" 
                   placeholder="หรือพิมพ์โทเค็น แล้วกด Enter">
        </div>
    @else
        <div class="alert alert-info" style="border-radius: 10px; margin-bottom: 1rem;">
            <i class="fas fa-info-circle me-2"></i>รอบนี้เสร็จสิ้นแล้ว ไม่สามารถสแกนเพิ่มได้
        </div>
    @endif

    <!-- Minimal Passenger List -->
    <div id="passengerListSection">
    @if($recentRecords && count($recentRecords) > 0)
        <div class="passenger-list-minimal">
            <div class="list-header">รายชื่อผู้โดยสาร</div>
            <div id="passengerListMinimal">
                @foreach($recentRecords as $record)
                    <div class="passenger-item-minimal">
                        <div class="passenger-info">
                            <div class="passenger-name-minimal">{{ $record['employee_name'] }}</div>
                            <div class="passenger-time">
                                <i class="fas fa-clock"></i>{{ $record['scanned_at'] }}
                            </div>
                        </div>
                        @if($trip->status === 'active')
                            <button class="btn-remove-minimal" onclick="cancelSpecificRecord({{ $record['id'] }}, '{{ $record['employee_name'] }}')">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    </div>

    <!-- Complete Button (Minimal) - Only for active trips -->
    @if($trip->status === 'active')
        <button type="button" class="btn-complete-minimal" onclick="completeTrip()">
            <i class="fas fa-check-circle me-2"></i>ปิดรอบนี้
        </button>
    @endif

    <form id="completeTripForm" action="{{ route('driver.trip.complete', $trip) }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmScanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>ยืนยัน?
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-user-circle fa-4x text-success mb-3"></i>
                <h4 class="mb-2" id="modalEmployeeName"></h4>
                <p class="text-muted">รหัส: <span id="modalEmployeeCode"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-lg" onclick="cancelScan()">
                    ยกเลิก
                </button>
                <button type="button" class="btn btn-success btn-lg" onclick="confirmScan()">
                    <i class="fas fa-check me-2"></i>ยืนยัน
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const tripId = {{ $trip->id }};
    const tripStatus = '{{ $trip->status }}';
    let html5QrCode;
    let cameraPermissionGranted = false;
    let pendingEmployeeData = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Only initialize camera for active trips
        if (tripStatus !== 'active') {
            return;
        }

        // Check camera permission
        navigator.permissions.query({ name: 'camera' }).then(permission => {
            if (permission.state === 'granted') {
                cameraPermissionGranted = true;
                const cameraStatus = document.getElementById('camera-status');
                if (cameraStatus) {
                    cameraStatus.style.display = 'none';
                }
                initCamera();
            }
        }).catch(() => {});
        
        // Manual input
        const qrcodeInput = document.getElementById('qrcodeInput');
        if (qrcodeInput) {
            qrcodeInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const token = this.value.trim();
                    if (token) {
                        processQrCode(token);
                        this.value = '';
                    }
                }
            });
        }
    });

    async function requestCameraPermission() {
        if (tripStatus !== 'active') {
            alert('รอบนี้เสร็จสิ้นแล้ว ไม่สามารถสแกนเพิ่มได้');
            return;
        }
        
        try {
            document.getElementById('camera-status').innerHTML = `
                <div class="alert alert-info" style="background: rgba(255,255,255,0.9); color: #667eea; border: none;">
                    <i class="fas fa-spinner fa-spin me-2"></i>กำลังขออนุญาต...
                </div>
            `;
            
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'environment' } 
            });
            
            stream.getTracks().forEach(track => track.stop());
            cameraPermissionGranted = true;
            document.getElementById('camera-status').style.display = 'none';
            await initCamera();
            
        } catch (err) {
            console.error('Permission error:', err);
            document.getElementById('camera-status').innerHTML = `
                <div class="alert alert-warning" style="background: rgba(255,255,255,0.9); color: #dc2626; border: none; font-size: 0.9rem;">
                    <i class="fas fa-exclamation-triangle me-2"></i>ไม่สามารถเข้าถึงกล้อง
                </div>
                <button type="button" class="camera-btn" onclick="requestCameraPermission()">
                    <i class="fas fa-redo me-2"></i>ลองอีกครั้ง
                </button>
            `;
        }
    }

    async function initCamera() {
        try {
            html5QrCode = new Html5Qrcode("qr-reader");
            
            await html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                (decodedText) => {
                    processQrCode(decodedText);
                },
                (errorMessage) => {}
            );
            
            document.getElementById('camera-status').style.display = 'none';
            document.getElementById('camera-controls').style.display = 'block';
        } catch (err) {
            console.error("Camera start error:", err);
        }
    }

    function stopCamera() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                // Reload page after stopping camera
                location.reload();
            }).catch(err => {
                console.error('Stop camera error:', err);
                // Reload anyway
                location.reload();
            });
        } else {
            location.reload();
        }
    }

    function processQrCode(token) {
        if (tripStatus !== 'active') {
            Swal.fire({
                icon: 'warning',
                title: 'ไม่สามารถสแกนได้',
                text: 'รอบนี้เสร็จสิ้นแล้ว',
                confirmButtonText: 'ตกลง'
            });
            return;
        }
        
        fetch(`/driver/trip/${tripId}/scan`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ qrcode_token: token })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                pendingEmployeeData = data.data;
                showConfirmationDialog(data.data);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถสแกนได้',
                    text: data.message,
                    confirmButtonText: 'ตกลง'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'กรุณาลองใหม่',
                confirmButtonText: 'ตกลง'
            });
        });
    }

    function showConfirmationDialog(employee) {
        // Pause camera during confirmation
        if (html5QrCode) {
            try {
                html5QrCode.pause(true);
            } catch(e) {
                console.log('Camera pause not available');
            }
        }
        
        document.getElementById('modalEmployeeName').textContent = employee.employee_name;
        document.getElementById('modalEmployeeCode').textContent = employee.employee_code;
        const modal = new bootstrap.Modal(document.getElementById('confirmScanModal'));
        modal.show();
    }

    function cancelScan() {
        pendingEmployeeData = null;
        
        // Close modal and clean up backdrop
        const modal = bootstrap.Modal.getInstance(document.getElementById('confirmScanModal'));
        if (modal) {
            modal.hide();
        }
        
        setTimeout(() => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            
            // Resume camera after cancel
            if (html5QrCode) {
                try {
                    html5QrCode.resume();
                } catch(e) {
                    console.log('Camera resume not available');
                }
            }
        }, 200);
    }

    function confirmScan() {
        if (!pendingEmployeeData) return;

        fetch(`/driver/trip/${tripId}/confirm-scan`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                employee_id: pendingEmployeeData.employee_id 
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Confirm scan response:', data);
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmScanModal'));
            modal.hide();
            
            if (data.success) {
                // Extract data from nested data object
                const passengerCount = data.data?.passenger_count || 0;
                const records = data.data?.records || [];
                
                console.log('Success! Updating UI with:', {
                    count: passengerCount,
                    records: records
                });
                
                // Update UI immediately BEFORE showing SweetAlert
                updatePassengerCount(passengerCount);
                updatePassengerList(records);
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกสำเร็จ',
                    text: `${pendingEmployeeData.employee_name}`,
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                console.error('Scan failed:', data.message);
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถบันทึกได้',
                    text: data.message,
                    confirmButtonText: 'ตกลง'
                });
            }

            pendingEmployeeData = null;
            
            // Clean up modal backdrop and resume camera after UI update
            setTimeout(() => {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                
                // Resume camera after everything is done
                if (html5QrCode) {
                    try {
                        html5QrCode.resume();
                    } catch(e) {
                        console.log('Camera resume not available');
                    }
                }
            }, 300);
        })
        .catch(error => {
            console.error('Fetch error:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'กรุณาลองใหม่',
                confirmButtonText: 'ตกลง'
            });
        });
    }

    function updatePassengerCount(count) {
        console.log('Updating count to:', count);
        const countElement = document.getElementById('passengerCount');
        if (countElement) {
            countElement.textContent = count;
        }
    }

    function updatePassengerList(records) {
        console.log('Updating passenger list:', records);
        const listSection = document.getElementById('passengerListSection');
        if (!listSection) {
            console.error('passengerListSection not found!');
            return;
        }
        
        if (!records || records.length === 0) {
            console.log('No records, clearing list');
            listSection.innerHTML = '';
            return;
        }
        
        const listHtml = records.map(r => `
            <div class="passenger-item-minimal">
                <div class="passenger-info">
                    <div class="passenger-name-minimal">${r.employee_name}</div>
                    <div class="passenger-time">
                        <i class="fas fa-clock"></i>${r.scanned_at}
                    </div>
                </div>
                ${tripStatus === 'active' ? `
                    <button class="btn-remove-minimal" onclick="cancelSpecificRecord(${r.id}, '${r.employee_name}')">
                        <i class="fas fa-times"></i>
                    </button>
                ` : ''}
            </div>
        `).join('');

        listSection.innerHTML = `
            <div class="passenger-list-minimal">
                <div class="list-header">รายชื่อผู้โดยสาร</div>
                <div id="passengerListMinimal">
                    ${listHtml}
                </div>
            </div>
        `;
        
        console.log('Passenger list updated successfully');
    }

    function cancelSpecificRecord(recordId, employeeName) {
        if (tripStatus !== 'active') {
            Swal.fire({
                icon: 'warning',
                title: 'ไม่สามารถยกเลิกได้',
                text: 'รอบนี้เสร็จสิ้นแล้ว',
                confirmButtonText: 'ตกลง'
            });
            return;
        }
        
        Swal.fire({
            title: 'ยืนยันการยกเลิก?',
            text: `ยกเลิก ${employeeName}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ใช่',
            cancelButtonText: 'ไม่'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/driver/trip/${tripId}/cancel-specific-record`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ record_id: recordId })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Cancel record response:', data);
                    
                    if (data.success) {
                        // Extract data from nested data object
                        const passengerCount = data.data?.passenger_count || 0;
                        const records = data.data?.records || [];
                        
                        console.log('Deleting success! Updating UI with:', {
                            count: passengerCount,
                            records: records
                        });
                        
                        // Force immediate UI update
                        updatePassengerCount(passengerCount);
                        updatePassengerList(records);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'ยกเลิกสำเร็จ',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.message || 'กรุณาลองใหม่',
                            confirmButtonText: 'ตกลง'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'กรุณาลองใหม่',
                        confirmButtonText: 'ตกลง'
                    });
                });
            }
        });
    }

    function completeTrip() {
        Swal.fire({
            title: 'ปิดรอบนี้?',
            text: `ผู้โดยสาร ${document.getElementById('passengerCount').textContent} คน`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('completeTripForm').submit();
            }
        });
    }

    // Stop camera when leaving
    window.addEventListener('beforeunload', () => {
        if (html5QrCode) {
            html5QrCode.stop().catch(() => {});
        }
    });
</script>
@endsection
