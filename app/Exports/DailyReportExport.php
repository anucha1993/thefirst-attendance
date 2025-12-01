<?php

namespace App\Exports;

use App\Models\Trip;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DailyReportExport implements WithMultipleSheets
{
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // Get all trips for the date
        $trips = Trip::whereDate('started_at', $this->date)
            ->with(['vehicle', 'route.pickupLocation', 'route.dropoffLocation', 'driver', 'attendanceRecords.employee'])
            ->withCount('attendanceRecords')
            ->orderBy('started_at')
            ->get();

        // Group trips by transport company
        $groupedTrips = $trips->groupBy(function ($trip) {
            return $trip->vehicle->transport_company ?? 'ไม่ระบุบริษัท';
        });

        // Create a sheet for each transport company
        foreach ($groupedTrips as $companyName => $companyTrips) {
            $sheets[] = new DailyReportByCompanySheet($companyTrips, $companyName, $this->date);
        }

        // If no trips found, create a summary sheet
        if (empty($sheets)) {
            $sheets[] = new DailyReportByCompanySheet(collect(), 'ไม่มีข้อมูล', $this->date);
        }

        return $sheets;
    }
}

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyReportByCompanySheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $trips;
    protected $companyName;
    protected $date;

    public function __construct($trips, $companyName, $date)
    {
        $this->trips = $trips;
        $this->companyName = $companyName;
        $this->date = $date;
    }

    public function collection()
    {
        return $this->trips;
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
            'บริษัทขนส่ง',
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
            $trip->vehicle->transport_company ?? '-',
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
        // Clean sheet name for Excel compatibility (max 31 chars, no special chars)
        $cleanName = preg_replace('/[\\\\\/\?\*\:\[\]]/', '', $this->companyName);
        return mb_substr($cleanName, 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
