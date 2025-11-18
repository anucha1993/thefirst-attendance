<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\FareCalculation;
use Illuminate\Support\Facades\DB;

/**
 * Service for calculating fares based on trips and rules
 */
class FareCalculationService
{
    /**
     * Calculate fare for a completed trip
     *
     * @param Trip $trip
     * @return FareCalculation|null
     */
    public function calculateTripFare(Trip $trip)
    {
        // Get applicable fare rule for this route
        $fareRule = $trip->route->fareRules()
            ->where('is_active', true)
            ->where('effective_from', '<=', $trip->started_at->toDateString())
            ->where(function ($q) {
                $q->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', now()->toDateString());
            })
            ->first();

        if (!$fareRule) {
            return null;
        }

        // Prepare trip data for fare calculation
        $tripData = [
            'passenger_count' => $trip->passenger_count,
            'distance_km' => $trip->route->distance_km,
        ];

        // Calculate fare based on rule type
        $totalFare = $fareRule->calculateFare($tripData);

        // Create and store fare calculation record
        return FareCalculation::create([
            'trip_id' => $trip->id,
            'fare_rule_id' => $fareRule->id,
            'passenger_count' => $trip->passenger_count,
            'unit_fare' => $fareRule->base_fare ?? 0,
            'total_fare' => $totalFare,
            'calculation_details' => [
                'rule_type' => $fareRule->type,
                'calculation_mode' => $fareRule->calculation_mode,
                'distance_km' => $trip->route->distance_km,
            ],
        ]);
    }

    /**
     * Get total fare summary for a date range and filters
     *
     * @param array $filters
     * @return array
     */
    public function getfareSummary(array $filters = [])
    {
        $query = FareCalculation::with(['trip', 'fareRule']);

        if (isset($filters['date_from'])) {
            $query->whereHas('trip', fn($q) => 
                $q->where('started_at', '>=', $filters['date_from'])
            );
        }

        if (isset($filters['date_to'])) {
            $query->whereHas('trip', fn($q) => 
                $q->where('started_at', '<=', $filters['date_to'])
            );
        }

        if (isset($filters['route_id'])) {
            $query->whereHas('trip', fn($q) => 
                $q->where('route_id', $filters['route_id'])
            );
        }

        if (isset($filters['vehicle_id'])) {
            $query->whereHas('trip', fn($q) => 
                $q->where('vehicle_id', $filters['vehicle_id'])
            );
        }

        if (isset($filters['driver_id'])) {
            $query->whereHas('trip', fn($q) => 
                $q->where('driver_id', $filters['driver_id'])
            );
        }

        $calculations = $query->get();

        return [
            'total_fare' => $calculations->sum('total_fare'),
            'total_passengers' => $calculations->sum('passenger_count'),
            'total_trips' => $calculations->count(),
            'calculations' => $calculations->map(fn($c) => [
                'trip_id' => $c->trip_id,
                'route' => $c->trip->route->name,
                'vehicle' => $c->trip->vehicle->license_plate,
                'driver' => $c->trip->driver->name,
                'passenger_count' => $c->passenger_count,
                'total_fare' => $c->total_fare,
                'started_at' => $c->trip->started_at->format('Y-m-d H:i'),
            ])->toArray(),
        ];
    }

    /**
     * Calculate daily summary
     *
     * @param string $date
     * @return array
     */
    public function getDailySummary(string $date)
    {
        $trips = Trip::whereDate('started_at', $date)
            ->with(['vehicle', 'route', 'driver', 'attendanceRecords'])
            ->orderBy('started_at')
            ->get();

        $summary = [
            'date' => $date,
            'total_trips' => $trips->count(),
            'total_passengers' => $trips->sum('passenger_count'),
            'total_fare' => 0,
            'trips_by_vehicle' => [],
            'trips_by_route' => [],
        ];

        foreach ($trips as $trip) {
            // Calculate fare for each trip if not already done
            if (!$trip->total_fare) {
                $fareCalc = $this->calculateTripFare($trip);
                if ($fareCalc) {
                    $trip->update(['total_fare' => $fareCalc->total_fare]);
                    $summary['total_fare'] += $fareCalc->total_fare;
                }
            } else {
                $summary['total_fare'] += $trip->total_fare;
            }

            // Group by vehicle
            $vehicleKey = $trip->vehicle->license_plate;
            if (!isset($summary['trips_by_vehicle'][$vehicleKey])) {
                $summary['trips_by_vehicle'][$vehicleKey] = [
                    'vehicle' => $trip->vehicle,
                    'trips' => [],
                    'passenger_count' => 0,
                    'total_fare' => 0,
                ];
            }
            $summary['trips_by_vehicle'][$vehicleKey]['passenger_count'] += $trip->passenger_count;
            $summary['trips_by_vehicle'][$vehicleKey]['total_fare'] += $trip->total_fare ?? 0;
            $summary['trips_by_vehicle'][$vehicleKey]['trips'][] = $trip;

            // Group by route
            $routeKey = $trip->route->name;
            if (!isset($summary['trips_by_route'][$routeKey])) {
                $summary['trips_by_route'][$routeKey] = [
                    'route' => $trip->route,
                    'trips' => [],
                    'passenger_count' => 0,
                    'total_fare' => 0,
                ];
            }
            $summary['trips_by_route'][$routeKey]['passenger_count'] += $trip->passenger_count;
            $summary['trips_by_route'][$routeKey]['total_fare'] += $trip->total_fare ?? 0;
            $summary['trips_by_route'][$routeKey]['trips'][] = $trip;
        }

        return $summary;
    }
}
