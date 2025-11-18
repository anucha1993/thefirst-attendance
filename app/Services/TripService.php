<?php

namespace App\Services;

use App\Models\Trip;
use Illuminate\Support\Facades\DB;

/**
 * Service for managing trips
 */
class TripService
{
    /**
     * Start a new trip
     *
     * @param int $vehicleId
     * @param int $routeId
     * @param int $driverId
     * @return Trip
     */
    public function startTrip(int $vehicleId, int $routeId, int $driverId): Trip
    {
        return Trip::create([
            'vehicle_id' => $vehicleId,
            'route_id' => $routeId,
            'driver_id' => $driverId,
            'started_at' => now(),
            'status' => 'active',
            'passenger_count' => 0,
        ]);
    }

    /**
     * End/complete a trip
     *
     * @param Trip $trip
     * @param string|null $notes
     * @return Trip
     */
    public function completeTrip(Trip $trip, ?string $notes = null): Trip
    {
        $trip->update([
            'ended_at' => now(),
            'status' => 'completed',
            'notes' => $notes,
        ]);

        // Calculate fare if not already done
        if (!$trip->total_fare) {
            $fareCalcService = new FareCalculationService();
            $fareCalc = $fareCalcService->calculateTripFare($trip);
            if ($fareCalc) {
                $trip->update(['total_fare' => $fareCalc->total_fare]);
            }
        }

        return $trip;
    }

    /**
     * Cancel a trip
     *
     * @param Trip $trip
     * @param string|null $reason
     * @return Trip
     */
    public function cancelTrip(Trip $trip, ?string $reason = null): Trip
    {
        $trip->update([
            'status' => 'cancelled',
            'notes' => $reason ?? $trip->notes,
        ]);

        return $trip;
    }

    /**
     * Get active trip for a vehicle
     *
     * @param int $vehicleId
     * @return Trip|null
     */
    public function getActiveTrip(int $vehicleId): ?Trip
    {
        return Trip::where('vehicle_id', $vehicleId)
            ->where('status', 'active')
            ->orderByDesc('started_at')
            ->first();
    }

    /**
     * Get today's trips for a vehicle
     *
     * @param int $vehicleId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTodayTrips(int $vehicleId)
    {
        return Trip::where('vehicle_id', $vehicleId)
            ->whereDate('started_at', today())
            ->orderBy('started_at')
            ->get();
    }

    /**
     * Get trips for a date range
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTripsInRange(string $dateFrom, string $dateTo, array $filters = [])
    {
        $query = Trip::whereBetween('started_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->with(['vehicle', 'route', 'driver']);

        if (isset($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        if (isset($filters['route_id'])) {
            $query->where('route_id', $filters['route_id']);
        }

        if (isset($filters['driver_id'])) {
            $query->where('driver_id', $filters['driver_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('started_at')->get();
    }
}
