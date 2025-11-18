<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 450px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-icon {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 15px;
        }

        .login-container h1 {
            color: #2c3e50;
            margin-bottom: 5px;
            font-weight: 700;
            font-size: 28px;
        }

        .login-container .subtitle {
            color: #7f8c8d;
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 0.95rem;
            display: block;
        }

        .form-control {
            border: 2px solid #ecf0f1;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: #667eea;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #bdc3c7;
        }

        .form-check {
            margin-top: 15px;
            margin-bottom: 25px;
        }

        .form-check-input {
            border: 2px solid #ecf0f1;
            border-radius: 5px;
            accent-color: #667eea;
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-label {
            color: #555;
            font-size: 0.95rem;
            margin-left: 8px;
            user-select: none;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 13px 30px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s ease;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
            color: white;
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .forgot-password {
            text-decoration: none;
            color: #667eea;
            font-size: 0.95rem;
            transition: color 0.3s ease;
            font-weight: 500;
        }

        .forgot-password:hover {
            color: #5568d3;
            text-decoration: underline;
        }

        .login-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
            gap: 15px;
        }

        .divider {
            color: #bdc3c7;
            font-size: 0.9rem;
        }

        .error-alert {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.95rem;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-alert ul {
            margin: 0;
            padding-left: 20px;
        }

        .error-alert li {
            margin: 5px 0;
        }

        .success-alert {
            background-color: #efe;
            border: 1px solid #cfc;
            color: #3c3;
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s ease;
            font-weight: 500;
        }

        .back-link a:hover {
            color: #5568d3;
            text-decoration: underline;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #bdc3c7;
            pointer-events: none;
        }

        .input-icon input {
            padding-right: 40px;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .login-container {
                padding: 40px 25px;
            }

            .login-container h1 {
                font-size: 24px;
            }

            .logo-icon {
                font-size: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="fas fa-bus"></i>
                </div>
                <h1>Attendance System</h1>
                <p class="subtitle">Employee Boarding Management</p>
            </div>

            {{ $slot }}

            <div class="back-link">
                <a href="{{ route('welcome') }}"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

