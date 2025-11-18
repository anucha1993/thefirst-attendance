<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Route;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\FareRule;
use App\Models\DistanceFareBracket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_employees' => Employee::where('is_active', true)->count(),
            'total_vehicles' => Vehicle::where('status', 'active')->count(),
            'total_routes' => Route::count(),
            'today_trips' => \App\Models\Trip::whereDate('started_at', today())->count(),
        ];

        return view('admin.dashboard', $stats);
    }

    /**
     * Locations management
     */
    public function locationsIndex()
    {
        $locations = Location::withCount(['pickupRoutes', 'dropoffRoutes'])->paginate(20);
        return view('admin.locations.index', compact('locations'));
    }

    public function locationsCreate()
    {
        return view('admin.locations.create');
    }

    public function locationsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:pickup,dropoff,both',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        Location::create($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'สร้างจุดรับ–ส่งเรียบร้อย');
    }

    public function locationsEdit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function locationsUpdate(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:pickup,dropoff,both',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $location->update($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'แก้ไขจุดรับ–ส่งเรียบร้อย');
    }

    public function locationsDestroy(Location $location)
    {
        $location->delete();
        return redirect()->route('admin.locations.index')
            ->with('success', 'ลบจุดรับ–ส่งเรียบร้อย');
    }

    /**
     * Routes management
     */
    public function routesIndex()
    {
        $routes = Route::with(['pickupLocation', 'dropoffLocation'])
            ->withCount('trips')
            ->paginate(20);
        return view('admin.routes.index', compact('routes'));
    }

    public function routesCreate()
    {
        $locations = Location::orderBy('name')->get();
        return view('admin.routes.create', compact('locations'));
    }

    public function routesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:routes',
            'pickup_location_id' => 'required|exists:locations,id',
            'dropoff_location_id' => 'required|exists:locations,id|different:pickup_location_id',
            'distance_km' => 'nullable|numeric|min:0',
            'estimated_duration_minutes' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        Route::create($validated);

        return redirect()->route('admin.routes.index')
            ->with('success', 'สร้างสายรถเรียบร้อย');
    }

    public function routesEdit(Route $route)
    {
        $locations = Location::orderBy('name')->get();
        return view('admin.routes.edit', compact('route', 'locations'));
    }

    public function routesUpdate(Request $request, Route $route)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:routes,name,' . $route->id,
            'pickup_location_id' => 'required|exists:locations,id',
            'dropoff_location_id' => 'required|exists:locations,id|different:pickup_location_id',
            'distance_km' => 'nullable|numeric|min:0',
            'estimated_duration_minutes' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $route->update($validated);

        return redirect()->route('admin.routes.index')
            ->with('success', 'แก้ไขสายรถเรียบร้อย');
    }

    public function routesDestroy(Route $route)
    {
        $route->delete();
        return redirect()->route('admin.routes.index')
            ->with('success', 'ลบสายรถเรียบร้อย');
    }

    /**
     * Vehicles management
     */
    public function vehiclesIndex()
    {
        $vehicles = Vehicle::with('drivers')
            ->withCount('trips')
            ->paginate(20);
        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function vehiclesCreate()
    {
        return view('admin.vehicles.create');
    }

    public function vehiclesStore(Request $request)
    {
        $validated = $request->validate([
            'license_plate' => 'required|string|unique:vehicles|max:20',
            'vehicle_model' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string',
        ]);

        Vehicle::create($validated);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'เพิ่มรถเรียบร้อย');
    }

    public function vehiclesEdit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function vehiclesUpdate(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'license_plate' => 'required|string|unique:vehicles,license_plate,' . $vehicle->id . '|max:20',
            'vehicle_model' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string',
        ]);

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'แก้ไขรถเรียบร้อย');
    }

    public function vehiclesDestroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index')
            ->with('success', 'ลบรถเรียบร้อย');
    }

    /**
     * Employees management
     */
    public function employeesIndex()
    {
        $employees = Employee::with('user')
            ->orderBy('employee_code')
            ->paginate(20);
        return view('admin.employees.index', compact('employees'));
    }

    public function employeesCreate()
    {
        return view('admin.employees.create');
    }

    public function employeesStore(Request $request)
    {
        $validated = $request->validate([
            'employee_code' => 'required|string|unique:employees|max:50',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:employees|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $validated['qrcode_token'] = Employee::generateQrcodeToken();
        $validated['is_active'] = true;

        Employee::create($validated);

        return redirect()->route('admin.employees.index')
            ->with('success', 'เพิ่มพนักงานเรียบร้อย');
    }

    public function employeesEdit(Employee $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function employeesUpdate(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'employee_code' => 'required|string|unique:employees,employee_code,' . $employee->id . '|max:50',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:employees,email,' . $employee->id . '|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $employee->update($validated);

        return redirect()->route('admin.employees.index')
            ->with('success', 'แก้ไขพนักงานเรียบร้อย');
    }

    public function employeesShowQrCode(Employee $employee)
    {
        $qrCodeService = new \App\Services\QrCodeService();
        $qrCodeUrl = $qrCodeService->getQrCodeUrl($employee);

        return view('admin.employees.qrcode', compact('employee', 'qrCodeUrl'));
    }

    public function employeesBulkQrCode(Request $request)
    {
        $ids = explode(',', $request->get('ids', ''));
        $employees = Employee::whereIn('id', $ids)
            ->where('is_active', true)
            ->orderBy('employee_code')
            ->get();

        if ($employees->isEmpty()) {
            return redirect()->route('admin.employees.index')
                ->with('error', 'ไม่พบพนักงานที่เลือก');
        }

        $qrCodeService = new \App\Services\QrCodeService();
        
        // Generate QR codes for all employees
        $employeeData = $employees->map(function ($employee) use ($qrCodeService) {
            return [
                'employee' => $employee,
                'qrCodeUrl' => $qrCodeService->getQrCodeUrl($employee),
            ];
        });

        return view('admin.employees.qrcode-bulk', compact('employeeData'));
    }

    public function employeesDestroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')
            ->with('success', 'ลบพนักงานเรียบร้อย');
    }

    /**
     * Fare rules management
     */
    public function fareRulesIndex()
    {
        $fareRules = FareRule::with('route')
            ->orderByDesc('effective_from')
            ->paginate(20);
        return view('admin.fare-rules.index', compact('fareRules'));
    }

    public function fareRulesCreate()
    {
        $routes = Route::orderBy('name')->get();
        return view('admin.fare-rules.create', compact('routes'));
    }

    public function fareRulesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,distance_based,special',
            'route_id' => 'nullable|exists:routes,id',
            'base_fare' => 'required_if:type,fixed|nullable|numeric|min:0',
            'calculation_mode' => 'required|in:per_passenger,per_trip,per_km',
            'description' => 'nullable|string',
            'effective_from' => 'required|date',
            'effective_until' => 'nullable|date|after:effective_from',
            'is_active' => 'boolean',
        ]);

        FareRule::create($validated);

        return redirect()->route('admin.fare-rules.index')
            ->with('success', 'สร้างกฎค่าโดยสารเรียบร้อย');
    }

    public function fareRulesEdit(FareRule $fareRule)
    {
        $routes = Route::orderBy('name')->get();
        $distanceBrackets = $fareRule->distanceBrackets;
        return view('admin.fare-rules.edit', compact('fareRule', 'routes', 'distanceBrackets'));
    }

    public function fareRulesUpdate(Request $request, FareRule $fareRule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,distance_based,special',
            'route_id' => 'nullable|exists:routes,id',
            'base_fare' => 'required_if:type,fixed|nullable|numeric|min:0',
            'calculation_mode' => 'required|in:per_passenger,per_trip,per_km',
            'description' => 'nullable|string',
            'effective_from' => 'required|date',
            'effective_until' => 'nullable|date|after:effective_from',
            'is_active' => 'boolean',
        ]);

        $fareRule->update($validated);

        return redirect()->route('admin.fare-rules.index')
            ->with('success', 'แก้ไขกฎค่าโดยสารเรียบร้อย');
    }

    public function fareRulesDestroy(FareRule $fareRule)
    {
        $fareRule->delete();
        return redirect()->route('admin.fare-rules.index')
            ->with('success', 'ลบกฎค่าโดยสารเรียบร้อย');
    }
}
