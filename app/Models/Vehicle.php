<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'license_plate',
        'vehicle_model',
        'transport_company',
        'capacity',
        'status',
        'description',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    // ============ Relationships ============

    /**
     * Drivers assigned to this vehicle
     */
    public function vehicleDrivers()
    {
        return $this->hasMany(VehicleDriver::class);
    }

    /**
     * All drivers assigned to this vehicle (many-to-many)
     */
    public function drivers()
    {
        return $this->belongsToMany(User::class, 'vehicle_drivers', 'vehicle_id', 'driver_id')
            ->withPivot('assigned_from', 'assigned_until', 'is_primary')
            ->withTimestamps();
    }

    /**
     * Primary driver for this vehicle
     */
    public function primaryDriver()
    {
        return $this->belongsToMany(User::class, 'vehicle_drivers', 'vehicle_id', 'driver_id')
            ->where('is_primary', true)
            ->withPivot('assigned_from', 'assigned_until')
            ->withTimestamps();
    }

    /**
     * Trips for this vehicle
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    // ============ Helper Methods ============

    /**
     * Check if vehicle is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get active trips for today
     */
    public function todayTrips()
    {
        return $this->trips()
            ->whereDate('started_at', today())
            ->orderBy('started_at')
            ->get();
    }

    /**
     * Get current active trip
     */
    public function currentTrip()
    {
        return $this->trips()
            ->where('status', 'active')
            ->latest('started_at')
            ->first();
    }
}
