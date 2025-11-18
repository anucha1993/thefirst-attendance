<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>รายงานรายวัน - {{ $date }}</title>
    <style>
        body {
            font-family: 'Garuda', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        .header p {
            margin: 3px 0;
            color: #666;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        th {
            background-color: #f4f4f4;
            padding: 6px 4px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 10px;
        }
        td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>รายงานรายวัน</h1>
        <p>วันที่: {{ \Carbon\Carbon::parse($date)->locale('th')->translatedFormat('j F Y') }}</p>
        <p>พิมพ์เมื่อ: {{ now()->locale('th')->translatedFormat('j F Y H:i น.') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 6%;">รหัสรอบ</th>
                <th style="width: 8%;">วันที่</th>
                <th style="width: 6%;">เวลาเริ่ม</th>
                <th style="width: 6%;">เวลาสิ้นสุด</th>
                <th style="width: 10%;">สายรถ</th>
                <th style="width: 8%;">จุดรับ</th>
                <th style="width: 8%;">จุดส่ง</th>
                <th style="width: 8%;">ป้ายทะเบียน</th>
                <th style="width: 8%;">คนขับ</th>
                <th class="text-center" style="width: 5%;">จำนวน</th>
                <th style="width: 15%;">รายชื่อผู้โดยสาร</th>
                <th style="width: 7%;">รหัสพนักงาน</th>
                <th class="text-right" style="width: 5%;">ค่าโดยสาร</th>
            </tr>
        </thead>
        <tbody>
            @php
                $trips = \App\Models\Trip::whereDate('started_at', $date)
                    ->with(['vehicle', 'route.pickupLocation', 'route.dropoffLocation', 'driver', 'attendanceRecords.employee'])
                    ->orderBy('started_at')
                    ->get();
                $totalPassengers = 0;
                $totalFare = 0;
            @endphp
            
            @forelse($trips as $trip)
                @php
                    $passengers = $trip->attendanceRecords;
                    $passengerNames = $passengers->map(fn($r) => $r->employee->full_name)->join(', ');
                    $employeeCodes = $passengers->map(fn($r) => $r->employee->employee_code)->join(', ');
                    $totalPassengers += $passengers->count();
                    $totalFare += $trip->total_fare ?? 0;
                @endphp
                <tr>
                    <td>{{ $trip->id }}</td>
                    <td>{{ $trip->started_at->format('d/m/Y') }}</td>
                    <td>{{ $trip->started_at->format('H:i') }}</td>
                    <td>{{ $trip->completed_at ? $trip->completed_at->format('H:i') : '-' }}</td>
                    <td>{{ $trip->route->name }}</td>
                    <td>{{ $trip->route->pickupLocation->name }}</td>
                    <td>{{ $trip->route->dropoffLocation->name }}</td>
                    <td>{{ $trip->vehicle->license_plate }}</td>
                    <td>{{ $trip->driver->name }}</td>
                    <td class="text-center">{{ $passengers->count() }}</td>
                    <td>{{ $passengerNames }}</td>
                    <td>{{ $employeeCodes }}</td>
                    <td class="text-right">{{ number_format($trip->total_fare ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center">ไม่มีข้อมูล</td>
                </tr>
            @endforelse
            
            @if($trips->count() > 0)
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td colspan="9" class="text-right">รวมทั้งหมด:</td>
                    <td class="text-center">{{ $totalPassengers }}</td>
                    <td colspan="2"></td>
                    <td class="text-right">{{ number_format($totalFare, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>ระบบบันทึกการเข้าออกงานและค่าโดยสารรถรับส่งพนักงาน</p>
        <p>พิมพ์โดย: {{ auth()->user()->name ?? 'System' }}</p>
    </div>
</body>
</html>
