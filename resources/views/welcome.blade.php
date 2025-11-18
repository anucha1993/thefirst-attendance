<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance & Shuttle Bus System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .welcome-container {
            background: white;
            border-radius: 15px;
            padding: 60px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            text-align: center;
        }
        .welcome-container h1 {
            color: #333;
            margin-bottom: 20px;
            font-weight: 700;
        }
        .welcome-container p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        .btn-login, .btn-register {
            padding: 12px 30px;
            font-size: 1rem;
            border-radius: 8px;
            font-weight: 600;
            margin: 10px;
            transition: all 0.3s;
        }
        .btn-login {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
        }
        .btn-login:hover {
            background-color: #5568d3;
            border-color: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-register {
            background-color: #f093fb;
            border-color: #f093fb;
            color: white;
        }
        .btn-register:hover {
            background-color: #e080e8;
            border-color: #e080e8;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(240, 147, 251, 0.4);
            color: white;
        }
        .feature-list {
            text-align: left;
            margin: 30px 0;
            color: #555;
        }
        .feature-list li {
            margin: 10px 0;
            padding-left: 30px;
            position: relative;
        }
        .feature-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h1>🚌 Attendance & Shuttle Bus System</h1>
        <p>Real-time employee boarding with QR codes and automated fare calculation</p>
        
        <ul class="feature-list">
            <li>QR Code Attendance Tracking</li>
            <li>Duplicate Scan Prevention</li>
            <li>Smart Fare Calculation</li>
            <li>Comprehensive Reporting</li>
            <li>Role-Based Access Control</li>
        </ul>

        <div class="mt-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-lg btn-login">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-lg btn-login">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-lg btn-register">Register</a>
                    @endif
                @endauth
            @endif
        </div>

        <hr class="my-4">
        <p style="font-size: 0.9rem; color: #999;">
            <strong>Demo Credentials:</strong><br>
            Admin: admin@example.com | Password: password<br>
            Driver: driver@example.com | Password: password<br>
            Supervisor: supervisor@example.com | Password: password<br>
            Employee: emp00001@example.com | Password: password
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
