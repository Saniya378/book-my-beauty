<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - BookMyBeauty</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
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

        .form-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .form-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 6px;
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
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            outline: none;
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
    </style>
</head>

<body>
    <div class="card">
        <div class="form-header">
            <a href="{{ route('home') }}" style="text-decoration: none;">
                <h1
                    style="font-size: 22px; font-family: 'Playfair Display', serif; color: var(--primary); margin-bottom: 12px;">
                    Beauty <span style="color: var(--accent);">Portal</span></h1>
            </a>
            <h2>Create New Password</h2>
            <p>Enter a secure password for {{ $email }}</p>
        </div>

        @include('components.alert')

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="password">New Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required autofocus
                        placeholder="At least 6 characters">
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

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        placeholder="Re-enter password">
                    <button type="button" class="password-toggle" data-target="password_confirmation"
                        aria-label="Show password">
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

            <button type="submit" class="btn"
                style="width: 100%; padding: 12px; font-size: 14px; border-radius: 8px;">Reset Password</button>
        </form>

        <div style="margin-top: 25px; text-align: center; font-size: 13px;">
            <a href="{{ route('login') }}"
                style="color: var(--primary); text-decoration: none; font-weight: 500;">&larr; Back to Login</a>
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
