<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Beauty Portal</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background: var(--primary);
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }
        .left-panel {
            flex: 1;
            background: linear-gradient(rgba(26,26,46,0.92), rgba(15,52,96,0.95)), url('{{ asset('images/image.jpg') }}') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            color: white;
        }
        .left-panel .logo-mark {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }
        .left-panel .logo-mark em { color: var(--accent); font-style: normal; }
        .left-panel h2 {
            font-family: 'Playfair Display', serif;
            font-size: 34px;
            line-height: 1.3;
            margin-bottom: 15px;
        }
        .left-panel p {
            font-size: 14px;
            opacity: 0.8;
            max-width: 450px;
            line-height: 1.6;
        }
        .right-panel {
            width: 480px;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
        }
        .back-to-browse {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--text-gray);
            text-decoration: none;
            margin-bottom: 25px;
            width: fit-content;
        }
        .back-to-browse:hover { color: var(--accent); }
        .form-header { margin-bottom: 30px; }
        .form-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: var(--primary);
            margin-bottom: 8px;
        }
        .form-header p { font-size: 13px; color: var(--text-gray); }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--text-dark);
        }
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus { border-color: var(--accent); }
        .form-footer {
            margin-top: 25px;
            text-align: center;
            font-size: 13px;
            color: var(--text-gray);
        }
        .form-footer a { color: var(--accent); text-decoration: none; font-weight: 600; }
        @media(max-width: 900px) {
            body { flex-direction: column; }
            .left-panel { display: none; }
            .right-panel { width: 100%; min-height: 100vh; padding: 40px 25px; }
        }
    </style>
</head>
<body>
    <div class="left-panel">
          <a href="{{ route('browse') }}" class="back-to-browse">&larr; Back to Browse</a>
        <a href="{{ route('home') }}" style="text-decoration: none; color: white;">
            <div class="logo-mark">Beauty <em>Portal</em></div>
        </a>
        <h2>Discover the Finest<br>Beauty Artists Near You.</h2>
        <p>Book verified beauticians, explore portfolios, manage your appointments effortlessly, or get real-time assistance from Bella AI.</p>
    </div>

    <div class="right-panel">
      

        <div class="form-header">
            <h3>Welcome Back</h3>
            <p>Log in to access your appointments and favorites.</p>
        </div>

        @include('components.alert')

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com">
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label for="password" style="margin: 0;">Password</label>
                    <a href="{{ route('password.request') }}" style="font-size: 12px; color: var(--accent); text-decoration: none;">Forgot password?</a>
                </div>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>

            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                <input type="checkbox" id="remember" name="remember" style="accent-color: var(--primary); width: auto;">
                <label for="remember" style="font-size: 13px; color: var(--text-gray); cursor: pointer; margin: 0;">Remember me on this computer</label>
            </div>

            <button type="submit" class="btn" style="width: 100%; padding: 12px; font-size: 14px; border-radius: 8px;">Sign In</button>
        </form>

        <div class="form-footer">
            Don't have an account? <a href="{{ route('register') }}">Create an account</a>
        </div>
    </div>
</body>
</html>