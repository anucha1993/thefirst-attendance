<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Location;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $attendanceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendanceService = new AttendanceService();
    }

    /**
     * Test: Prevent duplicate scan in same trip
     */
    public function test_prevent_duplicate_scan_in_same_trip()
    {
        // Create test data
        $location1 = Location::create([
            'name' => 'หอพัก A',
            'type' => 'pickup',
        ]);
        $location2 = Location::create([
            'name' => 'โรงงาน X',
            'type' => 'dropoff',
        ]);

        $route = Route::create([
            'name' => 'สาย A-1',
            'pickup_location_id' => $location1->id,
            'dropoff_location_id' => $location2->id,
            'distance_km' => 8.5,
        ]);

        $vehicle = Vehicle::create([
            'license_plate' => 'กง-1234',
            'capacity' => 30,
        ]);

        $driver = User::factory()->create(['role' => 'driver']);
        $employee = Employee::create([
            'employee_code' => 'EMP00001',
            'first_name' => 'ทดสอบ',
            'last_name' => 'ระบบ',
            'qrcode_token' => Employee::generateQrcodeToken(),
        ]);

        $trip = Trip::create([
            'vehicle_id' => $vehicle->id,
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'started_at' => now(),
            'status' => 'active',
        ]);

        // First scan - should succeed
        $result1 = $this->attendanceService->processQrcodeScan($trip, $employee->qrcode_token);
        $this->assertTrue($result1['success']);
        $this->assertEquals('success', $result1['type']);

        // Second scan - should fail with duplicate message
        $result2 = $this->attendanceService->processQrcodeScan($trip, $employee->qrcode_token);
        $this->assertFalse($result2['success']);
        $this->assertEquals('duplicate', $result2['type']);

        // Verify only one record in database
        $this->assertEquals(1, $trip->attendanceRecords()->count());
    }

    /**
     * Test: QR code token uniqueness
     */
    public function test_qrcode_token_is_unique()
    {
        $emp1 = Employee::create([
            'employee_code' => 'EMP00001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'qrcode_token' => 'EMP-ABC123',
        ]);

        // Attempt to create another employee with same token
        $this->expectException(\Illuminate\Database\QueryException::class);

        Employee::create([
            'employee_code' => 'EMP00002',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'qrcode_token' => 'EMP-ABC123', // Duplicate!
        ]);
    }

    /**
     * Test: Driver can scan QR codes
     */
    public function test_driver_can_scan_qrcode()
    {
        $driver = User::factory()->create(['role' => 'driver']);
        $employee = Employee::factory()->create();

        // Create trip
        $trip = Trip::factory()
            ->for($driver, 'driver')
            ->create(['status' => 'active']);

        $this->actingAs($driver);

        $response = $this->postJson(route('driver.trip.scan-process', $trip), [
            'qrcode_token' => $employee->qrcode_token,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Test: Cannot scan for inactive employee
     */
    public function test_cannot_scan_inactive_employee()
    {
        $driver = User::factory()->create(['role' => 'driver']);
        $employee = Employee::factory()->create(['is_active' => false]);

        $trip = Trip::factory()
            ->for($driver, 'driver')
            ->create(['status' => 'active']);

        $this->actingAs($driver);

        $response = $this->postJson(route('driver.trip.scan-process', $trip), [
            'qrcode_token' => $employee->qrcode_token,
        ]);

        $response->assertStatus(400);
    }

    /**
     * Test: Cannot scan for completed trip
     */
    public function test_cannot_scan_for_completed_trip()
    {
        $driver = User::factory()->create(['role' => 'driver']);
        $employee = Employee::factory()->create();

        $trip = Trip::factory()
            ->for($driver, 'driver')
            ->create(['status' => 'completed']);

        $this->actingAs($driver);

        $response = $this->postJson(route('driver.trip.scan-process', $trip), [
            'qrcode_token' => $employee->qrcode_token,
        ]);

        $response->assertStatus(400);
    }

    /**
     * Test: Can retrieve trip summary
     */
    public function test_get_trip_summary()
    {
        $trip = Trip::factory()->create(['status' => 'active']);

        // Add some attendance records
        for ($i = 0; $i < 5; $i++) {
            $employee = Employee::factory()->create();
            $trip->attendanceRecords()->create([
                'employee_id' => $employee->id,
                'scanned_at' => now()->subMinutes(5 - $i),
            ]);
        }

        $summary = $this->attendanceService->getTripSummary($trip);

        $this->assertEquals($trip->id, $summary['trip_id']);
        $this->assertEquals(5, $summary['passenger_count']);
        $this->assertCount(5, $summary['records']);
    }
}
