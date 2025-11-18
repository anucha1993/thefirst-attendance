<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDriver extends Model
{
    use HasFactory;

    protected $table = 'vehicle_drivers';

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'assigned_from',
        'assigned_until',
        'is_primary',
    ];

    protected $casts = [
        'assigned_from' => 'date',
        'assigned_until' => 'date',
        'is_primary' => 'boolean',
    ];

    // ============ Relationships ============

    /**
     * Vehicle assigned
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Driver user
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
