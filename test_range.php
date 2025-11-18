<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Trip;

$trip = Trip::find(1);

echo "=== Trip #1 Dates ===\n";
echo "trip_date: " . ($trip->trip_date ?? 'NULL') . "\n";
echo "started_at: " . $trip->started_at . "\n";
echo "ended_at: " . ($trip->ended_at ?? 'NULL') . "\n\n";

// Check FareCalculation
$fareCalc = \App\Models\FareCalculation::where('trip_id', 1)->first();
if ($fareCalc) {
    echo "=== FareCalculation Found ===\n";
    echo "Total Fare: {$fareCalc->total_fare}\n";
    echo "Passenger Count: {$fareCalc->passenger_count}\n";
} else {
    echo "❌ No FareCalculation record found\n";
}

// Test Range Report query
echo "\n=== Range Report Query Test ===\n";
$dateFrom = '2025-11-18';
$dateTo = '2025-11-18';

echo "Date Range: {$dateFrom} to {$dateTo}\n\n";

// Test using trip_date
$tripsUsingTripDate = Trip::whereBetween('trip_date', [$dateFrom, $dateTo])->count();
echo "Trips found (using trip_date): {$tripsUsingTripDate}\n";

// Test using started_at
$tripsUsingStartedAt = Trip::whereDate('started_at', '>=', $dateFrom)
    ->whereDate('started_at', '<=', $dateTo)
    ->count();
echo "Trips found (using started_at): {$tripsUsingStartedAt}\n";
