# Attendance & Shuttle Bus Fare System using Employee QRCode

A complete, production-ready Laravel LTS web application for managing employee shuttle bus boarding with QR code scanning, automated fare calculation, and comprehensive reporting.

## ✨ Features

### Core Functionality
- **QR Code Attendance Tracking**: Real-time employee boarding via unique QR codes
- **Duplicate Scan Prevention**: Unique constraint prevents same employee boarding twice on same trip
- **Smart Fare Calculation**: Flexible system supporting fixed, distance-based, and special pricing modes
- **Comprehensive Reporting**: Daily, range, calendar, and employee history reports with export to Excel/PDF
- **Audit Trail**: Complete compliance audit log of all attendance changes with user tracking
- **Role-Based Access**: Four user roles (Admin, Driver, Supervisor, Employee) with granular permissions

### Business Features
- **Multi-Location Support**: Define pickup and dropoff locations with coordinates
- **Route Management**: Create routes between locations with distance calculations
- **Vehicle Fleet**: Register vehicles with capacity tracking
- **Driver Assignment**: Assign multiple drivers to vehicles with date ranges
- **Employee Management**: Centralized employee database with automatic QR generation
- **Real-Time Scanning**: Live passenger count updates during trip
- **Trip Management**: Complete trip lifecycle (start → scan → complete)
- **Fare Calculation Modes**:
  - Fixed fare per passenger
  - Distance-based with tiered pricing brackets
  - Special pricing for specific routes
- **Daily Summaries**: Automated trip totals, passenger counts, and fare summaries

### Reporting
- **Daily Reports**: All trips for a specific date with statistics
- **Range Reports**: Multi-day analysis with vehicle/route/driver filters
- **Calendar View**: Monthly calendar with daily trip statistics
- **Audit Log**: Full compliance trail of attendance modifications
- **Employee History**: Individual boarding records with trip details
- **Exports**: Excel and PDF export for all reports

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- MySQL 8.0+ / MariaDB 10.6+
- Composer
- Node.js 18+ (for Vite assets)
- Web server (Apache/Nginx)

### Installation (5 Minutes)

1. **Clone and Setup**
```bash
cd d:\Programing\thefirst-attendance
composer install
npm install
cp .env.example .env
php artisan key:generate
```

2. **Database Configuration**
```bash
# Edit .env file:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=thefirst_attendance
DB_USERNAME=root
DB_PASSWORD=
```

3. **Run Migrations & Seed**
```bash
php artisan migrate
php artisan db:seed
```

4. **Build Assets & Start**
```bash
npm run build
php artisan serve
```

5. **Login with Default Credentials**
- **Admin**: admin@example.com | password
- **Driver**: driver@example.com | password
- **Supervisor**: supervisor@example.com | password
- **Employee**: emp00001@example.com | password

Visit http://localhost:8000 after login.

## 🏗️ Architecture

### Tech Stack
- **Framework**: Laravel 11 LTS
- **Frontend**: Blade Templates + Bootstrap 5
- **Database**: MySQL 8.0+
- **QR Scanning**: ZXing.js (browser) + simplesoftwareio/simple-qrcode (PHP)
- **Asset Pipeline**: Vite
- **Testing**: PHPUnit

