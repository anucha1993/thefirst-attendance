<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FareRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'route_id',
        'base_fare',
        'calculation_mode',
        'description',
        'effective_from',
        'effective_until',
        'is_active',
    ];

    protected $casts = [
        'base_fare' => 'decimal:2',
        'effective_from' => 'date',
        'effective_until' => 'date',
        'is_active' => 'boolean',
    ];

    // ============ Relationships ============

    /**
     * Route this fare rule applies to
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Distance-based fare brackets for this rule
     */
    public function distanceBrackets()
    {
        return $this->hasMany(DistanceFareBracket::class);
    }

    /**
     * Fare calculations using this rule
     */
    public function fareCalculations()
    {
        return $this->hasMany(FareCalculation::class);
    }

    // ============ Helper Methods ============

    /**
     * Check if this fare rule is currently effective
     */
    public function isEffective($date = null)
    {
        $date = $date ?? today();
        return $this->is_active
            && $date->gte($this->effective_from)
            && (!$this->effective_until || $date->lte($this->effective_until));
    }

    /**
     * Get fare for a trip based on this rule
     */
    public function calculateFare($tripData)
    {
        if ($this->type === 'fixed') {
            return $this->calculateFixedFare($tripData);
        } elseif ($this->type === 'distance_based') {
            return $this->calculateDistanceFare($tripData);
        }

        return 0;
    }

    /**
     * Calculate fixed fare
     */
    private function calculateFixedFare($tripData)
    {
        $passengerCount = $tripData['passenger_count'] ?? 0;
        $basePrice = $this->base_fare;

        if ($this->calculation_mode === 'per_passenger') {
            return $passengerCount * $basePrice;
        }

        return $basePrice;
    }

    /**
     * Calculate distance-based fare
     */
    private function calculateDistanceFare($tripData)
    {
        $distance = $tripData['distance_km'] ?? 0;
        $passengerCount = $tripData['passenger_count'] ?? 0;

        $bracket = $this->distanceBrackets()
            ->where('distance_from_km', '<=', $distance)
            ->where('distance_to_km', '>=', $distance)
            ->first();

        if (!$bracket) {
            return 0;
        }

        if ($this->calculation_mode === 'per_passenger') {
            return $passengerCount * $bracket->fare_per_passenger;
        }

        return $bracket->fare_per_passenger;
    }
}
