<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'pickup_location_id',
        'dropoff_location_id',
        'distance_km',
        'estimated_duration_minutes',
        'description',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
    ];

    // ============ Relationships ============

    /**
     * Pickup location for this route
     */
    public function pickupLocation()
    {
        return $this->belongsTo(Location::class, 'pickup_location_id');
    }

    /**
     * Dropoff location for this route
     */
    public function dropoffLocation()
    {
        return $this->belongsTo(Location::class, 'dropoff_location_id');
    }

    /**
     * Trips on this route
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Fare rules for this route
     */
    public function fareRules()
    {
        return $this->hasMany(FareRule::class);
    }

    // ============ Helper Methods ============

    /**
     * Get route display name
     */
    public function getDisplayName()
    {
        return $this->name . ' (' . $this->pickupLocation->name . ' → ' . $this->dropoffLocation->name . ')';
    }
}
