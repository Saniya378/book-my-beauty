<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Beauty Portal</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--light-bg);
            margin: 0;
            font-family: 'Poppins', sans-serif;
            padding: 20px;
        }
        .card {
            background: white;
            width: 100%;
            max-width: 440px;
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }
        .form-header { text-align: center; margin-bottom: 25px; }
        .form-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 6px;
        }
        .form-header p { font-size: 13px; color: var(--text-gray); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; }
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            outline: none;
        }
        .form-group input:focus { border-color: var(--accent); }
    </style>
</head>
<body>
    <div class="card">
        <div class="form-header">
            <a href="{{ route('home') }}" style="text-decoration: none;">
                <h1 style="font-size: 22px; font-family: 'Playfair Display', serif; color: var(--primary); margin-bottom: 12px;">Beauty <span style="color: var(--accent);">Portal</span></h1>
            </a>
            <h2>Reset Password</h2>
            <p>Enter your registered email and we'll send you a password reset link.</p>
        </div>

        @include('components.alert')

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
            </div>

            <button type="submit" class="btn" style="width: 100%; padding: 12px; font-size: 14px; border-radius: 8px;">Send Reset Link</button>
        </form>

        <div style="margin-top: 25px; text-align: center; font-size: 13px;">
            <a href="{{ route('login') }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">&larr; Back to Login</a>
        </div>
    </div>
</body>
</html>
