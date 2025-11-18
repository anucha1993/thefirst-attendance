<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Employee;
use App\Services\AttendanceService;
use App\Services\TripService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    protected $attendanceService;
    protected $tripService;

    public function __construct(AttendanceService $attendanceService, TripService $tripService)
    {
        $this->attendanceService = $attendanceService;
        $this->tripService = $tripService;
    }

    /**
     * Driver dashboard
     */
    public function dashboard()
    {
        $driver = auth()->user();
        $vehicles = $driver->vehicles;
        $todayTrips = Trip::whereDate('started_at', today())
            ->where('driver_id', $driver->id)
            ->count();

        return view('driver.dashboard', compact('vehicles', 'todayTrips'));
    }

    /**
     * Show start trip form
     */
    public function startTripForm()
    {
        $driver = auth()->user();
        $vehicles = $driver->vehicles()->where('status', 'active')->get();
        $routes = Route::orderBy('name')->get();

        return view('driver.trip.start-form', compact('vehicles', 'routes'));
    }

    /**
     * Start a new trip
     */
    public function startTrip(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'route_id' => 'required|exists:routes,id',
        ]);

        // Verify driver has access to this vehicle
        $hasVehicle = auth()->user()->vehicles()
            ->where('vehicles.id', $validated['vehicle_id'])
            ->exists();

        if (!$hasVehicle) {
            return back()->with('error', 'คุณไม่มีสิทธิ์เข้าถึงรถคันนี้');
        }

        $trip = $this->tripService->startTrip(
            $validated['vehicle_id'],
            $validated['route_id'],
            auth()->id()
        );

        return redirect()->route('driver.trip.scan', $trip)
            ->with('success', 'เริ่มรอบเรียบร้อย');
    }

    /**
     * Show QR code scanning screen
     */
    public function scanScreen(Trip $trip)
    {
        // Verify this is the driver's trip
        if ($trip->driver_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (!$trip->isActive()) {
            return redirect()->route('driver.dashboard')
                ->with('error', 'รอบนี้ไม่ใช่ลำดับที่ใช้งาน');
        }

        $tripSummary = $this->attendanceService->getTripSummary($trip);
        $recentRecords = $this->attendanceService->getRecentAttendance($trip);

        return view('driver.trip.scan', compact('trip', 'tripSummary', 'recentRecords'));
    }

    /**
     * Process QR code scan (AJAX) - Validation only
     */
    public function processQrcodeScan(Request $request, Trip $trip)
    {
        // Verify this is the driver's trip
        if ($trip->driver_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'qrcode_token' => 'required|string',
        ]);

        try {
            $result = $this->attendanceService->validateQrcodeScan(
                $trip,
                $validated['qrcode_token']
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'type' => 'error',
            ], 400);
        }
    }

    /**
     * Confirm attendance scan (AJAX) - Actually save to database
     */
    public function confirmAttendanceScan(Request $request, Trip $trip)
    {
        // Verify this is the driver's trip
        if ($trip->driver_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'employee_id' => 'required|integer',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            $result = $this->attendanceService->confirmAttendanceRecord(
                $trip,
                $validated['employee_id'],
                [
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                ]
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'type' => 'error',
            ], 400);
        }
    }

    /**
     * Get recent attendance records (for real-time updates)
     */
    public function getRecentRecords(Trip $trip)
    {
        if ($trip->driver_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Refresh trip data from database to get latest passenger_count
        $trip->refresh();
        
        // ดึงทั้งหมดไม่จำกัด
        $records = $this->attendanceService->getRecentAttendance($trip);
        return response()->json([
            'records' => $records,
            'passenger_count' => $trip->passenger_count,
            'trip_status' => $trip->status,
        ]);
    }

    /**
     * Cancel last attendance record
     */
    public function cancelLastRecord(Request $request, Trip $trip)
    {
        if ($trip->driver_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $lastRecord = $trip->attendanceRecords()
            ->latest('scanned_at')
            ->first();

        if (!$lastRecord) {
            return back()->with('error', 'ไม่มีรายการสแกนที่สามารถยกเลิกได้');
        }

        $this->attendanceService->cancelAttendanceRecord(
            $lastRecord,
            $validated['reason'] ?? 'ยกเลิกจากหน้าจอของคนขับ'
        );

        return back()->with('success', 'ยกเลิกรายการสำเร็จ');
    }

    /**
     * Complete a trip
     */
    public function completeTrip(Request $request, Trip $trip)
    {
        if ($trip->driver_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $this->tripService->completeTrip($trip, $validated['notes'] ?? null);

        return redirect()->route('driver.trip-summary', $trip)
            ->with('success', 'ปิดรอบสำเร็จ');
    }

    /**
     * Show trip summary
     */
    public function tripSummary(Trip $trip)
    {
        if ($trip->driver_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $summary = $this->attendanceService->getTripSummary($trip);

        return view('driver.trip.summary', compact('trip', 'summary'));
    }

    /**
     * View today's trips
     */
    public function todayTrips()
    {
        $driver = auth()->user();
        $trips = Trip::whereDate('started_at', today())
            ->where('driver_id', $driver->id)
            ->with(['vehicle', 'route', 'attendanceRecords'])
            ->orderBy('started_at')
            ->get();

        return view('driver.today-trips', compact('trips'));
    }
}
