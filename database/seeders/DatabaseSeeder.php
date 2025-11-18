<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Location;
use App\Models\Route;
use App\Models\Vehicle;
use App\Models\VehicleDriver;
use App\Models\Employee;
use App\Models\FareRule;
use App\Models\DistanceFareBracket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@attendance.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create Driver users
        $driver1 = User::create([
            'name' => 'ชัยพร คนขับรถ',
            'email' => 'driver1@attendance.local',
            'password' => Hash::make('password'),
            'role' => 'driver',
            'is_active' => true,
        ]);

        $driver2 = User::create([
            'name' => 'วิชัย ผู้ขับขี่',
            'email' => 'driver2@attendance.local',
            'password' => Hash::make('password'),
            'role' => 'driver',
            'is_active' => true,
        ]);

        // Create Supervisor user
        User::create([
            'name' => 'สุรชัย หัวหน้างาน',
            'email' => 'supervisor@attendance.local',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
            'is_active' => true,
        ]);

        // Create Employee users with associated Employee records
        for ($i = 1; $i <= 50; $i++) {
            $user = User::create([
                'name' => 'พนักงาน ' . $i,
                'email' => 'employee' . $i . '@attendance.local',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'is_active' => true,
            ]);

            Employee::create([
                'user_id' => $user->id,
                'employee_code' => 'EMP' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'first_name' => 'พนักงาน',
                'last_name' => $i,
                'department' => collect(['ผลิต', 'คลังสินค้า', 'แพ็คเกจจิ่ง'])->random(),
                'position' => 'สายการผลิต',
                'email' => 'employee' . $i . '@attendance.local',
                'phone' => '08' . str_pad(rand(1, 999999999), 9, '0', STR_PAD_LEFT),
                'qrcode_token' => Employee::generateQrcodeToken(),
                'is_active' => true,
            ]);
        }

        // Create Locations
        $location1 = Location::create([
            'name' => 'หอพัก A',
            'type' => 'pickup',
            'description' => 'จุดรับพนักงาน',
            'latitude' => 13.7563,
            'longitude' => 100.5018,
        ]);

        $location2 = Location::create([
            'name' => 'หอพัก B',
            'type' => 'pickup',
            'description' => 'จุดรับพนักงาน',
            'latitude' => 13.7500,
            'longitude' => 100.5100,
        ]);

        $location3 = Location::create([
            'name' => 'โรงงาน X',
            'type' => 'dropoff',
            'description' => 'โรงงานปลายทาง',
            'latitude' => 13.7400,
            'longitude' => 100.5200,
        ]);

        $location4 = Location::create([
            'name' => 'โรงงาน Y',
            'type' => 'dropoff',
            'description' => 'โรงงานปลายทาง',
            'latitude' => 13.7300,
            'longitude' => 100.5300,
        ]);

        // Create Routes
        $route1 = Route::create([
            'name' => 'สาย A-1',
            'pickup_location_id' => $location1->id,
            'dropoff_location_id' => $location3->id,
            'distance_km' => 8.5,
            'estimated_duration_minutes' => 30,
            'description' => 'หอพัก A ไป โรงงาน X',
        ]);

        $route2 = Route::create([
            'name' => 'สาย B-1',
            'pickup_location_id' => $location2->id,
            'dropoff_location_id' => $location3->id,
            'distance_km' => 7.2,
            'estimated_duration_minutes' => 25,
            'description' => 'หอพัก B ไป โรงงาน X',
        ]);

        $route3 = Route::create([
            'name' => 'สาย A-2',
            'pickup_location_id' => $location1->id,
            'dropoff_location_id' => $location4->id,
            'distance_km' => 12.3,
            'estimated_duration_minutes' => 40,
            'description' => 'หอพัก A ไป โรงงาน Y',
        ]);

        // Create Vehicles
        $vehicle1 = Vehicle::create([
            'license_plate' => 'กง-1234',
            'vehicle_model' => 'Toyota Hiace',
            'capacity' => 30,
            'status' => 'active',
            'description' => 'รถตู้ 30 ที่นั่ง',
        ]);

        $vehicle2 = Vehicle::create([
            'license_plate' => 'กง-5678',
            'vehicle_model' => 'Isuzu NMR',
            'capacity' => 25,
            'status' => 'active',
            'description' => 'รถตู้ 25 ที่นั่ง',
        ]);

        // Assign drivers to vehicles
        VehicleDriver::create([
            'vehicle_id' => $vehicle1->id,
            'driver_id' => $driver1->id,
            'assigned_from' => now()->startOfDay(),
            'is_primary' => true,
        ]);

        VehicleDriver::create([
            'vehicle_id' => $vehicle2->id,
            'driver_id' => $driver2->id,
            'assigned_from' => now()->startOfDay(),
            'is_primary' => true,
        ]);

        // Create Fare Rules
        $fareRule1 = FareRule::create([
            'name' => 'Fixed Fare - Route A-1',
            'type' => 'fixed',
            'route_id' => $route1->id,
            'base_fare' => 20.00,
            'calculation_mode' => 'per_passenger',
            'description' => 'ค่าโดยสารคงที่ 20 บาท/คน/รอบ สำหรับสาย A-1',
            'effective_from' => now()->startOfDay(),
            'is_active' => true,
        ]);

        $fareRule2 = FareRule::create([
            'name' => 'Fixed Fare - Route B-1',
            'type' => 'fixed',
            'route_id' => $route2->id,
            'base_fare' => 20.00,
            'calculation_mode' => 'per_passenger',
            'description' => 'ค่าโดยสารคงที่ 20 บาท/คน/รอบ สำหรับสาย B-1',
            'effective_from' => now()->startOfDay(),
            'is_active' => true,
        ]);

        $fareRule3 = FareRule::create([
            'name' => 'Distance Based - Route A-2',
            'type' => 'distance_based',
            'route_id' => $route3->id,
            'calculation_mode' => 'per_passenger',
            'description' => 'ค่าโดยสารตามระยะทาง สำหรับสาย A-2',
            'effective_from' => now()->startOfDay(),
            'is_active' => true,
        ]);

        // Create distance brackets for route A-2
        DistanceFareBracket::create([
            'fare_rule_id' => $fareRule3->id,
            'distance_from_km' => 0,
            'distance_to_km' => 5,
            'fare_per_passenger' => 15.00,
        ]);

        DistanceFareBracket::create([
            'fare_rule_id' => $fareRule3->id,
            'distance_from_km' => 5.01,
            'distance_to_km' => 10,
            'fare_per_passenger' => 25.00,
        ]);

        DistanceFareBracket::create([
            'fare_rule_id' => $fareRule3->id,
            'distance_from_km' => 10.01,
            'distance_to_km' => 100,
            'fare_per_passenger' => 35.00,
        ]);
    }
}

