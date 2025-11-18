<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\AttendanceRecord;
use App\Models\AttendanceAudit;
use App\Services\FareCalculationService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $fareCalcService;

    public function __construct(FareCalculationService $fareCalcService)
    {
        $this->fareCalcService = $fareCalcService;
    }

    /**
     * Daily report
     */
    public function daily(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        $summary = $this->fareCalcService->getDailySummary($date);

        $trips = Trip::whereDate('started_at', $date)
            ->with(['vehicle', 'route', 'driver', 'attendanceRecords.employee'])
            ->orderBy('started_at')
            ->get();

        return view('reports.daily', compact('date', 'summary', 'trips'));
    }

    /**
     * Range report (date range with filters)
     */
    public function range(Request $request)
    {
        $dateFrom = $request->input('date_from', today()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', today()->toDateString());

        $filters = [
            'vehicle_id' => $request->input('vehicle_id'),
            'route_id' => $request->input('route_id'),
            'driver_id' => $request->input('driver_id'),
        ];

        $summary = $this->fareCalcService->getfareSummary(array_merge([
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ], $filters));

        $vehicles = \App\Models\Vehicle::all();
        $routes = \App\Models\Route::all();
        $drivers = \App\Models\User::where('role', 'driver')->get();

        return view('reports.range', compact(
            'dateFrom',
            'dateTo',
            'summary',
            'filters',
            'vehicles',
            'routes',
            'drivers'
        ));
    }

    /**
     * Calendar view
     */
    public function calendar(Request $request)
    {
        $month = $request->input('month', today()->month);
        $year = $request->input('year', today()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->clone()->endOfMonth();

        $trips = Trip::whereBetween('started_at', [$startDate, $endDate])
            ->selectRaw('DATE(started_at) as date, COUNT(*) as trip_count, SUM(passenger_count) as passenger_count, SUM(total_fare) as total_fare')
            ->groupByRaw('DATE(started_at)')
            ->get()
            ->keyBy('date');

        $calendar = [];
        $current = $startDate->clone();

        while ($current->lte($endDate)) {
            $dateStr = $current->toDateString();
            $calendar[] = [
                'date' => $dateStr,
                'day' => $current->day,
                'week_day' => $current->englishDayOfWeek,
                'data' => $trips[$dateStr] ?? null,
            ];
            $current->addDay();
        }

        return view('reports.calendar', compact('calendar', 'month', 'year'));
    }

    /**
     * Audit log report
     */
    public function auditLog(Request $request)
    {
        $dateFrom = $request->input('date_from', today()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', today()->toDateString());

        $auditLogs = AttendanceAudit::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['trip', 'attendanceRecord', 'user'])
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('reports.audit-log', compact('auditLogs', 'dateFrom', 'dateTo'));
    }

    /**
     * Trip details report
     */
    public function tripDetails(Trip $trip)
    {
        $records = $trip->attendanceRecords()
            ->with('employee')
            ->orderBy('scanned_at')
            ->get();

        $auditLogs = AttendanceAudit::where('trip_id', $trip->id)
            ->with(['user', 'attendanceRecord'])
            ->orderByDesc('created_at')
            ->get();

        return view('reports.trip-details', compact('trip', 'records', 'auditLogs'));
    }

    /**
     * Export daily report to Excel
     */
    public function exportDailyToExcel(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DailyReportExport($date),
            'daily-report-' . $date . '.xlsx'
        );
    }

    /**
     * Export trip details to Excel
     */
    public function exportTripToExcel(Trip $trip)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TripDetailsExport($trip),
            'trip-' . $trip->id . '-' . $trip->started_at->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export range report to Excel
     */
    public function exportRangeToExcel(Request $request)
    {
        $dateFrom = $request->input('date_from', today()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', today()->toDateString());

        $filters = [
            'vehicle_id' => $request->input('vehicle_id'),
            'route_id' => $request->input('route_id'),
            'driver_id' => $request->input('driver_id'),
        ];

        return \Excel::download(
            new \App\Exports\RangeReportExport($dateFrom, $dateTo, $filters),
            'range-report-' . $dateFrom . '-to-' . $dateTo . '.xlsx'
        );
    }

    /**
     * Export daily report to PDF
     */
    public function exportDailyToPdf(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        $summary = $this->fareCalcService->getDailySummary($date);

        $pdf = \PDF::loadView('reports.daily-pdf', compact('date', 'summary'));
        return $pdf->download('daily-report-' . $date . '.pdf');
    }

    /**
     * Employee attendance history
     */
    public function employeeHistory(Request $request)
    {
        $employee_id = $request->input('employee_id');
        $dateFrom = $request->input('date_from', today()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', today()->toDateString());

        $employees = \App\Models\Employee::all();

        $records = AttendanceRecord::query();

        if ($employee_id) {
            $records->where('employee_id', $employee_id);
        }

        $records = $records->whereBetween('scanned_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->with(['employee', 'trip.vehicle', 'trip.route', 'trip.driver'])
            ->orderByDesc('scanned_at')
            ->paginate(50);

        return view('reports.employee-history', compact('records', 'employees', 'employee_id', 'dateFrom', 'dateTo'));
    }
}
