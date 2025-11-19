<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        // Return empty rows as example data
        return [
            [
                'EMP001',           // employee_code
                'สมชาย',            // first_name
                'ใจดี',             // last_name
                'พนักงานขับรถ',      // position
                'ฝ่ายขนส่ง',         // department
                '081-234-5678',     // phone
                'somchai@example.com', // email
                '',                 // qrcode_token (optional - will auto-generate)
                '1',                // is_active (1=active, 0=inactive)
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'employee_code',
            'first_name',
            'last_name',
            'position',
            'department',
            'phone',
            'email',
            'qrcode_token',
            'is_active',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA'],
                ],
            ],
        ];
    }
}
