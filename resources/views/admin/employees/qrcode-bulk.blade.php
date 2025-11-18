<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Codes - พิมพ์รายการ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
            padding: 20px;
        }

        .no-print {
            margin-bottom: 20px;
            text-align: center;
        }

        .qr-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin: 0 auto;
            max-width: 1400px;
        }

        .qr-card {
            background: white;
            border: 2px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .qr-card img {
            width: 200px;
            height: 200px;
            margin: 10px auto;
            display: block;
            border: 3px solid #667eea;
            border-radius: 8px;
            padding: 8px;
            background: white;
        }

        .employee-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin: 10px 0 5px;
        }

        .employee-code {
            font-size: 14px;
            color: #666;
            font-family: 'Courier New', monospace;
            background: #f0f0f0;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        .employee-dept {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }

        .qr-token {
            font-size: 10px;
            color: #aaa;
            margin-top: 8px;
            font-family: monospace;
            word-break: break-all;
        }

        /* Print styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .qr-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
                max-width: 100%;
            }

            .qr-card {
                border: 2px solid #333;
                box-shadow: none;
                margin: 0;
                padding: 15px;
            }

            .qr-card img {
                width: 180px;
                height: 180px;
            }

            @page {
                size: A4;
                margin: 10mm;
            }
        }

        /* For smaller screens */
        @media (max-width: 768px) {
            .qr-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }

            .qr-card img {
                width: 160px;
                height: 160px;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div class="container">
            <div class="alert alert-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>จำนวน {{ $employeeData->count() }} คน</strong> - พร้อมพิมพ์
                    </div>
                    <div>
                        <button onclick="window.print()" class="btn btn-primary me-2">
                            <i class="fas fa-print me-1"></i>พิมพ์
                        </button>
                        <button onclick="window.close()" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>ปิด
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="qr-grid">
        @foreach($employeeData as $data)
            <div class="qr-card">
                @if($data['qrCodeUrl'])
                    <img src="{{ $data['qrCodeUrl'] }}" alt="QR Code - {{ $data['employee']->employee_code }}">
                @else
                    <div style="width: 200px; height: 200px; background: #f0f0f0; margin: 10px auto; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                        <span class="text-muted">ไม่พบ QR Code</span>
                    </div>
                @endif

                <div class="employee-name">
                    {{ $data['employee']->first_name }} {{ $data['employee']->last_name }}
                </div>

                <div class="employee-code">
                    {{ $data['employee']->employee_code }}
                </div>

                <div class="employee-dept">
                    {{ $data['employee']->department ?? '-' }} | {{ $data['employee']->position ?? '-' }}
                </div>

                <div class="qr-token">
                    {{ $data['employee']->qrcode_token }}
                </div>
            </div>
        @endforeach
    </div>

    <script>
        // Auto print on load (optional - uncomment if needed)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // };
    </script>
</body>
</html>
