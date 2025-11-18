<?php

namespace App\Exports;

use App\Models\Trip;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RangeReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $dateFrom;
    protected $dateTo;
    protected $filters;

    public function __construct($dateFrom, $dateTo, $filters = [])
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Trip::with(['vehicle', 'route.pickupLocation', 'route.dropoffLocation', 'driver', 'attendanceRecords.employee'])
            ->whereBetween('trip_date', [$this->dateFrom, $this->dateTo])
            ->whereNotNull('ended_at')
            ->orderBy('trip_date', 'desc')
            ->orderBy('started_at', 'desc');

        // Apply filters
        if (!empty($this->filters['vehicle_id'])) {
            $query->where('vehicle_id', $this->filters['vehicle_id']);
        }

        if (!empty($this->filters['route_id'])) {
            $query->where('route_id', $this->filters['route_id']);
        }

        if (!empty($this->filters['driver_id'])) {
            $query->where('driver_id', $this->filters['driver_id']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Trip ID',
            'วันที่',
            'เวลาเริ่ม',
            'เวลาสิ้นสุด',
            'ระยะเวลา (นาที)',
            'ทะเบียนรถ',
            'รุ่นรถ',
            'ความจุ',
            'สายรถ',
            'เส้นทาง',
            'คนขับ',
            'จำนวนผู้โดยสาร',
            'รายชื่อผู้โดยสาร',
            'รหัสพนักงาน',
            'แผนก',
            'ตำแหน่ง',
            'อัตราเต็ม (%)',
            'ค่าโดยสารรวม (บาท)',
        ];
    }

    public function map($trip): array
    {
        $startedAt = Carbon::parse($trip->started_at);
        $endedAt = Carbon::parse($trip->ended_at);
        $duration = $startedAt->diffInMinutes($endedAt);
        
        $passengers = $trip->attendanceRecords->where('status', 'completed');
        $passengerCount = $passengers->count();
        $passengerNames = $passengers->map(fn($record) => $record->employee->full_name)->join(', ');
        $employeeCodes = $passengers->map(fn($record) => $record->employee->employee_code)->join(', ');
        $departments = $passengers->map(fn($record) => $record->employee->department)->unique()->join(', ');
        $positions = $passengers->map(fn($record) => $record->employee->position)->unique()->join(', ');
        
        $occupancyRate = $trip->vehicle && $trip->vehicle->capacity > 0
            ? ($passengerCount / $trip->vehicle->capacity) * 100
            : 0;

        return [
            $trip->id,
            Carbon::parse($trip->trip_date)->format('d/m/Y'),
            $startedAt->format('H:i'),
            $endedAt->format('H:i'),
            $duration,
            $trip->vehicle ? $trip->vehicle->license_plate : '-',
            $trip->vehicle ? $trip->vehicle->vehicle_model : '-',
            $trip->vehicle ? $trip->vehicle->capacity : '-',
            $trip->route ? $trip->route->name : '-',
            $trip->route
                ? ($trip->route->pickupLocation->name . ' → ' . $trip->route->dropoffLocation->name)
                : '-',
            $trip->driver ? $trip->driver->name : '-',
            $passengerCount,
            $passengerNames,
            $employeeCodes,
            $departments,
            $positions,
            number_format($occupancyRate, 1),
            number_format($trip->total_fare, 2),
        ];
    }

    public function title(): string
    {
        return 'รายงานช่วงเวลา ' . Carbon::parse($this->dateFrom)->format('d/m/Y') . ' - ' . Carbon::parse($this->dateTo)->format('d/m/Y');
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
