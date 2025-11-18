<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Trip;
use App\Models\FareCalculation;

class RecalculateTripFares extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trips:recalculate-fares {--all : Recalculate all trips}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate fare for trips with zero or null total_fare';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Trip::with(['route', 'attendanceRecords']);
        
        if ($this->option('all')) {
            $trips = $query->get();
            $this->info('Recalculating ALL trips...');
        } else {
            $trips = $query->where(function($q) {
                $q->whereNull('total_fare')->orWhere('total_fare', 0);
            })->get();
            $this->info('Recalculating trips with zero/null fare...');
        }

        if ($trips->isEmpty()) {
            $this->warn('No trips found to recalculate.');
            return 0;
        }

        $this->info("Found {$trips->count()} trips to process\n");
        $bar = $this->output->createProgressBar($trips->count());

        $processed = 0;
        $errors = 0;

        foreach ($trips as $trip) {
            try {
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
                    $this->newLine();
                    $this->warn("Trip #{$trip->id}: No fare rule found for route {$trip->route->name}");
                    $errors++;
                    $bar->advance();
                    continue;
                }

                // Calculate fare
                $passengerCount = $trip->attendanceRecords()->count(); // Soft delete auto-handled
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

                $processed++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Trip #{$trip->id}: {$e->getMessage()}");
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Successfully processed: {$processed} trips");
        if ($errors > 0) {
            $this->warn("⚠️  Errors: {$errors} trips");
        }

        return 0;
    }
}
