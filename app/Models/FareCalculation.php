<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FareCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'fare_rule_id',
        'passenger_count',
        'unit_fare',
        'total_fare',
        'calculation_details',
    ];

    protected $casts = [
        'passenger_count' => 'integer',
        'unit_fare' => 'decimal:2',
        'total_fare' => 'decimal:2',
        'calculation_details' => 'json',
    ];

    // ============ Relationships ============

    /**
     * Trip this calculation is for
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Fare rule used for calculation
     */
    public function fareRule()
    {
        return $this->belongsTo(FareRule::class);
    }
}
