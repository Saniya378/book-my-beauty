<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BookMyBeauty</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
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
            background: linear-gradient(rgba(26, 26, 46, 0.92), rgba(15, 52, 96, 0.95)), url('{{ asset('images/image.jpg') }}') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 60px;
            padding-top: 100px;
            color: white;
            position: relative;
        }

        .left-panel .logo-mark {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        .left-panel .logo-mark em {
            color: var(--accent);
            font-style: normal;
        }

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
            position: absolute;
            top: 30px;
            left: 60px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .back-to-browse:hover {
            color: var(--accent);
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .form-header p {
            font-size: 13px;
            color: var(--text-gray);
        }

        .form-group {
            margin-bottom: 20px;
        }

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

        .form-group input:focus {
            border-color: var(--accent);
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 42px;
        }

        .password-toggle {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            background: none;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 0;
            margin: 0;
            line-height: 0;
            color: #9a9a9a;
            display: flex;
            align-items: center;
            justify-content: center;
            -webkit-appearance: none;
            appearance: none;
            box-sizing: border-box;
        }

        .password-toggle:hover,
        .password-toggle:focus,
        .password-toggle:active {
            color: var(--primary);
            transform: translateY(-50%);
        }

        .password-toggle svg {
            width: 19px;
            height: 19px;
            display: block;
        }

        .password-toggle .icon-hide {
            display: none;
        }

        .password-toggle.is-visible .icon-show {
            display: none;
        }

        .password-toggle.is-visible .icon-hide {
            display: block;
        }

        .form-footer {
            margin-top: 25px;
            text-align: center;
            font-size: 13px;
            color: var(--text-gray);
        }

        .form-footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        @media(max-width: 900px) {
            body {
                flex-direction: column;
            }

            .left-panel {
                display: none;
            }

            .right-panel {
                width: 100%;
                min-height: 100vh;
                padding: 40px 25px;
            }
        }
    </style>
</head>

<body>
    <div class="left-panel">
        <a href="{{ route('browse') }}" class="back-to-browse">&larr; Back to Browse</a>
        <a href="{{ route('home') }}" style="text-decoration: none; color: white;">
            <div class="logo-mark">BookMy<em>Beauty</em></div>
        </a>
        <h2>Discover the Finest<br>Beauty Artists Near You.</h2>
        <p>Book verified beauticians, explore portfolios, manage your appointments effortlessly, or get real-time
            assistance from Bella AI.</p>
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
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="name@example.com">
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label for="password" style="margin: 0;">Password</label>
                    <a href="{{ route('password.request') }}"
                        style="font-size: 12px; color: var(--accent); text-decoration: none;">Forgot password?</a>
                </div>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                    <button type="button" class="password-toggle" data-target="password" aria-label="Show password">
                        <svg class="icon-show" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg class="icon-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a20.3 20.3 0 0 1 5.06-6.06M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 8 11 8a20.32 20.32 0 0 1-3.22 4.44M14.12 14.12a3 3 0 1 1-4.24-4.24" />
                            <line x1="1" y1="1" x2="23" y2="23" />
                        </svg>
                    </button>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                <input type="checkbox" id="remember" name="remember"
                    style="accent-color: var(--primary); width: auto;">
                <label for="remember"
                    style="font-size: 13px; color: var(--text-gray); cursor: pointer; margin: 0;">Remember me on this
                    computer</label>
            </div>

            <button type="submit" class="btn"
                style="width: 100%; padding: 12px; font-size: 14px; border-radius: 8px;">Sign In</button>
        </form>

        <div class="form-footer">
            Don't have an account? <a href="{{ route('register') }}">Create an account</a>
        </div>
    </div>

    <script>
        document.querySelectorAll('.password-toggle').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var input = document.getElementById(btn.getAttribute('data-target'));
                var showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                btn.classList.toggle('is-visible', !showing);
                btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            });
        });
    </script>
</body>

</html>
