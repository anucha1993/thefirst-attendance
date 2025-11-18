<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // ============ Relationships ============

    /**
     * Routes where this is a pickup location
     */
    public function pickupRoutes()
    {
        return $this->hasMany(Route::class, 'pickup_location_id');
    }

    /**
     * Routes where this is a dropoff location
     */
    public function dropoffRoutes()
    {
        return $this->hasMany(Route::class, 'dropoff_location_id');
    }
}
