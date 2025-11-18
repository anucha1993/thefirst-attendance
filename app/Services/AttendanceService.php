<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Trip;
use App\Models\AttendanceRecord;
use App\Models\AttendanceAudit;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Service for managing employee attendance scanning
 */
class AttendanceService
{
    /**
     * Process QR code scan for an employee
     *
     * @param Trip $trip
     * @param string $qrcodeToken
     * @param array $locationData
     * @return array
     * @throws InvalidArgumentException
     */
    public function processQrcodeScan(Trip $trip, string $qrcodeToken, array $locationData = [])
    {
        // Find employee by QR code token
        $employee = Employee::where('qrcode_token', $qrcodeToken)->firstOrFail();

        // Validate trip is active
        if (!$trip->isActive()) {
            throw new InvalidArgumentException('Trip is not active.');
        }

        // Check for duplicate scan in same trip
        if ($employee->hasScannedInTrip($trip->id)) {
            return [
                'success' => false,
                'message' => 'ผู้ใช้นี้ได้สแกนในรอบนี้แล้ว',
                'type' => 'duplicate',
            ];
        }

        // Create attendance record
        $record = DB::transaction(function () use ($trip, $employee, $locationData) {
            $record = AttendanceRecord::create([
                'trip_id' => $trip->id,
                'employee_id' => $employee->id,
                'scanned_at' => now(),
                'latitude' => $locationData['latitude'] ?? null,
                'longitude' => $locationData['longitude'] ?? null,
            ]);

            // Update trip passenger count
            $trip->increment('passenger_count');

            // Create audit log
            AttendanceAudit::create([
                'trip_id' => $trip->id,
                'attendance_record_id' => $record->id,
                'user_id' => auth()->id(),
                'action' => 'created',
                'reason' => 'QR code scan',
                'new_data' => $record->getDetail(),
            ]);

            return $record;
        });

        return [
            'success' => true,
            'message' => 'สแกนสำเร็จ',
            'type' => 'success',
            'data' => [
                'employee_code' => $employee->employee_code,
                'employee_name' => $employee->getFullName(),
                'scanned_at' => $record->scanned_at->format('H:i:s'),
                'passenger_count' => $trip->passenger_count,
            ],
        ];
    }

    /**
     * Cancel attendance record
     *
     * @param AttendanceRecord $record
     * @param string $reason
     * @return array
     */
    public function cancelAttendanceRecord(AttendanceRecord $record, string $reason = '')
    {
        return DB::transaction(function () use ($record, $reason) {
            $trip = $record->trip;

            // Store old data
            $oldData = $record->getDetail();

            // Decrement passenger count
            $trip->decrement('passenger_count');

            // Create audit log
            AttendanceAudit::create([
                'trip_id' => $trip->id,
                'attendance_record_id' => $record->id,
                'user_id' => auth()->id(),
                'action' => 'cancelled',
                'reason' => $reason,
                'old_data' => $oldData,
            ]);

            // Soft delete the record
            $record->delete();

            return [
                'success' => true,
                'message' => 'ยกเลิกการสแกนสำเร็จ',
                'passenger_count' => $trip->passenger_count,
            ];
        });
    }

    /**
     * Get attendance summary for a trip
     *
     * @param Trip $trip
     * @return array
     */
    public function getTripSummary(Trip $trip)
    {
        $records = $trip->attendanceRecords()
            ->with('employee')
            ->orderBy('scanned_at')
            ->get();

        return [
            'trip_id' => $trip->id,
            'vehicle' => $trip->vehicle->license_plate,
            'route' => $trip->route->name,
            'driver' => $trip->driver->name,
            'status' => $trip->status,
            'started_at' => $trip->started_at->format('Y-m-d H:i:s'),
            'ended_at' => $trip->ended_at?->format('Y-m-d H:i:s'),
            'passenger_count' => $trip->passenger_count,
            'total_fare' => $trip->total_fare,
            'records' => $records->map(fn($r) => [
                'id' => $r->id,
                'employee_code' => $r->employee->employee_code,
                'employee_name' => $r->employee->getFullName(),
                'scanned_at' => $r->scanned_at->format('H:i:s'),
            ])->toArray(),
        ];
    }

    /**
     * Get last N attendance records for a trip (for real-time display)
     *
     * @param Trip $trip
     * @param int $limit
     * @return array
     */
    public function getRecentAttendance(Trip $trip, int $limit = 10)
    {
        return $trip->attendanceRecords()
            ->with('employee')
            ->latest('scanned_at')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(fn($r) => [
                'id' => $r->id,
                'employee_code' => $r->employee->employee_code,
                'employee_name' => $r->employee->getFullName(),
                'scanned_at' => $r->scanned_at->format('H:i:s'),
            ])
            ->toArray();
    }
}