### Directory Structure
```
app/
├── Http/Controllers/
│   ├── AdminController.php       # CRUD for locations, routes, vehicles, employees, fare rules
│   ├── DriverController.php      # Trip management, QR scanning, trip summaries
│   └── ReportController.php      # Reports, audit logs, exports
├── Models/
│   ├── User.php                  # Role-based user model
│   ├── Location.php              # Pickup/dropoff locations
│   ├── Route.php                 # Routes connecting locations
│   ├── Vehicle.php               # Shuttle buses
│   ├── VehicleDriver.php         # Driver assignments (pivot)
│   ├── Employee.php              # Employee records with QR codes
│   ├── Trip.php                  # Individual shuttle runs
│   ├── AttendanceRecord.php      # Boarding records
│   ├── AttendanceAudit.php       # Audit trail
│   ├── FareRule.php              # Pricing rules
│   ├── DistanceFareBracket.php   # Distance-based pricing tiers
│   └── FareCalculation.php       # Computed fares
├── Services/
│   ├── AttendanceService.php     # QR scan processing, attendance management
│   ├── FareCalculationService.php # Fare computation and summaries
│   ├── QrCodeService.php         # QR generation, validation, token management
│   └── TripService.php           # Trip lifecycle management
├── Http/Middleware/
│   └── CheckRole.php             # Role-based authorization
└── Exports/
    ├── DailyReportExport.php     # Excel export for daily reports
    └── RangeReportExport.php     # Excel export for range reports

routes/
├── web.php                        # All application routes organized by role

database/
├── migrations/                    # 11 table migrations
├── seeders/
│   └── DatabaseSeeder.php        # Test data: 50 employees, 4 locations, 3 routes, 2 vehicles, 3 fare rules
└── factories/
    ├── UserFactory.php
    ├── EmployeeFactory.php
    └── TripFactory.php

resources/views/
├── layouts/app.blade.php         # Main layout with sidebar navigation
├── admin/
│   ├── dashboard.blade.php       # Admin dashboard with statistics
│   ├── locations/                # CRUD views for locations
│   ├── routes/                   # CRUD views for routes
│   ├── vehicles/                 # CRUD views for vehicles
│   ├── employees/                # CRUD views for employees + QR display
│   └── fare-rules/               # CRUD views for fare rules
├── driver/
│   ├── dashboard.blade.php       # Driver dashboard
│   ├── trip/
│   │   ├── start-form.blade.php  # Trip initialization form
│   │   ├── scan.blade.php        # Real-time QR scanning interface
│   │   └── summary.blade.php     # Trip completion summary
│   └── today-trips.blade.php     # Today's trips list
├── reports/
│   ├── daily.blade.php           # Daily report with statistics
│   ├── range.blade.php           # Range report with filters
│   ├── calendar.blade.php        # Monthly calendar view
│   ├── audit-log.blade.php       # Compliance audit trail
│   ├── trip-details.blade.php    # Individual trip details
│   └── employee-history.blade.php # Employee boarding history
└── employee/
    ├── dashboard.blade.php       # Employee dashboard
    ├── qrcode.blade.php          # Employee's QR code display
    └── attendance-history.blade.php # Personal boarding history

tests/Feature/
└── AttendanceFeatureTest.php     # 6 test scenarios covering core business logic

docs/
├── SYSTEM_DOCUMENTATION.md       # Complete technical guide
├── API_DOCUMENTATION.md          # Service classes and endpoints
├── QUICK_START.md                # Setup and basic usage guide
└── IMPLEMENTATION_SUMMARY.md     # Deliverables overview
```

## 📋 Database Schema

### Core Tables (11 Total)

**Locations** - Physical boarding/dropoff points
- `id`, `name`, `type` (pickup/dropoff), `latitude`, `longitude`, `created_at`

**Routes** - Connections between locations
- `id`, `pickup_location_id`, `dropoff_location_id`, `distance_km`, `estimated_duration_minutes`

**Vehicles** - Shuttle buses
- `id`, `license_plate`, `model`, `capacity`, `status`, `created_at`

**VehicleDrivers** - Driver assignments (many-to-many)
- `id`, `vehicle_id`, `driver_id`, `assigned_from`, `assigned_until`, `is_primary`, `created_at`

**Employees** - Employee records
- `id`, `user_id`, `employee_code`, `first_name`, `last_name`, `department`, `position`, `qrcode_token` (UNIQUE), `qrcode_data`, `is_active`, `created_at`

**Trips** - Individual shuttle runs
- `id`, `vehicle_id`, `route_id`, `driver_id`, `started_at`, `ended_at`, `status` (active/completed/cancelled), `passenger_count`, `total_fare`, `notes`, `created_at`

