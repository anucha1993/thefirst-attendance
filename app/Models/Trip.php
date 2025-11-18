<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'route_id',
        'driver_id',
        'started_at',
        'ended_at',
        'status',
        'passenger_count',
        'total_fare',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'passenger_count' => 'integer',
        'total_fare' => 'decimal:2',
    ];

    // ============ Relationships ============

    /**
     * Vehicle for this trip
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Route for this trip
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Driver who drove this trip
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Attendance records for this trip
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    /**
     * Employees who scanned in for this trip
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'attendance_records', 'trip_id', 'employee_id')
            ->withPivot('scanned_at', 'latitude', 'longitude')
            ->withTimestamps();
    }

    /**
     * Attendance audit logs for this trip
     */
    public function attendanceAudits()
    {
        return $this->hasMany(AttendanceAudit::class);
    }

    /**
     * Fare calculations for this trip
     */
    public function fareCalculations()
    {
        return $this->hasMany(FareCalculation::class);
    }

    // ============ Helper Methods ============

    /**
     * Check if trip is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if trip is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Get duration in minutes
     */
    public function getDurationInMinutes()
    {
        if (!$this->ended_at) {
            return null;
        }
        return $this->started_at->diffInMinutes($this->ended_at);
    }

    /**
     * Get trip summary as array
     */
    public function getSummary()
    {
        return [
            'id' => $this->id,
            'vehicle' => $this->vehicle->license_plate,
            'route' => $this->route->name,
            'driver' => $this->driver->name,
            'started_at' => $this->started_at->format('Y-m-d H:i'),
            'ended_at' => $this->ended_at?->format('Y-m-d H:i'),
            'passenger_count' => $this->passenger_count,
            'total_fare' => $this->total_fare,
            'status' => $this->status,
        ];
    }
}
