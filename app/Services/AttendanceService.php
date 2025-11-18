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
            $trip->passenger_count = $trip->passenger_count + 1;
            $trip->save();

            // Recalculate fare for active trip
            $this->recalculateTripFare($trip);

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
     * Validate QR code scan without saving to database
     *
     * @param Trip $trip
     * @param string $qrcodeToken
     * @return array
     */
    public function validateQrcodeScan(Trip $trip, string $qrcodeToken)
    {
        try {
            // Find employee by QR code token
            $employee = Employee::where('qrcode_token', $qrcodeToken)->firstOrFail();

            // Check if employee already scanned in this trip
            if ($employee->hasScannedInTrip($trip->id)) {
                return [
                    'success' => false,
                    'message' => 'พนักงานท่านนี้ได้สแกนแล้ว',
                    'type' => 'duplicate',
                    'data' => [
                        'employee_code' => $employee->employee_code,
                        'employee_name' => $employee->getFullName(),
                    ],
                ];
            }

            // Return employee data for confirmation
            return [
                'success' => true,
                'message' => 'พบข้อมูลพนักงาน',
                'type' => 'pending',
                'data' => [
                    'employee_id' => $employee->id,
                    'employee_code' => $employee->employee_code,
                    'employee_name' => $employee->getFullName(),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'ไม่พบข้อมูลพนักงาน: ' . $e->getMessage(),
                'type' => 'error',
            ];
        }
    }

    /**
     * Confirm and save attendance record
     *
     * @param Trip $trip
     * @param int $employeeId
     * @param array $locationData
     * @return array
     */
    public function confirmAttendanceRecord(Trip $trip, int $employeeId, array $locationData = [])
    {
        try {
            $employee = Employee::findOrFail($employeeId);

            // Double check for duplicates
            if ($employee->hasScannedInTrip($trip->id)) {
                return [
                    'success' => false,
                    'message' => 'พนักงานท่านนี้ได้สแกนแล้ว',
                    'type' => 'duplicate',
                ];
            }

            // Create attendance record in database transaction
            $record = DB::transaction(function () use ($trip, $employee, $locationData) {
                $record = AttendanceRecord::create([
                    'trip_id' => $trip->id,
                    'employee_id' => $employee->id,
                    'scanned_at' => now(),
                    'scanned_by' => auth()->id(),
                    'scan_latitude' => $locationData['latitude'] ?? null,
                    'scan_longitude' => $locationData['longitude'] ?? null,
                ]);

                // Update trip passenger count
                $trip->passenger_count = $trip->passenger_count + 1;
                $trip->save();

                // Recalculate fare for the trip
                $this->recalculateTripFare($trip);

                // Create audit log
                AttendanceAudit::create([
                    'trip_id' => $trip->id,
                    'attendance_record_id' => $record->id,
                    'user_id' => auth()->id(),
                    'action' => 'created',
                    'reason' => 'QR Code Scan',
                    'old_data' => null,
                    'new_data' => json_encode($record->toArray()),
                ]);

                return $record;
            });

            return [
                'success' => true,
                'message' => 'บันทึกข้อมูลสำเร็จ',
                'type' => 'success',
                'data' => [
                    'employee_code' => $employee->employee_code,
                    'employee_name' => $employee->getFullName(),
                    'scanned_at' => $record->scanned_at->format('H:i:s'),
                    'passenger_count' => $trip->passenger_count,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'type' => 'error',
            ];
        }
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
            $trip->passenger_count = $trip->passenger_count - 1;
            $trip->save();

            // Recalculate fare
            $this->recalculateTripFare($trip);

            // Create audit log
            AttendanceAudit::create([
                'trip_id' => $trip->id,
                'attendance_record_id' => $record->id,
                'user_id' => auth()->id(),
                'action' => 'cancelled',
                'reason' => $reason,
                'old_data' => $oldData,
                'new_data' => null,
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
    /**
     * @return array
     */
    public function getRecentAttendance(Trip $trip, int $limit = null)
    {
        $query = $trip->attendanceRecords()
            ->with('employee')
            ->latest('scanned_at');
        
        if ($limit !== null) {
            $query->limit($limit);
        }
        
        return $query->get()
            ->reverse()
            ->map(fn($r) => [
                'id' => $r->id,
                'employee_code' => $r->employee->employee_code,
                'employee_name' => $r->employee->getFullName(),
                'scanned_at' => $r->scanned_at->format('H:i:s'),
            ])
            ->toArray();
    }

    /**
     * Recalculate trip fare based on current passenger count
     *
     * @param Trip $trip
     * @return void
     */
    private function recalculateTripFare(Trip $trip)
    {
        $fareCalcService = new FareCalculationService();
        
        // Get applicable fare rule
        $fareRule = $trip->route->fareRules()
            ->where('is_active', true)
            ->where('effective_from', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', now()->toDateString());
            })
            ->first();

        if (!$fareRule) {
            return;
        }

        // Calculate fare
        $tripData = [
            'passenger_count' => $trip->passenger_count,
            'distance_km' => $trip->route->distance_km,
        ];

        $totalFare = $fareRule->calculateFare($tripData);
        
        // Update trip fare
        $trip->total_fare = $totalFare;
        $trip->save();

        // Update or create FareCalculation record
        \App\Models\FareCalculation::updateOrCreate(
            ['trip_id' => $trip->id],
            [
                'fare_rule_id' => $fareRule->id,
                'passenger_count' => $trip->passenger_count,
                'unit_fare' => $fareRule->base_fare ?? 0,
                'total_fare' => $totalFare,
                'calculation_details' => [
                    'rule_type' => $fareRule->type,
                    'calculation_mode' => $fareRule->calculation_mode,
                    'distance_km' => $trip->route->distance_km,
                ],
            ]
        );
    }
}