**AttendanceRecords** - Employee boarding (UNIQUE on trip_id + employee_id)
- `id`, `trip_id`, `employee_id`, `scanned_at`, `latitude`, `longitude`, `deleted_at` (soft delete), `created_at`

**AttendanceAudits** - Compliance audit trail
- `id`, `trip_id`, `attendance_record_id`, `user_id`, `action`, `reason`, `old_data` (JSON), `new_data` (JSON), `created_at`

**FareRules** - Pricing configurations
- `id`, `type` (fixed/distance_based/special), `route_id`, `base_fare`, `effective_from`, `effective_until`, `created_at`

**DistanceFareBrackets** - Distance-based pricing tiers
- `id`, `fare_rule_id`, `distance_from_km`, `distance_to_km`, `fare_per_km`, `created_at`

**FareCalculations** - Computed trip fares
- `id`, `trip_id`, `fare_rule_id`, `passenger_count`, `unit_fare`, `total_fare`, `calculation_details` (JSON), `created_at`

## 👥 User Roles & Workflows

### Admin
- Manage locations, routes, vehicles, employees
- View all system reports
- Manage fare rules and pricing
- Access audit logs

### Driver
- View assigned vehicles and routes
- Start and complete trips
- Real-time QR code scanning with ZXing.js
- View trip summaries and daily reports

### Supervisor
- Access comprehensive reporting dashboard
- Daily, range, and calendar reports
- Employee boarding history
- Audit trail review
- Export reports to Excel/PDF

### Employee
- View personal QR code (display on phone or print)
- Track personal boarding history
- View upcoming scheduled trips

## 🔄 Main Workflows

### Employee Boarding Process
1. Driver starts trip (vehicle + route selected)
2. Scanning interface opens with camera access
3. Driver scans employee QR code (or enters token manually)
4. System checks:
   - Employee exists and is active
   - Not already boarded in this trip (duplicate prevention)
   - Geolocation matches route area (if enabled)
5. Attendance record created, passenger count incremented
6. Real-time list updates with new boarding
7. Driver completes trip → fare calculated automatically

### Fare Calculation Workflow
1. Trip completed
2. FareCalculationService finds applicable FareRule
3. Based on rule type:
   - **Fixed**: fare_per_passenger × passenger_count
   - **Distance-based**: Bracket lookup by trip distance → fare_per_km × passenger_count
   - **Special**: Custom logic for specific routes
4. Result stored in FareCalculation table
5. Trip.total_fare updated

### Reporting Workflow
1. Supervisor selects date range and optional filters
2. System queries trips + attendance records
3. Aggregates by vehicle, route, driver, or custom grouping
4. Generates statistics: total passengers, total fare, trip count
5. Export to Excel or PDF on demand

## 🔐 Security Features

- **Role-Based Authorization**: CheckRole middleware validates user permissions on all routes
- **QR Token Uniqueness**: Database unique constraint on `(employee_id, qrcode_token)` prevents duplicates
- **Duplicate Scan Prevention**: Application-level check + database unique constraint on `(trip_id, employee_id)`
- **Audit Trail**: All attendance modifications logged with user, action, timestamp, and reason
- **Soft Deletes**: Records marked for deletion rather than permanently removed
- **Geolocation Tracking**: Latitude/longitude stored with each scan (optional validation)
- **SQL Injection Prevention**: All queries use Eloquent ORM with parameterized statements
- **CSRF Protection**: Token validation on all POST/PUT/DELETE requests

## 📊 Key Business Rules

1. **Duplicate Scan Prevention**: Same employee cannot board same trip twice
2. **Active Employee Only**: Inactive employees cannot be scanned
3. **Single QR Per Employee**: Each employee has unique, regenerable QR token
4. **Automatic Passenger Count**: Increments on each successful scan, decrements on cancellation
5. **Automatic Fare Calculation**: Computed when trip completes based on applicable rule
6. **Audit Trail**: All attendance changes tracked with user and reason
7. **Soft Delete Preservation**: Deleted records remain in database for audit purposes

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=AttendanceFeatureTest

