# API & Integration Documentation

## RESTful Endpoints

### QR Code Scanning (Driver)

#### POST /driver/trip/{trip}/scan
Process a QR code scan for attendance

**Method**: POST  
**Middleware**: auth, verified, role:driver

**Request Body** (JSON):
```json
{
    "qrcode_token": "EMP-xxxxx",
    "latitude": 13.7563,      // optional
    "longitude": 100.5018      // optional
}
```

**Success Response** (200):
```json
{
    "success": true,
    "message": "สแกนสำเร็จ",
    "type": "success",
    "data": {
        "employee_code": "EMP00001",
        "employee_name": "ชื่อพนักงาน",
        "scanned_at": "09:15:30",
        "passenger_count": 15
    }
}
```

**Duplicate Scan Response** (200):
```json
{
    "success": false,
    "message": "ผู้ใช้นี้ได้สแกนในรอบนี้แล้ว",
    "type": "duplicate"
}
```

**Error Response** (400):
```json
{
    "success": false,
    "message": "เกิดข้อผิดพลาด: ...",
    "type": "error"
}
```

---

#### GET /driver/trip/{trip}/records
Get recent attendance records for a trip

**Method**: GET  
**Middleware**: auth, verified, role:driver  
**Query Parameters**: None

**Response** (200):
```json
{
    "records": [
        {
            "id": 1,
            "employee_code": "EMP00001",
            "employee_name": "ชื่อพนักงาน",
            "scanned_at": "09:15:30"
        },
        {
            "id": 2,
            "employee_code": "EMP00002",
            "employee_name": "ชื่ออื่น",
            "scanned_at": "09:16:45"
        }
    ],
    "passenger_count": 15,
    "trip_status": "active"
}
```

---

#### POST /driver/trip/{trip}/cancel-record
Cancel the last attendance record

**Method**: POST  
**Middleware**: auth, verified, role:driver

**Request Body** (Form Data):
```
reason=ยกเลิกจากหน้าจอของคนขับ
```

**Response** (302): Redirect to scan page with success message

---

#### POST /driver/trip/{trip}/complete
Complete/close a trip

**Method**: POST  
**Middleware**: auth, verified, role:driver

**Request Body** (Form Data):
```
notes=ข้อมูลเพิ่มเติม (optional)
```

**Response** (302): Redirect to trip summary page

---

### Trip Management (Driver)

#### POST /driver/trip/start
Start a new trip

**Method**: POST  
**Middleware**: auth, verified, role:driver

**Request Body** (Form Data):
```
vehicle_id=1
route_id=2
```

**Response** (302): Redirect to scan screen

---

### Reports & Analytics (Supervisor)

#### GET /reports/daily
Get daily report for a specific date

**Method**: GET  
**Middleware**: auth, verified, role:supervisor

**Query Parameters**:
- `date` (string, YYYY-MM-DD): Date to report. Default: today

**Response**: HTML (Blade view with data)

---

#### GET /reports/range
Get report for a date range with filters

**Method**: GET  
**Middleware**: auth, verified, role:supervisor

**Query Parameters**:
- `date_from` (string, YYYY-MM-DD): Start date
- `date_to` (string, YYYY-MM-DD): End date
- `vehicle_id` (int, optional): Filter by vehicle
- `route_id` (int, optional): Filter by route
- `driver_id` (int, optional): Filter by driver

**Response**: HTML with filtered summary

---

#### GET /reports/export/daily
Export daily report to Excel

**Method**: GET  
**Middleware**: auth, verified, role:supervisor

**Query Parameters**:
- `date` (string, YYYY-MM-DD): Date to export

**Response**: Excel file download (application/vnd.ms-excel)

---

#### GET /reports/export/daily-pdf
Export daily report to PDF

**Method**: GET  
**Middleware**: auth, verified, role:supervisor

**Query Parameters**:
- `date` (string, YYYY-MM-DD): Date to export

**Response**: PDF file download (application/pdf)

---

#### GET /reports/trip/{trip}
Get detailed trip information

**Method**: GET  
**Middleware**: auth, verified, role:supervisor

**URL Parameters**:
- `trip` (int): Trip ID

**Response**: HTML with trip details, attendance records, and audit log

---

#### GET /reports/calendar
Get calendar view of attendance

**Method**: GET  
**Middleware**: auth, verified, role:supervisor

**Query Parameters**:
- `month` (int, 1-12): Month. Default: current month
- `year` (int): Year. Default: current year

**Response**: HTML with calendar grid

---

## Service Classes

### AttendanceService

```php
use App\Services\AttendanceService;

$service = new AttendanceService();

// Process QR code scan
$result = $service->processQrcodeScan(
    $trip,
    $qrcodeToken,
    ['latitude' => 13.7563, 'longitude' => 100.5018]
);

// Cancel a record
$result = $service->cancelAttendanceRecord($record, 'Reason for cancellation');

// Get trip summary
$summary = $service->getTripSummary($trip);

// Get recent records
$records = $service->getRecentAttendance($trip, $limit = 10);
```

