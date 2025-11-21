<?php

namespace App\Exports;

use App\Models\Trip;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function collection()
    {
        return Trip::whereDate('started_at', $this->date)
            ->with(['vehicle', 'route.pickupLocation', 'route.dropoffLocation', 'driver', 'attendanceRecords.employee'])
            ->withCount('attendanceRecords')
            ->orderBy('started_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'รหัสรอบ',
            'วันที่',
            'เวลาเริ่ม',
            'เวลาสิ้นสุด',
            'สายรถ',
            'จุดรับ',
            'จุดส่ง',
            'ป้ายทะเบียน',
            'รุ่นรถ',
            'คนขับ',
            'จำนวนผู้โดยสาร',
            'รายชื่อผู้โดยสาร',
            'รหัสพนักงาน',
            'แผนก',
            'ตำแหน่ง',
            'ที่นั่งทั้งหมด',
            'ค่าโดยสาร (บาท)',
            'สถานะ',
        ];
    }

    public function map($trip): array
    {
        $passengers = $trip->attendanceRecords; // Soft delete already filters out deleted records
        $passengerNames = $passengers->map(fn($record) => $record->employee->full_name)->join(', ');
        $employeeCodes = $passengers->map(fn($record) => $record->employee->employee_code)->join(', ');
        $departments = $passengers->map(fn($record) => $record->employee->department)->unique()->join(', ');
        $positions = $passengers->map(fn($record) => $record->employee->position)->unique()->join(', ');

        return [
            $trip->id,
            $trip->started_at->format('d/m/Y'),
            $trip->started_at->format('H:i'),
            $trip->ended_at ? $trip->ended_at->format('H:i') : '-',
            $trip->route->name,
            $trip->route->pickupLocation->name,
            $trip->route->dropoffLocation->name,
            $trip->vehicle->license_plate,
            $trip->vehicle->vehicle_model,
            $trip->driver->name,
            $passengers->count(),
            $passengerNames,
            $employeeCodes,
            $departments,
            $positions,
            $trip->vehicle->capacity,
            $trip->total_fare ?? 0,
            $trip->status === 'completed' ? 'เสร็จสิ้น' : 'กำลังดำเนินการ',
        ];
    }

    public function title(): string
    {
        return 'รายงานรายวัน ' . date('d/m/Y', strtotime($this->date));
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
