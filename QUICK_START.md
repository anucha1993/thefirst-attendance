# Quick Start Guide

## 5-Minute Setup

### Step 1: Clone/Extract Project
```bash
cd d:\Programing\thefirst-attendance
```

### Step 2: Install Dependencies
```bash
composer install
npm install
```

### Step 3: Configure Environment
```bash
copy .env.example .env
php artisan key:generate
```

Edit `.env` and set database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=attendance_system
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4: Setup Database
```bash
# Create database
mysql -u root -e "CREATE DATABASE attendance_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations and seed
php artisan migrate --seed
```

### Step 5: Build Frontend
```bash
npm run build
```

### Step 6: Start Server
```bash
php artisan serve
```

Visit: **http://localhost:8000**

---

## Default Credentials

| Role | Email | Password |
|------|-------|----------|
| 🔐 Admin | admin@attendance.local | password |
| 🚗 Driver | driver1@attendance.local | password |
| 📊 Supervisor | supervisor@attendance.local | password |
| 👤 Employee | employee1@attendance.local | password |

---

## First Steps After Setup

### 1. Admin - Setup System
1. Login as admin
2. Go to "จุดรับ–ส่ง" and add locations
3. Go to "สายรถ" and create routes
4. Add vehicles and assign drivers
5. Register employees and view their QR codes

### 2. Driver - Test Trip
1. Login as driver
2. Click "เริ่มรอบใหม่"
3. Select vehicle and route
4. Start trip
5. Scan employee QR codes or enter tokens manually

### 3. Supervisor - View Reports
1. Login as supervisor
2. Click "รายงานรายวัน"
3. Select date and view trip summaries
4. Export to Excel/PDF

### 4. Employee - View QR Code
1. Login as employee
2. Click "QR Code ของฉัน"
3. Print or display on mobile

---

## System Architecture

```
┌─────────────────────────────────────────────────────┐
│              ATTENDANCE & FARE SYSTEM                │
├─────────────────────────────────────────────────────┤
│                                                       │
│  Frontend: Bootstrap 5 + Blade Templates             │
│  ├─ Admin Panel (Setup & Configuration)              │
│  ├─ Driver Portal (QR Scanning)                      │
│  ├─ Supervisor Dashboard (Reports & Analytics)      │
│  └─ Employee Portal (History & QR Code)              │
│                                                       │
├─────────────────────────────────────────────────────┤
│                                                       │
│  Application Layer (Laravel 11)                      │
│  ├─ Controllers: AdminController, DriverController  │
│  ├─ Services: AttendanceService, FareService        │
│  ├─ Models: Employee, Trip, AttendanceRecord        │
│  └─ Middleware: Role-based access control           │
│                                                       │
├─────────────────────────────────────────────────────┤
│                                                       │
│  Database Layer (MySQL)                              │
│  ├─ Locations, Routes, Vehicles                      │
│  ├─ Employees, Trips, AttendanceRecords             │
│  ├─ FareRules, FareCalculations                     │
│  └─ AttendanceAudits (Compliance)                   │
│                                                       │
└─────────────────────────────────────────────────────┘
```

---

## Key Features Overview

### 🔐 Role-Based Access
- **Admin**: Full system control
- **Driver**: Scan QR codes, manage trips
- **Supervisor**: View reports, export data
- **Employee**: View attendance history

### 📱 QR Code Scanning
- Real-time camera scanning (ZXing library)
- Manual token input fallback
- Prevents duplicate scans automatically

### 💰 Flexible Fare Calculation
- **Fixed**: ฿X per passenger
- **Distance-based**: Varies by km
- **Custom**: Special rules per route

### 📊 Comprehensive Reporting
- Daily summaries by vehicle, route, driver
- Calendar view with daily statistics
- Export to Excel/PDF
- Audit trail of all changes

### 📈 Real-time Monitoring
- Live passenger count during trips
- AJAX-based updates
- Responsive mobile-friendly UI

---

## Database Tables Overview

```
Locations          Route start/end points (หอพัก, โรงงาน)
    ├─ Routes     Connections (สาย A-1, สาย B-1)
    ├─ Vehicles   Shuttles (รถ)
    ├─ Employees  Staff with QR codes
    │   └─ AttendanceRecords (per trip scan)
    │       └─ AttendanceAudits (who changed what)
    └─ FareRules  Pricing rules
        ├─ DistanceFareBrackets
        └─ FareCalculations
```

---

## Common Tasks

### Add New Employee
```
Admin → พนักงาน → เพิ่มพนักงานใหม่ → ดูQR Code → พิมพ์
```

### Start a Trip
```
Driver → เริ่มรอบใหม่ → เลือกรถและสาย → เริ่มรอบ → สแกนQR Codes
```

### View Daily Report
```
Supervisor → รายงานรายวัน → เลือกวัน → ดูสรุป → Export PDF
```

### Check Attendance History
```
Employee → ประวัติ → ดูรายการขึ้นรถ
```

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Can't login | Check email/password match seeded data |
| Camera not working | Allow browser camera permission |
| QR code won't scan | Ensure code is clear, check employee is active |
| Missing data in report | Verify trip is completed (not just started) |
| Page won't load | Clear browser cache, check Laravel logs |

---

## Development Tips

### Watch Mode (CSS/JS changes)
```bash
npm run dev
```

### Interactive Shell
```bash
php artisan tinker
>>> $emp = App\Models\Employee::first();
>>> $emp->qrcode_token
```

### Database Reset
```bash
php artisan migrate:fresh --seed
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## Production Deployment Checklist

- [ ] Copy `.env.example` to `.env` with production credentials
- [ ] Set `APP_DEBUG=false` and `APP_ENV=production`
- [ ] Run `php artisan key:generate`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Optimize: `php artisan optimize`
- [ ] Set proper file permissions
- [ ] Configure SSL/HTTPS
- [ ] Setup automated backups
- [ ] Configure mail service (.env MAIL_* variables)
- [ ] Set up monitoring/logging

---

## Support Resources

1. **Documentation**: See `SYSTEM_DOCUMENTATION.md`
2. **API Reference**: See `API_DOCUMENTATION.md`
3. **Laravel Docs**: https://laravel.com/docs
4. **Bootstrap Docs**: https://getbootstrap.com/docs
5. **ZXing Library**: https://github.com/zxing-js/library

---

## Next Steps

1. ✅ Setup complete!
2. 🎯 Login and explore each role
3. 🛠️ Customize for your needs
4. 📱 Test on mobile devices
5. 🚀 Deploy to production

---

**System Ready!** 🎉

Access the application at: **http://localhost:8000**
