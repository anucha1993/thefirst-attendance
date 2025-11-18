<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistanceFareBracket extends Model
{
    use HasFactory;

    protected $fillable = [
        'fare_rule_id',
        'distance_from_km',
        'distance_to_km',
        'fare_per_passenger',
    ];

    protected $casts = [
        'distance_from_km' => 'decimal:2',
        'distance_to_km' => 'decimal:2',
        'fare_per_passenger' => 'decimal:2',
    ];

    // ============ Relationships ============

    /**
     * Fare rule this bracket belongs to
     */
    public function fareRule()
    {
        return $this->belongsTo(FareRule::class);
    }

    // ============ Helper Methods ============

    /**
     * Get display range as string
     */
    public function getRangeDisplay()
    {
        return $this->distance_from_km . ' - ' . $this->distance_to_km . ' km';
    }
}
