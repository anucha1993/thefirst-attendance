# Attendance & Shuttle Bus Fare System

## Architecture Overview

This is a complete Laravel-based attendance management system with QR code scanning for employee shuttle bus operations. The system supports role-based access control with four main user roles: Admin, Driver, Supervisor, and Employee.

### Key Architecture Components

#### Database Layer
- **Locations**: Pickup points and dropoff locations
- **Routes**: Shuttle routes connecting locations with distance and estimated time
- **Vehicles**: Shuttle vehicles with capacity
- **VehicleDrivers**: Many-to-many relationship between vehicles and drivers
- **Employees**: Employee data with unique QR code tokens
- **Trips**: Individual shuttle runs with real-time tracking
- **AttendanceRecords**: Employee scan records for each trip
- **FareRules**: Flexible fare calculation rules (fixed, distance-based, special)
- **FareCalculations**: Computed fares for trips
- **AttendanceAudits**: Comprehensive audit trail of all changes

#### Application Structure

```
app/
├── Models/
│   ├── User.php                    # Base user model with role support
│   ├── Location.php
│   ├── Route.php
│   ├── Vehicle.php
│   ├── VehicleDriver.php
│   ├── Employee.php
│   ├── Trip.php
│   ├── AttendanceRecord.php
│   ├── AttendanceAudit.php
│   ├── FareRule.php
│   ├── DistanceFareBracket.php
│   └── FareCalculation.php
├── Services/
│   ├── AttendanceService.php       # QR scan processing
│   ├── FareCalculationService.php  # Fare computation
│   ├── QrCodeService.php           # QR code generation
│   └── TripService.php             # Trip management
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php     # System setup
│   │   ├── DriverController.php    # QR scanning & trips
│   │   └── ReportController.php    # Reports & analytics
│   └── Middleware/
│       └── CheckRole.php           # Role-based access control
```

## Installation & Setup

### Prerequisites
- PHP 8.1+
- Laravel 11+
- MySQL 8.0+
- Composer
- Node.js & npm (for frontend assets)

### Step 1: Install Dependencies

```bash
cd d:\Programing\thefirst-attendance
composer install
npm install
```

### Step 2: Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Configure `.env`:
```env
APP_NAME="Transportation System"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=attendance_system
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log
```

### Step 3: Database Setup

```bash
# Create database
mysql -u root -e "CREATE DATABASE attendance_system;"

# Run migrations
php artisan migrate

# Seed with test data
php artisan db:seed
```

### Step 4: Install QR Code Library

```bash
composer require simplesoftwareio/simple-qrcode
```

### Step 5: Build Frontend Assets

```bash
npm run build
# or for development with watch
npm run dev
```

### Step 6: Create Storage Symlink

```bash
php artisan storage:link
```

### Step 7: Start Development Server

```bash
php artisan serve
```

Access the application at: `http://localhost:8000`

## Default Login Credentials

After running seeders, use these credentials:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@attendance.local | password |
| Driver 1 | driver1@attendance.local | password |
| Driver 2 | driver2@attendance.local | password |
| Supervisor | supervisor@attendance.local | password |
| Employee | employee1@attendance.local | password |

## User Roles & Permissions

### 1. Admin
- Setup system configuration
- Manage locations (pickup/dropoff points)
- Create and manage shuttle routes
- Register vehicles and assign drivers
- Register employees and generate QR codes
- Configure fare rules and pricing
- View all system data

**Routes**: `/admin/*`

### 2. Driver
- View assigned vehicles
- Start new trips/rounds
- Scan employee QR codes during trip
- View real-time passenger count
- Cancel/undo mistaken scans
- Close trips when completed
- View today's trip summary

**Routes**: `/driver/*`

### 3. Supervisor/HR/Accounting
- View daily reports
- Filter reports by date range, vehicle, route, driver
- View detailed trip information with passenger lists
- Calculate fares automatically
- Export reports to Excel/PDF
- View calendar with daily statistics
- Access audit logs of all changes
- View employee attendance history

**Routes**: `/reports/*`

### 4. Employee
- View their QR code (print or display on mobile)
- View attendance history
- See their boarding records

**Routes**: `/employee/*`

## Key Features

### 1. QR Code Generation & Scanning

**Generation** (`QrCodeService.php`):
```php
$qrCodeService = new QrCodeService();
$qrCodeUrl = $qrCodeService->getQrCodeUrl($employee);
```

**Scanning**: Uses ZXing library for browser-based QR code reading
- Real-time camera access
- Manual token input fallback
- Prevents duplicate scans in same trip

### 2. Attendance Management

**Processing Scan**:
```php
$result = $attendanceService->processQrcodeScan(
    $trip,
    $qrcodeToken,
    ['latitude' => null, 'longitude' => null]
);
```

**Duplicate Prevention**:
- Database unique constraint on `(trip_id, employee_id)`
- UI validation before submission
- Audit trail of all attempts

### 3. Fare Calculation

**Modes Supported**:

1. **Fixed Fare**
   ```php
   // Per passenger per trip
   Price = number_of_passengers × fare_per_passenger
   ```

2. **Distance-Based**
   ```php
   // Based on route distance
   0-5 km: 15฿, 5-10 km: 25฿, >10 km: 35฿
   ```

3. **Custom/Special Rules** (extensible)

**Usage**:
```php
$fareCalcService = new FareCalculationService();
$fareCalc = $fareCalcService->calculateTripFare($trip);
$summary = $fareCalcService->getDailySummary('2025-11-13');
```

