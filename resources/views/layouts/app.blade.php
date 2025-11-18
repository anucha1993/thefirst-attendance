<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <style>
            :root {
                --primary-color: #667eea;
                --secondary-color: #764ba2;
            }

            body {
                background-color: #f8f9fa;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            .navbar {
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .navbar-brand {
                font-weight: 700;
                font-size: 1.5rem;
            }

            .sidebar {
                background: white;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                min-height: calc(100vh - 70px);
            }

            .sidebar .nav-link {
                color: #555;
                border-left: 3px solid transparent;
                transition: all 0.3s;
            }

            .sidebar .nav-link:hover,
            .sidebar .nav-link.active {
                background-color: #f0f2f5;
                border-left-color: var(--primary-color);
                color: var(--primary-color);
                font-weight: 600;
            }

            .card {
                border: none;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                transition: all 0.3s;
            }

            .card:hover {
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
                border: none;
                transition: all 0.3s;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            }

            .stat-card {
                background: white;
                border-radius: 10px;
                padding: 20px;
                text-align: center;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }

            .stat-card .stat-number {
                font-size: 2.5rem;
                font-weight: 700;
                color: var(--primary-color);
            }

            .stat-card .stat-label {
                color: #7f8c8d;
                font-size: 0.95rem;
                margin-top: 10px;
            }

            .main-content {
                padding: 15px;
            }

            @media (min-width: 768px) {
                .main-content {
                    padding: 30px;
                }
            }

            .page-title {
                font-size: 28px;
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 20px;
            }

            /* Mobile Sidebar Toggle */
            .mobile-menu-toggle {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 1000;
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
                border: none;
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
            }

            @media (min-width: 768px) {
                .mobile-menu-toggle {
                    display: none;
                }
            }

            .breadcrumb {
                background-color: transparent;
                padding: 0;
                margin-bottom: 20px;
            }

            .table {
                background: white;
                border-radius: 8px;
                overflow: hidden;
            }

            .table thead {
                background-color: #f8f9fa;
            }

            .table thead th {
                border: none;
                font-weight: 600;
                color: #2c3e50;
                padding: 15px;
            }

            .table tbody td {
                padding: 15px;
                border: none;
                vertical-align: middle;
            }

            .table tbody tr {
                border-bottom: 1px solid #ecf0f1;
            }

            .table tbody tr:hover {
                background-color: #f8f9fa;
            }
        </style>
    </head>
    <body>
        @include('layouts.navigation')

        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar Navigation - Hidden on Mobile -->
                <nav class="col-md-2 d-none d-md-block sidebar">
                    @include('layouts.sidebar')
                </nav>

                <!-- Main Content -->
                <div class="col-12 col-md-10 ms-sm-auto main-content">
                    <!-- Page Heading -->
                    @isset($header)
                        <header class="mb-4">
                            <h1 class="page-title">{{ $header }}</h1>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <main>
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Toggle (Floating Button) -->
        <button class="mobile-menu-toggle d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Mobile Sidebar (Offcanvas) -->
        <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileSidebar">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title"><i class="fas fa-bars me-2"></i>เมนู</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body p-0">
                @include('layouts.sidebar')
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        @vite(['resources/js/app.js'])
        
        @yield('scripts')
    </body>
</html>
