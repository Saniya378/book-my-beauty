<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Beauty Portal</title>
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
            width: 520px;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px 60px;
            overflow-y: auto;
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
            margin-bottom: 20px;
        }

        .form-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: var(--primary);
            margin-bottom: 6px;
        }

        .form-header p {
            font-size: 13px;
            color: var(--text-gray);
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 5px;
            color: var(--text-dark);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: var(--accent);
        }

        .password-wrapper {
            position: relative;
        }
        .password-wrapper input {
            padding-right: 40px;
        }
        .password-toggle {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
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
        .password-toggle:active { color: var(--primary); transform: translateY(-50%); }
        .password-toggle svg {
            width: 18px;
            height: 18px;
            display: block;
        }
        .password-toggle .icon-hide { display: none; }
        .password-toggle.is-visible .icon-show { display: none; }
        .password-toggle.is-visible .icon-hide { display: block; }

        .steps-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
        }

        .step-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--accent);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: var(--border);
        }

        .step-dot.inactive {
            background: var(--border);
            color: var(--text-gray);
        }

        .form-footer {
            margin-top: 18px;
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
            <div class="logo-mark">Beauty <em>Portal</em></div>
        </a>
        <h2>Join Our Growing<br>Beauty Community.</h2>
        <p>Whether you're looking to book luxurious salon appointments or build and manage your independent beautician
            business, Beauty Portal is designed for you.</p>
    </div>

    <div class="right-panel">


        <div class="steps-indicator">
            <div class="step-dot">1</div>
            <div class="step-line"></div>
            <div class="step-dot inactive">2</div>
        </div>

        <div class="form-header">
            <h3>Create Account</h3>
            <p>Step 1 of 2 — Enter your details and we'll send an OTP to verify your email.</p>
        </div>

        @include('components.alert')

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                    placeholder="e.g. Ayesha Khan">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    placeholder="name@example.com">
            </div>

            <div class="form-group">
                <label for="role">I want to join as</label>
                <select id="role" name="role" required>
                    <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer (Book
                        Appointments)</option>
                    <option value="beautician" {{ old('role') === 'beautician' ? 'selected' : '' }}>Beautician (Offer
                        Services &amp; Manage Bookings)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required placeholder="At least 6 characters">
                    <button type="button" class="password-toggle" data-target="password" aria-label="Show password">
                        <svg class="icon-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="icon-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a20.3 20.3 0 0 1 5.06-6.06M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 8 11 8a20.32 20.32 0 0 1-3.22 4.44M14.12 14.12a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        placeholder="Re-enter password">
                    <button type="button" class="password-toggle" data-target="password_confirmation" aria-label="Show password">
                        <svg class="icon-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="icon-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a20.3 20.3 0 0 1 5.06-6.06M9.9 4.24A10.4 10.4 0 0 1 12 4c7 0 11 8 11 8a20.32 20.32 0 0 1-3.22 4.44M14.12 14.12a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn"
                style="width: 100%; padding: 12px; font-size: 14px; border-radius: 8px; margin-top: 10px;">
                Send OTP &amp; Continue →
            </button>
        </form>

        <div class="form-footer">
            Already have an account? <a href="{{ route('login') }}">Sign In</a>
        </div>
    </div>

    <script>
        document.querySelectorAll('.password-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
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