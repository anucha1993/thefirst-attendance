<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 15px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
            animation: slideDown 0.6s ease-out;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .logo-icon i {
            font-size: 28px;
            color: #667eea;
        }

        .logo-title {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .logo-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }

        .login-card {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        .login-card-body {
            padding: 30px 25px;
        }

        .login-heading {
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
            text-align: center;
        }

        .login-subheading {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-group-text {
            background: transparent;
            border: 2px solid #e0e6ed;
            color: #667eea;
            border-right: none;
            font-size: 16px;
        }

        .form-control {
            border: 2px solid #e0e6ed;
            border-left: none;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: none;
            background-color: #f8f9ff;
        }

        .form-control::placeholder {
            color: #bdc3c7;
        }

        .form-check {
            margin-bottom: 20px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #e0e6ed;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-label {
            font-size: 13px;
            color: #555;
            cursor: pointer;
            margin-left: 5px;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 13px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: center;
            margin-top: 18px;
        }

        .forgot-password a {
            color: #667eea;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .demo-section {
            margin-top: 25px;
            padding: 15px;
            background: #f0f4ff;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .demo-title {
            font-size: 12px;
            font-weight: 700;
            color: #667eea;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .demo-item {
            font-size: 13px;
            color: #2c3e50;
            margin-bottom: 5px;
            font-family: 'Courier New', monospace;
        }

        .demo-item:last-child {
            margin-bottom: 0;
        }

        .alert-error {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            border-left: 4px solid #c33;
        }

        .alert-success {
            background: #efe;
            color: #3c3;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            border-left: 4px solid #3c3;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .login-container {
                max-width: 100%;
            }

            .login-card-body {
                padding: 25px 20px;
            }

            .logo-title {
                font-size: 24px;
            }

            .login-heading {
                font-size: 20px;
            }

            body {
                padding: 10px;
            }
        }

        .error-messages {
            color: #c33;
            font-size: 12px;
            margin-top: 6px;
            display: block;
        }

        .is-invalid {
            border-color: #c33 !important;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-bus"></i>
            </div>
            <div class="logo-title">Attendance System</div>
            <div class="logo-subtitle">Employee Shuttle Bus Management</div>
        </div>

        <!-- Login Card -->
        <div class="login-card">
            <div class="login-card-body">
                <h2 class="login-heading">
                    <i class="fas fa-sign-in-alt"></i> Login
                </h2>
                <p class="login-subheading">Sign in to your account</p>

                <!-- Session Status -->
                @if ($status = session('status'))
                    <div class="alert-success">
                        {{ $status }}
                    </div>
                @endif

                <!-- Errors -->
                @if ($errors->any())
                    <div class="alert-error">
                        <strong><i class="fas fa-exclamation-circle"></i> Login Failed!</strong>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                name="email" 
                                value="{{ old('email') }}" 
                                placeholder="your@email.com"
                                required 
                                autofocus
                            >
                        </div>
                        @error('email')
                            <span class="error-messages">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input 
                                type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                name="password" 
                                placeholder="••••••••"
                                required
                            >
                        </div>
                        @error('password')
                            <span class="error-messages">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            id="remember_me" 
                            name="remember"
                        >
                        <label class="form-check-label" for="remember_me">
                            Remember me on this device
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Log In
                    </button>

                    <!-- Forgot Password -->
                    @if (Route::has('password.request'))
                        <div class="forgot-password">
                            <a href="{{ route('password.request') }}">
                                <i class="fas fa-question-circle"></i> Forgot your password?
                            </a>
                        </div>
                    @endif
                </form>

                <!-- Demo Credentials -->
                <div class="demo-section">
                    <div class="demo-title">
                        <i class="fas fa-info-circle"></i> Demo Accounts
                    </div>
                    <div class="demo-item">
                        <strong>Admin:</strong> admin@attendance.local
                    </div>
                    <div class="demo-item">
                        <strong>Pass:</strong> password
                    </div>
                    <div class="demo-item" style="margin-top: 10px; border-top: 1px solid rgba(102,126,234,0.2); padding-top: 10px;">
                        <strong>Driver:</strong> driver1@attendance.local
                    </div>
                    <div class="demo-item">
                        <strong>Supervisor:</strong> supervisor@attendance.local
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

