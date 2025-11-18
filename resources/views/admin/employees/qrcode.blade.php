@extends('layouts.app')

@section('title', 'QR Code - ' . $employee->getFullName())

@section('css')
<style>
    .qrcode-container {
        text-align: center;
        padding: 40px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .qrcode-container img, .qrcode-container svg {
        max-width: 300px;
        border: 3px solid #3498db;
        border-radius: 8px;
        padding: 10px;
        background: white;
    }

    .employee-info {
        margin-top: 20px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .print-btn {
        margin-top: 20px;
    }
</style>
@endsection

@section('content')
<div class="page-title">
    <i class="bi bi-qr-code"></i>
    <div>
        <h2>QR Code</h2>
        <small class="text-muted">{{ $employee->getFullName() }}</small>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="qrcode-container">
            @if($qrCodeUrl)
                <img src="{{ $qrCodeUrl }}" alt="QR Code">
            @else
                <p class="text-muted">ไม่พบ QR Code</p>
            @endif

            <div class="employee-info">
                <h5>{{ $employee->getFullName() }}</h5>
                <p class="mb-2"><strong>รหัส:</strong> {{ $employee->employee_code }}</p>
                <p class="mb-2"><strong>Token:</strong> <code>{{ $employee->qrcode_token }}</code></p>
                <p class="mb-0"><strong>แผนก:</strong> {{ $employee->department }}</p>
            </div>

            <div class="print-btn">
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> พิมพ์
                </button>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> กลับ
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> วิธีใช้
            </div>
            <div class="card-body">
                <h6>วิธีการ 1: พิมพ์บัตร</h6>
                <ol>
                    <li>กดปุ่ม "พิมพ์"</li>
                    <li>พิมพ์ QR Code</li>
                    <li>ติดบนบัตรพนักงาน</li>
                </ol>

                <h6 class="mt-4">วิธีการ 2: แสดงจากมือถือ</h6>
                <ol>
                    <li>ให้พนักงานเปิดหน้านี้</li>
                    <li>ให้คนขับสแกนจากหน้าจอมือถือ</li>
                </ol>

                <h6 class="mt-4">โทเค็น (สำหรับสแกนด้วยตนเอง):</h6>
                <p><code class="bg-light p-2">{{ $employee->qrcode_token }}</code></p>

                <h6 class="mt-4">ข้อมูลพนักงาน:</h6>
                <ul>
                    <li><strong>รหัส:</strong> {{ $employee->employee_code }}</li>
                    <li><strong>ชื่อ:</strong> {{ $employee->getFullName() }}</li>
                    <li><strong>แผนก:</strong> {{ $employee->department }}</li>
                    <li><strong>ตำแหน่ง:</strong> {{ $employee->position }}</li>
                    <li><strong>สถานะ:</strong> <span class="badge bg-{{ $employee->is_active ? 'success' : 'danger' }}">{{ $employee->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<style>
    @media print {
        body, .sidebar, .navbar {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .page-title, .card, button, a {
            display: none !important;
        }
        .qrcode-container {
            display: block !important;
            margin: 0 !important;
            box-shadow: none !important;
            width: 100%;
            height: 100%;
        }
    }
</style>
@endsection