---

### FareCalculationService

```php
use App\Services\FareCalculationService;

$service = new FareCalculationService();

// Calculate fare for a trip
$fareCalc = $service->calculateTripFare($trip);

// Get fare summary with filters
$summary = $service->getfareSummary([
    'date_from' => '2025-11-01',
    'date_to' => '2025-11-30',
    'route_id' => 1,
    'vehicle_id' => 1,
]);

// Get daily summary
$daily = $service->getDailySummary('2025-11-13');
```

---

### QrCodeService

```php
use App\Services\QrCodeService;

$service = new QrCodeService();

// Get or generate QR code URL
$url = $service->getQrCodeUrl($employee);

// Validate QR code token
$isValid = $service->validateQrCodeToken($token);

// Get employee from token
$employee = $service->getEmployeeFromToken($token);

// Regenerate token (for security refresh)
$newToken = $service->regenerateQrCodeToken($employee);
```

---

### TripService

```php
use App\Services\TripService;

$service = new TripService();

// Start a new trip
$trip = $service->startTrip($vehicleId, $routeId, $driverId);

// Complete a trip
$trip = $service->completeTrip($trip, 'Optional notes');

// Cancel a trip
$trip = $service->cancelTrip($trip, 'Cancellation reason');

// Get active trip for vehicle
$trip = $service->getActiveTrip($vehicleId);

// Get today's trips for vehicle
$trips = $service->getTodayTrips($vehicleId);

// Get trips in date range
$trips = $service->getTripsInRange(
    '2025-11-01',
    '2025-11-30',
    ['vehicle_id' => 1, 'status' => 'completed']
);
```

---

## Webhook Events (Future Enhancement)

```php
// Example webhook triggers that could be implemented:
event('tripStarted', $trip);
event('tripCompleted', $trip);
event('attendanceRecorded', $attendance);
event('scanAnomalyDetected', $trip, $employee);
```

---

## Authentication

All endpoints require:
1. User to be authenticated (`auth` middleware)
2. Email to be verified (`verified` middleware)
3. Appropriate role (`role:admin`, `role:driver`, `role:supervisor`, `role:employee`)

### Authentication Methods

**Session/Cookie Authentication** (Default):
- Login through `/login` route
- Laravel session manages authentication state

**CSRF Protection**:
- All POST/PUT/DELETE requests must include `X-CSRF-TOKEN` header or CSRF token in form

---

## Error Handling

### HTTP Status Codes

- `200` OK - Request successful
- `302` Found - Redirect (typically after successful form submission)
- `400` Bad Request - Invalid input or business logic error
- `403` Forbidden - User lacks permission (wrong role)
- `404` Not Found - Resource doesn't exist
- `422` Unprocessable Entity - Validation failed
- `500` Internal Server Error - Server error

### Common Error Responses

**Validation Error** (422):
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "vehicle_id": ["The vehicle field is required."]
    }
}
```

**Authorization Error** (403):
```
Unauthorized access
```

---

## Rate Limiting (Recommended for Production)

To protect against abuse, consider implementing rate limiting:

```php
// In RouteServiceProvider or middleware
Route::middleware('throttle:60,1')->group(function () {
    // API routes
});
```

---

## CORS Configuration (for external APIs)

If building mobile apps or external integrations, enable CORS:

```php
// config/cors.php
'paths' => ['api/*', 'driver/trip/*/scan'],
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
```

---

## Integration Examples

### JavaScript/Fetch API

```javascript
// Scan QR code
async function scanQrCode(tripId, token) {
    const response = await fetch(`/driver/trip/${tripId}/scan`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            qrcode_token: token,
            latitude: null,
            longitude: null
        })
    });

    return response.json();
}

// Get trip records
async function getTripRecords(tripId) {
    const response = await fetch(`/driver/trip/${tripId}/records`);
    return response.json();
}
```

### Laravel HTTP Client

```php
use Illuminate\Support\Facades\Http;

// If calling from another Laravel app
$response = Http::withToken($token)
    ->post('https://attendance.local/driver/trip/1/scan', [
        'qrcode_token' => 'EMP-xxxxx'
    ]);
```

---

## Monitoring & Logging

Key events to monitor:

1. **Duplicate Scans**: Check audit logs for unusual patterns
2. **Trip Duration**: Alert if trip exceeds estimated time by 50%
3. **Passenger Anomalies**: Alert if passenger count exceeds vehicle capacity
4. **System Errors**: Check Laravel logs in `storage/logs/`

---

## Performance Considerations

- **Caching**: Route and location data can be cached for 24 hours
- **Pagination**: Reports paginate at 50 records
- **Indexing**: Database indexes on frequently filtered columns (trip_id, employee_id, started_at)
- **Query Optimization**: Use eager loading with `with()` to prevent N+1 queries

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-11-13 | Initial release |

---

For more information, see SYSTEM_DOCUMENTATION.md