# Run with coverage
php artisan test --coverage
```

**Test Coverage** (6 feature tests):
- Prevent duplicate scan in same trip
- QR token uniqueness validation
- Driver authorization checks
- Inactive employee handling
- Trip workflow validation
- Trip summary retrieval

## 📖 Documentation

See detailed documentation in the `docs/` folder:

- **[QUICK_START.md](docs/QUICK_START.md)** - 5-minute setup and first steps
- **[SYSTEM_DOCUMENTATION.md](docs/SYSTEM_DOCUMENTATION.md)** - Complete technical guide
- **[API_DOCUMENTATION.md](docs/API_DOCUMENTATION.md)** - Service classes and endpoints
- **[IMPLEMENTATION_SUMMARY.md](docs/IMPLEMENTATION_SUMMARY.md)** - Deliverables overview

## 🛠️ Development Commands

```bash
# Start development server
php artisan serve

# Build assets (production)
npm run build

# Watch assets (development)
npm run dev

# Run migrations
php artisan migrate

# Seed database with test data
php artisan db:seed

# Clear all caches
php artisan cache:clear

# Generate QR codes (if needed)
php artisan storage:link

# Tinker - interactive shell
php artisan tinker
```

## 🚢 Deployment

### Prerequisites
- Production PHP 8.2+ server
- MySQL 8.0+ database
- HTTPS certificate
- Environment configuration

### Steps
1. Clone repository to production server
2. Copy `.env.example` to `.env`
3. Update database credentials and APP_URL
4. Run `composer install --no-dev`
5. Run `php artisan migrate --force`
6. Run `npm run build`
7. Set proper file permissions:
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data .
   ```
8. Enable OPcache and other PHP production extensions
9. Configure web server (Apache/Nginx)
10. Set up HTTPS and SSL certificate

## 📱 Mobile Considerations

- QR Code Scanning: Works on iOS/Android with modern browsers
- Employee QR Display: Optimized for mobile phone screens (print-friendly)
- Responsive Design: All views built with Bootstrap 5 responsive grid
- Touch-Friendly: Buttons and controls sized for finger interaction

## 🔧 Customization

### Change Default Fare
Edit `database/seeders/DatabaseSeeder.php` and modify `FareRule::create()` calls.

### Add Custom Pricing Rule
1. Create new FareRule with `type='special'`
2. Override `FareRule::calculateFare()` method
3. Dispatch to custom calculation method

### Modify QR Code Format
1. Edit `QrCodeService::generateQrCodeData()` to change token format
2. Update `AttendanceService::processQrcodeScan()` if validation logic changes
3. Regenerate all employee QR codes with `regenerateQrCodeToken()`

### Extend Reporting
1. Add method to `ReportController`
2. Create corresponding Blade view
3. Add route to `routes/web.php`

## 🐛 Troubleshooting

**Q: QR Camera not working**
- Ensure HTTPS or localhost (browser security)
- Allow camera permission in browser settings
- Try manual token input as fallback

**Q: Duplicate scan still allowed**
- Verify migration ran: `php artisan migrate:status`
- Clear database cache: `php artisan cache:clear`

**Q: Fare not calculated**
- Check FareRule effective dates
- Verify trip route has active FareRule
- Check FareCalculation table for computation result

**Q: Reports show no data**
- Ensure migrations completed: `php artisan migrate`
- Seed test data: `php artisan db:seed`
- Verify trip status is 'completed' (active trips excluded)

## 📝 License

Proprietary software. All rights reserved.

## 🤝 Support

For questions or issues, contact the development team.

---

**System Version**: 1.0.0  
**Last Updated**: 2024  
**Status**: Production Ready