### 4. Trip Management

**Workflow**:
1. Driver selects vehicle and route
2. Starts trip → creates Trip record with status='active'
3. Employees board and scan QR codes
4. Driver can cancel mistaken scans with reason
5. Driver completes trip → calculates fare, status='completed'

### 5. Comprehensive Reporting

- Daily trip summaries
- Multi-day range reports with filters
- Calendar view with daily statistics
- Detailed trip information with passenger lists
- Audit log of all modifications
- Employee attendance history
- Export to Excel/PDF

### 6. Audit Trail

All critical actions are logged:
- Employee scanning
- Scan cancellations (with reason)
- Trip start/completion
- Manual data modifications

**Schema**: `attendance_audits` table stores:
- Action (created, deleted, cancelled, manually_added, manually_removed)
- Reason/notes
- Old and new data (JSON)
- Timestamp and user

## API Endpoints (AJAX)

### Process QR Code Scan
```
POST /driver/trip/{trip}/scan
Content-Type: application/json

{
    "qrcode_token": "EMP-xxxxx",
    "latitude": 13.7563,
    "longitude": 100.5018
}

Response:
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

### Get Recent Records
```
GET /driver/trip/{trip}/records

Response:
{
    "records": [...],
    "passenger_count": 15,
    "trip_status": "active"
}
```

## Database Schema Highlights

### Unique Constraints
- `employees`: `qrcode_token` (unique per employee)
- `attendance_records`: `(trip_id, employee_id)` (prevent duplicate scans)
- `vehicle_drivers`: `(vehicle_id, driver_id, assigned_from)` (prevent duplicate assignments)

### Indexes
- `trips`: `(vehicle_id, route_id)`, `(driver_id, started_at)`, `status`
- `attendance_records`: `(trip_id, scanned_at)`, `(employee_id, scanned_at)`
- `attendance_audits`: Supports efficient audit log queries

### Soft Deletes
Applied to: `users`, `locations`, `routes`, `vehicles`, `employees`, `trips`, `attendance_records`, `fare_rules`

## Security Considerations

1. **Role-Based Access Control**
   - Middleware validates user role for every protected route
   - Controllers verify user ownership of resources

2. **CSRF Protection**
   - All forms include CSRF tokens
   - API endpoints verify tokens

3. **Mass Assignment Protection**
   - Models use `$fillable` to explicitly allow attributes

4. **Audit Logging**
   - All modifications tracked in database
   - Supports compliance and fraud detection

5. **Unique Constraints**
   - Database prevents duplicate attendance records
   - QR code tokens are globally unique

## Testing

### Create Sample Data
```bash
php artisan migrate:fresh --seed
```

### Run Unit Tests
```bash
php artisan test
```

### Test QR Code Generation
```bash
# In Laravel Tinker
php artisan tinker
>>> $emp = App\Models\Employee::first();
>>> $qrService = new App\Services\QrCodeService();
>>> $url = $qrService->getQrCodeUrl($emp);
```

## Frontend Technologies

- **Bootstrap 5**: UI Framework
- **Bootstrap Icons**: Icon library
- **ZXing (js)**: Browser-based QR code scanning
- **AJAX**: Real-time updates without page reload

## Customization Guide

### Adding New Fare Rule Types

1. Add new type in `fare_rules.type` enum
2. Implement calculation logic in `FareRule::calculateFare()`
3. Create migration for specific fields if needed

### Adding New Reports

1. Create controller method in `ReportController`
2. Query data from trips/attendance records
3. Create Blade view for display
4. Add route in `web.php`

### Customizing Email Notifications

1. Create mailable class: `php artisan make:mail TripCompleted`
2. Dispatch in appropriate controller
3. Configure mail settings in `.env`

## Performance Optimization Tips

1. **Use Eager Loading**
   ```php
   Trip::with(['vehicle', 'route', 'driver', 'attendanceRecords'])->get()
   ```

2. **Implement Pagination**
   ```php
   Trip::paginate(50);
   ```

3. **Cache Frequently Accessed Data**
   ```php
   Cache::remember('routes', 3600, fn() => Route::all())
   ```

4. **Add Database Indexes** (already in migrations)

## Troubleshooting

### Camera Not Working
- Check browser permissions for camera access
- Ensure HTTPS in production (required by ZXing)
- Test in Chrome, Firefox, or Safari

### QR Code Not Scanning
- Ensure QR code is clear and well-lit
- Check that employee is active
- Verify trip is in 'active' status

### Duplicate Scan Error
- Database already has record for this employee in trip
- Use cancel function to remove mistaken scan
- Refresh page if stuck in error state

### Report Not Showing Data
- Verify trips exist and are completed
- Check date range in filters
- Ensure fare rules are configured

## File Locations Quick Reference

| Component | Location |
|-----------|----------|
| Models | `app/Models/*.php` |
| Controllers | `app/Http/Controllers/*.php` |
| Services | `app/Services/*.php` |
| Views | `resources/views/` |
| Routes | `routes/web.php` |
| Migrations | `database/migrations/` |
| Seeders | `database/seeders/` |
| Middleware | `app/Http/Middleware/` |

## Support & Development

For issues or enhancements:
1. Check this documentation
2. Review migration files for schema details
3. Check model relationships
4. Review service classes for business logic
5. Test with provided seeder data

---

**System Version**: 1.0.0  
**Last Updated**: November 13, 2025  
**Laravel Version**: 11.x
