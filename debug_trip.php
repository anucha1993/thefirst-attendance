<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Trip;
use App\Models\FareRule;

$trip = Trip::with(['route', 'vehicle', 'driver', 'attendanceRecords.employee'])->find(1);

if (!$trip) {
    echo "Trip #1 not found\n";
    exit;
}

echo "=== Trip #1 Details ===\n";
echo "ID: {$trip->id}\n";
echo "Status: {$trip->status}\n";
echo "Route: {$trip->route->name}\n";
echo "Vehicle: {$trip->vehicle->license_plate}\n";
echo "Driver: {$trip->driver->name}\n";
echo "Started: {$trip->started_at}\n";
echo "Ended: " . ($trip->ended_at ?? 'NULL') . "\n";
echo "Passenger Count: {$trip->passenger_count}\n";
echo "Total Fare: {$trip->total_fare}\n\n";

echo "=== Passengers ===\n";
foreach ($trip->attendanceRecords as $record) {
    echo "- {$record->employee->employee_code}: {$record->employee->full_name}\n";
}
echo "Total: " . $trip->attendanceRecords->count() . " passengers\n\n";

echo "=== Fare Rule Check ===\n";
$fareRule = $trip->route->fareRules()
    ->where('is_active', true)
    ->where('effective_from', '<=', now()->toDateString())
    ->where(function ($q) {
        $q->whereNull('effective_until')
            ->orWhere('effective_until', '>=', now()->toDateString());
    })
    ->first();

if ($fareRule) {
    echo "Fare Rule Found:\n";
    echo "  Name: {$fareRule->name}\n";
    echo "  Type: {$fareRule->type}\n";
    echo "  Mode: {$fareRule->calculation_mode}\n";
    echo "  Base Fare: {$fareRule->base_fare}\n";
    echo "  Active: " . ($fareRule->is_active ? 'Yes' : 'No') . "\n";
    echo "  Effective From: {$fareRule->effective_from}\n";
    echo "  Effective Until: " . ($fareRule->effective_until ?? 'NULL') . "\n\n";
    
    // Calculate manually
    $tripData = [
        'passenger_count' => $trip->attendanceRecords->count(),
        'distance_km' => $trip->route->distance_km,
    ];
    
    echo "=== Manual Calculation ===\n";
    echo "Passenger Count: {$tripData['passenger_count']}\n";
    echo "Distance: {$tripData['distance_km']} km\n";
    
    $totalFare = $fareRule->calculateFare($tripData);
    echo "Calculated Fare: {$totalFare} THB\n";
} else {
    echo "❌ No fare rule found!\n";
}
