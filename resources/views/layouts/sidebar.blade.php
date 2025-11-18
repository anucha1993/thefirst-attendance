<style>
    .sidebar {
        background-color: #f8f9fa;
        border-right: 1px solid #dee2e6;
        min-height: calc(100vh - 56px);
        padding: 20px 0;
    }

    .sidebar .nav-link {
        color: #495057;
        padding: 12px 20px;
        border-left: 3px solid transparent;
        margin-bottom: 5px;
        transition: all 0.3s ease;
    }

    .sidebar .nav-link:hover {
        color: #667eea;
        background-color: #e9ecef;
        border-left-color: #667eea;
    }

    .sidebar .nav-link.active {
        color: #667eea;
        background-color: #e9ecef;
        border-left-color: #667eea;
        font-weight: 600;
    }

    .sidebar .nav-link i {
        width: 20px;
        margin-right: 10px;
        text-align: center;
    }

    .sidebar-section-title {
        padding: 15px 20px 10px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
        letter-spacing: 0.5px;
    }
</style>

@auth
    <div class="sidebar">
        @if(auth()->user()->role === 'admin')
            <!-- Admin Menu -->
            <div class="sidebar-section-title">Management</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="{{ route('admin.locations.index') }}" class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt"></i> Locations
            </a>
            <a href="{{ route('admin.routes.index') }}" class="nav-link {{ request()->routeIs('admin.routes.*') ? 'active' : '' }}">
                <i class="fas fa-road"></i> Routes
            </a>
            <a href="{{ route('admin.vehicles.index') }}" class="nav-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}">
                <i class="fas fa-bus"></i> Vehicles
            </a>
            <a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Employees
            </a>

            <div class="sidebar-section-title">Configuration</div>
            <a href="{{ route('admin.fare-rules.index') }}" class="nav-link {{ request()->routeIs('admin.fare-rules.*') ? 'active' : '' }}">
                <i class="fas fa-coins"></i> Fare Rules
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i> Users
            </a>

            <div class="sidebar-section-title">Reports</div>
            <a href="{{ route('reports.daily') }}" class="nav-link {{ request()->routeIs('reports.daily') ? 'active' : '' }}">
                <i class="fas fa-calendar-day"></i> Daily Report
            </a>
            <a href="{{ route('reports.range') }}" class="nav-link {{ request()->routeIs('reports.range') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Range Report
            </a>
            <a href="{{ route('reports.audit-log') }}" class="nav-link {{ request()->routeIs('reports.audit-log') ? 'active' : '' }}">
                <i class="fas fa-history"></i> Audit Log
            </a>

        @elseif(auth()->user()->role === 'driver')
            <!-- Driver Menu -->
            <div class="sidebar-section-title">Operations</div>
            <a href="{{ route('driver.dashboard') }}" class="nav-link {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="{{ route('driver.trip.start') }}" class="nav-link {{ request()->routeIs('driver.trip.start') ? 'active' : '' }}">
                <i class="fas fa-play-circle"></i> Start Trip
            </a>
            <a href="{{ route('driver.today-trips') }}" class="nav-link {{ request()->routeIs('driver.today-trips') ? 'active' : '' }}">
                <i class="fas fa-list"></i> Today's Trips
            </a>

        @elseif(auth()->user()->role === 'supervisor')
            <!-- Supervisor Menu -->
            <div class="sidebar-section-title">Reports</div>
            <a href="{{ route('reports.daily') }}" class="nav-link {{ request()->routeIs('reports.daily') ? 'active' : '' }}">
                <i class="fas fa-calendar-day"></i> Daily Report
            </a>
            <a href="{{ route('reports.range') }}" class="nav-link {{ request()->routeIs('reports.range') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Range Report
            </a>
            <a href="{{ route('reports.calendar') }}" class="nav-link {{ request()->routeIs('reports.calendar') ? 'active' : '' }}">
                <i class="fas fa-calendar"></i> Calendar Report
            </a>

            <div class="sidebar-section-title">Audit</div>
            <a href="{{ route('reports.audit-log') }}" class="nav-link {{ request()->routeIs('reports.audit-log') ? 'active' : '' }}">
                <i class="fas fa-history"></i> Audit Log
            </a>
            <a href="{{ route('reports.employee-history') }}" class="nav-link {{ request()->routeIs('reports.employee-history') ? 'active' : '' }}">
                <i class="fas fa-user-clock"></i> Employee History
            </a>

        @else
            <!-- Employee Menu -->
            <div class="sidebar-section-title">Personal</div>
            <a href="{{ route('employee.dashboard') }}" class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="{{ route('employee.qrcode') }}" class="nav-link {{ request()->routeIs('employee.qrcode') ? 'active' : '' }}">
                <i class="fas fa-qrcode"></i> QR Code
            </a>
            <a href="{{ route('employee.attendance-history') }}" class="nav-link {{ request()->routeIs('employee.attendance-history') ? 'active' : '' }}">
                <i class="fas fa-history"></i> Attendance History
            </a>
        @endif
    </div>
@else
    <div class="sidebar"></div>
@endauth
