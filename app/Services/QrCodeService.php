<?php

namespace App\Services;

use App\Models\Employee;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

/**
 * Service for QR code generation
 */
class QrCodeService
{
    /**
     * Generate QR code for an employee
     *
     * @param Employee $employee
     * @return string The URL or data to be encoded in QR code
     */
    public function generateQrCodeData(Employee $employee): string
    {
        // Generate QR code URL or token data
        // Format: https://domain.com/scan/EMP-xxxxx
        // Or simply use the token: EMP-xxxxx
        return $employee->qrcode_token;
    }

    /**
     * Generate QR code image and save it
     *
     * @param Employee $employee
     * @param string $format
     * @return string Path to the saved image
     */
    public function generateAndSaveQrCode(Employee $employee, string $format = 'svg'): string
    {
        $qrCodeData = $this->generateQrCodeData($employee);
        $fileName = 'qrcodes/' . $employee->employee_code . '.' . $format;

        if ($format === 'svg') {
            $qrCode = QrCode::size(300)
                ->encoding('UTF-8')
                ->format('svg')
                ->generate($qrCodeData);
        } else {
            $qrCode = QrCode::size(300)
                ->encoding('UTF-8')
                ->format('png')
                ->generate($qrCodeData);
        }

        Storage::disk('public')->put($fileName, $qrCode);

        return Storage::url($fileName);
    }

    /**
     * Get QR code for employee (generate if not exists)
     *
     * @param Employee $employee
     * @return string
     */
    public function getQrCodeUrl(Employee $employee): string
    {
        if ($employee->qrcode_data) {
            return $employee->qrcode_data;
        }

        // Generate new QR code
        $url = $this->generateAndSaveQrCode($employee, 'png');
        
        // Store in database
        $employee->update(['qrcode_data' => $url]);

        return $url;
    }

    /**
     * Validate QR code token
     *
     * @param string $token
     * @return bool
     */
    public function validateQrCodeToken(string $token): bool
    {
        return Employee::where('qrcode_token', $token)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get employee from QR code token
     *
     * @param string $token
     * @return Employee|null
     */
    public function getEmployeeFromToken(string $token): ?Employee
    {
        return Employee::where('qrcode_token', $token)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Regenerate QR code token (in case of compromise)
     *
     * @param Employee $employee
     * @return string New token
     */
    public function regenerateQrCodeToken(Employee $employee): string
    {
        $oldToken = $employee->qrcode_token;
        $newToken = Employee::generateQrcodeToken();

        $employee->update([
            'qrcode_token' => $newToken,
            'qrcode_data' => null, // Clear the stored data to regenerate
        ]);

        return $newToken;
    }
}
