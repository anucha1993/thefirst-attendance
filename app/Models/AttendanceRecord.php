<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trip_id',
        'employee_id',
        'scanned_at',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // ============ Relationships ============

    /**
     * Trip this record belongs to
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Employee who scanned
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Audit entries for this attendance record
     */
    public function auditEntries()
    {
        return $this->hasMany(AttendanceAudit::class, 'attendance_record_id');
    }

    // ============ Helper Methods ============

    /**
     * Get attendance detail as array
     */
    public function getDetail()
    {
        return [
            'id' => $this->id,
            'employee_code' => $this->employee->employee_code,
            'employee_name' => $this->employee->getFullName(),
            'scanned_at' => $this->scanned_at->format('Y-m-d H:i:s'),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
