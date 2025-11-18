<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_code',
        'first_name',
        'last_name',
        'department',
        'position',
        'email',
        'phone',
        'qrcode_token',
        'qrcode_data',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ============ Relationships ============

    /**
     * User account (if employee has login access)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Attendance records for this employee
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    // ============ Helper Methods ============

    /**
     * Get full name
     */
    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get full name attribute (accessor)
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Generate unique QR code token
     */
    public static function generateQrcodeToken()
    {
        do {
            $token = 'EMP-' . strtoupper(uniqid() . '-' . bin2hex(random_bytes(4)));
        } while (self::where('qrcode_token', $token)->exists());

        return $token;
    }

    /**
     * Check if employee scanned in a specific trip
     */
    public function hasScannedInTrip($tripId)
    {
        return $this->attendanceRecords()
            ->where('trip_id', $tripId)
            ->exists();
    }
}
