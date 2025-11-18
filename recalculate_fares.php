<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Trip;
use App\Models\FareRule;
use App\Models\FareCalculation;

// Get all active trips without fare calculation
$trips = Trip::whereNull('total_fare')
    ->orWhere('total_fare', 0)
    ->with(['route', 'attendanceRecords'])
    ->get();

echo "Found " . $trips->count() . " trips to recalculate\n\n";

foreach ($trips as $trip) {
    echo "Processing Trip #{$trip->id}...\n";
    
    // Get fare rule
    $fareRule = $trip->route->fareRules()
        ->where('is_active', true)
        ->where('effective_from', '<=', now()->toDateString())
        ->where(function ($q) {
            $q->whereNull('effective_until')
                ->orWhere('effective_until', '>=', now()->toDateString());
        })
        ->first();

    if (!$fareRule) {
        echo "  ❌ No fare rule found for route {$trip->route->name}\n";
        continue;
    }

    // Calculate fare
    $passengerCount = $trip->attendanceRecords()->where('status', 'completed')->count();
    $tripData = [
        'passenger_count' => $passengerCount,
        'distance_km' => $trip->route->distance_km,
    ];

    $totalFare = $fareRule->calculateFare($tripData);
    
    // Update trip
    $trip->update([
        'passenger_count' => $passengerCount,
        'total_fare' => $totalFare
    ]);

    // Create or update FareCalculation
    FareCalculation::updateOrCreate(
        ['trip_id' => $trip->id],
        [
            'fare_rule_id' => $fareRule->id,
            'passenger_count' => $passengerCount,
            'unit_fare' => $fareRule->base_fare ?? 0,
            'total_fare' => $totalFare,
            'calculation_details' => [
                'rule_type' => $fareRule->type,
                'calculation_mode' => $fareRule->calculation_mode,
                'distance_km' => $trip->route->distance_km,
            ],
        ]
    );

    echo "  ✅ Trip #{$trip->id}: {$passengerCount} passengers x {$fareRule->base_fare} = {$totalFare} THB\n";
}

echo "\n✅ Done!\n";
