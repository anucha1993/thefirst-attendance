@extends('layouts.app')

@section('title', 'QR Code ของฉัน')

@section('css')
<style>
    .qrcode-display {
        text-align: center;
        padding: 40px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .qrcode-display img, .qrcode-display svg {
        max-width: 350px;
        width: 100%;
        border: 4px solid #3498db;
        border-radius: 8px;
        padding: 15px;
        background: white;
    }

    .employee-details {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
        border-left: 4px solid #3498db;
    }
</style>
@endsection

@section('content')
<div class="page-title">
    <i class="bi bi-qr-code"></i>
    <h2>QR Code ของฉัน</h2>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-badge"></i> QR Code สำหรับเช็คชื่อ
            </div>
            <div class="card-body">
                <div class="qrcode-display">
                    @if($qrCodeUrl)
                        <img src="{{ $qrCodeUrl }}" alt="QR Code">
                    @else
                        <p class="text-muted">ไม่พบ QR Code</p>
                    @endif

                    <div class="employee-details">
                        <h5>{{ auth()->user()->name }}</h5>
                        <p class="mb-0"><strong>รหัส:</strong> {{ $employee->employee_code }}</p>
                    </div>

                    <div class="mt-4">
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="bi bi-printer"></i> พิมพ์
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> วิธีใช้
            </div>
            <div class="card-body">
                <h6>ตัวเลือก 1: จากมือถือ</h6>
                <p>เปิดหน้านี้บนมือถือ ให้คนขับสแกน QR Code จากหน้าจอของคุณ</p>

                <h6 class="mt-3">ตัวเลือก 2: บัตรพิมพ์</h6>
                <p>กดปุ่ม "พิมพ์" เพื่อพิมพ์บัตร QR Code และติดบนบัตรพนักงาน</p>

                <hr>

                <p class="text-muted mb-0"><i class="bi bi-shield-check"></i> QR Code นี้ใช้ได้ตลอดจนกว่าจะเปลี่ยน</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-person-vcard"></i> ข้อมูลของฉัน
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>ชื่อ:</strong> {{ $employee->getFullName() }}</p>
                <p class="mb-2"><strong>รหัสพนักงาน:</strong> {{ $employee->employee_code }}</p>
                <p class="mb-2"><strong>แผนก:</strong> {{ $employee->department }}</p>
                <p class="mb-0"><strong>ตำแหน่ง:</strong> {{ $employee->position }}</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<style>
    @media print {
        body {
            background: white;
        }
        .sidebar, .page-title, .col-md-4, button:not([onclick="window.print()"]), a {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .card {
            box-shadow: none !important;
            page-break-inside: avoid;
        }
    }
</style>
@endsection
