<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class EmployeesImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Generate QR code token if not provided
        $qrcodeToken = $row['qrcode_token'] ?? Str::random(32);
        
        return new Employee([
            'employee_code' => $row['employee_code'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'position' => $row['position'] ?? null,
            'department' => $row['department'] ?? null,
            'phone' => $row['phone'] ?? null,
            'email' => $row['email'] ?? null,
            'qrcode_token' => $qrcodeToken,
            'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'employee_code' => 'required|unique:employees,employee_code',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'employee_code.required' => 'รหัสพนักงานห้ามว่าง',
            'employee_code.unique' => 'รหัสพนักงานซ้ำ',
            'first_name.required' => 'ชื่อห้ามว่าง',
            'last_name.required' => 'นามสกุลห้ามว่าง',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
        ];
    }
}
