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

class TripDetailsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $trip;

    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }

    public function collection()
    {
        return $this->trip->attendanceRecords()
            ->with('employee')
            ->orderBy('scanned_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ลำดับ',
            'รหัสพนักงาน',
            'ชื่อ',
            'นามสกุล',
            'แผนก',
            'ตำแหน่ง',
            'เบอร์โทร',
            'อีเมล',
            'เวลาสแกน',
            'ค่าโดยสาร (บาท)',
            'สถานะ',
        ];
    }

    public function map($record): array
    {
        static $index = 0;
        $index++;

        // Calculate fare per passenger
        $farePerPassenger = 0;
        if ($this->trip->total_fare && $this->trip->passenger_count > 0) {
            $farePerPassenger = $this->trip->total_fare / $this->trip->passenger_count;
        }

        return [
            $index,
            $record->employee->employee_code,
            $record->employee->first_name,
            $record->employee->last_name,
            $record->employee->department ?? '-',
            $record->employee->position ?? '-',
            $record->employee->phone ?? '-',
            $record->employee->email ?? '-',
            $record->scanned_at->format('d/m/Y H:i:s'),
            number_format($farePerPassenger, 2),
            $record->deleted_at ? 'ยกเลิก' : 'ปกติ',
        ];
    }

    public function title(): string
    {
        return 'รอบ #' . $this->trip->id;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
