<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Root - redirect to login or dashboard based on auth status
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'driver' => redirect()->route('driver.dashboard'),
            'supervisor' => redirect()->route('reports.daily'),
            default => redirect()->route('employee.dashboard'),
        };
    }
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============ Admin Routes ============
Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Locations management
        Route::get('/locations', [AdminController::class, 'locationsIndex'])->name('locations.index');
        Route::get('/locations/create', [AdminController::class, 'locationsCreate'])->name('locations.create');
        Route::post('/locations', [AdminController::class, 'locationsStore'])->name('locations.store');
        Route::get('/locations/{location}/edit', [AdminController::class, 'locationsEdit'])->name('locations.edit');
        Route::put('/locations/{location}', [AdminController::class, 'locationsUpdate'])->name('locations.update');
        Route::delete('/locations/{location}', [AdminController::class, 'locationsDestroy'])->name('locations.destroy');

        // Routes management
        Route::get('/routes', [AdminController::class, 'routesIndex'])->name('routes.index');
        Route::get('/routes/create', [AdminController::class, 'routesCreate'])->name('routes.create');
        Route::post('/routes', [AdminController::class, 'routesStore'])->name('routes.store');
        Route::get('/routes/{route}/edit', [AdminController::class, 'routesEdit'])->name('routes.edit');
        Route::put('/routes/{route}', [AdminController::class, 'routesUpdate'])->name('routes.update');
        Route::delete('/routes/{route}', [AdminController::class, 'routesDestroy'])->name('routes.destroy');

        // Vehicles management
        Route::get('/vehicles', [AdminController::class, 'vehiclesIndex'])->name('vehicles.index');
        Route::get('/vehicles/create', [AdminController::class, 'vehiclesCreate'])->name('vehicles.create');
        Route::post('/vehicles', [AdminController::class, 'vehiclesStore'])->name('vehicles.store');
        Route::get('/vehicles/{vehicle}/edit', [AdminController::class, 'vehiclesEdit'])->name('vehicles.edit');
        Route::put('/vehicles/{vehicle}', [AdminController::class, 'vehiclesUpdate'])->name('vehicles.update');
        Route::delete('/vehicles/{vehicle}', [AdminController::class, 'vehiclesDestroy'])->name('vehicles.destroy');

        // Employees management
        Route::get('/employees', [AdminController::class, 'employeesIndex'])->name('employees.index');
        Route::get('/employees/create', [AdminController::class, 'employeesCreate'])->name('employees.create');
        Route::post('/employees', [AdminController::class, 'employeesStore'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [AdminController::class, 'employeesEdit'])->name('employees.edit');
        Route::put('/employees/{employee}', [AdminController::class, 'employeesUpdate'])->name('employees.update');
        Route::get('/employees/{employee}/qrcode', [AdminController::class, 'employeesShowQrCode'])->name('employees.qrcode');
        Route::get('/employees/qrcode/bulk', [AdminController::class, 'employeesBulkQrCode'])->name('employees.qrcode-bulk');
        Route::delete('/employees/{employee}', [AdminController::class, 'employeesDestroy'])->name('employees.destroy');

        // Fare rules management
        Route::get('/fare-rules', [AdminController::class, 'fareRulesIndex'])->name('fare-rules.index');
        Route::get('/fare-rules/create', [AdminController::class, 'fareRulesCreate'])->name('fare-rules.create');
        Route::post('/fare-rules', [AdminController::class, 'fareRulesStore'])->name('fare-rules.store');
        Route::get('/fare-rules/{fareRule}/edit', [AdminController::class, 'fareRulesEdit'])->name('fare-rules.edit');
        Route::put('/fare-rules/{fareRule}', [AdminController::class, 'fareRulesUpdate'])->name('fare-rules.update');
        Route::delete('/fare-rules/{fareRule}', [AdminController::class, 'fareRulesDestroy'])->name('fare-rules.destroy');

        // Users management
        Route::get('/users', [AdminController::class, 'usersIndex'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'usersCreate'])->name('users.create');
        Route::post('/users', [AdminController::class, 'usersStore'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'usersEdit'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'usersUpdate'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'usersDestroy'])->name('users.destroy');
    });

    // ============ Driver Routes ============
    Route::middleware(['role:driver'])->prefix('driver')->name('driver.')->group(function () {
        Route::get('/dashboard', [DriverController::class, 'dashboard'])->name('dashboard');
        Route::get('/trip/start', [DriverController::class, 'startTripForm'])->name('trip.start-form');
        Route::post('/trip/start', [DriverController::class, 'startTrip'])->name('trip.start');
        Route::get('/trip/{trip}/scan', [DriverController::class, 'scanScreen'])->name('trip.scan');
        Route::post('/trip/{trip}/scan', [DriverController::class, 'processQrcodeScan'])->name('trip.scan-process');
        Route::get('/trip/{trip}/records', [DriverController::class, 'getRecentRecords'])->name('trip.recent-records');
        Route::post('/trip/{trip}/cancel-record', [DriverController::class, 'cancelLastRecord'])->name('trip.cancel-record');
        Route::post('/trip/{trip}/complete', [DriverController::class, 'completeTrip'])->name('trip.complete');
        Route::get('/trip/{trip}/summary', [DriverController::class, 'tripSummary'])->name('trip-summary');
        Route::get('/today-trips', [DriverController::class, 'todayTrips'])->name('today-trips');
    });

    // ============ Reports & Supervisor Routes ============
    Route::middleware(['role:supervisor,admin'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily', [ReportController::class, 'daily'])->name('daily');
        Route::get('/range', [ReportController::class, 'range'])->name('range');
        Route::get('/calendar', [ReportController::class, 'calendar'])->name('calendar');
        Route::get('/audit-log', [ReportController::class, 'auditLog'])->name('audit-log');
        Route::get('/trip/{trip}', [ReportController::class, 'tripDetails'])->name('trip-details');
        Route::get('/employee-history', [ReportController::class, 'employeeHistory'])->name('employee-history');

        // Export endpoints
        Route::get('/export/daily', [ReportController::class, 'exportDailyToExcel'])->name('export-daily-excel');
        Route::get('/export/range', [ReportController::class, 'exportRangeToExcel'])->name('export-range-excel');
        Route::get('/export/daily-pdf', [ReportController::class, 'exportDailyToPdf'])->name('export-daily-pdf');
        Route::get('/export/trip/{trip}', [ReportController::class, 'exportTripToExcel'])->name('export-trip-excel');
    });

    // ============ Employee Routes ============
    Route::middleware(['role:employee'])->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', function () {
            $employee = auth()->user()->employee;
            $attendanceRecords = $employee->attendanceRecords()
                ->with(['trip.vehicle', 'trip.route', 'trip.driver'])
                ->orderByDesc('scanned_at')
                ->paginate(20);

            return view('employee.dashboard', compact('attendanceRecords'));
        })->name('dashboard');

        Route::get('/qrcode', function () {
            $employee = auth()->user()->employee;
            $qrCodeService = new \App\Services\QrCodeService();
            $qrCodeUrl = $qrCodeService->getQrCodeUrl($employee);

            return view('employee.qrcode', compact('employee', 'qrCodeUrl'));
        })->name('qrcode');

        Route::get('/attendance-history', function () {
            $employee = auth()->user()->employee;
            $attendanceRecords = $employee->attendanceRecords()
                ->with(['trip.vehicle', 'trip.route', 'trip.driver'])
                ->orderByDesc('scanned_at')
                ->paginate(50);

            return view('employee.attendance-history', compact('attendanceRecords'));
        })->name('attendance-history');
    });
});

require __DIR__.'/auth.php';
