<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - BookMyBeauty</title>
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
            align-items: center;
            justify-content: center;
        }

        .otp-card {
            background: white;
            border-radius: 16px;
            padding: 50px 60px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.18);
            text-align: center;
        }

        .otp-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #c9a96e, #e0c78a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
        }

        .otp-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .otp-card p {
            font-size: 13.5px;
            color: var(--text-gray);
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .otp-card p .phone-num {
            color: var(--primary);
            font-weight: 600;
        }

        /* OTP Input Boxes */
        .otp-input-row {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 24px;
        }

        .otp-digit {
            width: 50px;
            height: 58px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            color: var(--primary);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            caret-color: var(--accent);
            font-family: 'Poppins', sans-serif;
        }

        .otp-digit:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(201, 169, 110, 0.18);
        }

        .otp-digit.filled {
            border-color: var(--accent);
            background: rgba(201, 169, 110, 0.06);
        }

        .timer-text {
            font-size: 12.5px;
            color: var(--text-gray);
            margin-bottom: 20px;
        }

        .timer-text #countdown {
            font-weight: 700;
            color: var(--primary);
        }

        .timer-text #countdown.expired {
            color: #e74c3c;
        }

        .btn-otp-verify {
            width: 100%;
            padding: 13px;
            background: var(--primary);
            color: var(--accent);
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            letter-spacing: 0.5px;
            transition: background 0.2s, transform 0.1s;
            margin-bottom: 14px;
        }

        .btn-otp-verify:hover {
            background: #0f3460;
            transform: translateY(-1px);
        }

        .btn-otp-verify:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .resend-row {
            font-size: 13px;
            color: var(--text-gray);
        }

        .resend-row button {
            background: none;
            border: none;
            color: var(--accent);
            font-weight: 600;
            cursor: pointer;
            font-size: 13px;
            font-family: inherit;
            padding: 0;
            text-decoration: underline;
        }

        .resend-row button:disabled {
            color: #aaa;
            cursor: not-allowed;
            text-decoration: none;
        }

        .back-link {
            display: block;
            margin-top: 18px;
            font-size: 12.5px;
            color: var(--text-gray);
            text-decoration: none;
        }

        .back-link:hover {
            color: var(--accent);
        }

        .steps-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            margin-bottom: 28px;
        }

        .step-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        .step-dot.done {
            background: #2ecc71;
            color: white;
        }

        .step-dot.active {
            background: var(--accent);
            color: var(--primary);
        }

        .step-line {
            flex: 0 0 40px;
            height: 2px;
            background: var(--accent);
        }

        .dev-alert {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12.5px;
            color: #856404;
            text-align: left;
            margin-bottom: 18px;
            line-height: 1.5;
        }

        @media(max-width: 500px) {
            .otp-card {
                padding: 35px 20px;
            }

            .otp-digit {
                width: 42px;
                height: 50px;
                font-size: 18px;
            }
        }
    </style>
</head>

<body>

    <div class="otp-card">
        <div class="steps-indicator">
            <div class="step-dot done">✓</div>
            <div class="step-line"></div>
            <div class="step-dot active">2</div>
        </div>

        <div class="otp-icon">📧</div>

        <h3>Verify Your Email</h3>

        <p>
            We've sent a 6-digit OTP to your email address<br>
            @if (session('pending_email'))
                <span class="phone-num">{{ session('pending_email') }}</span><br>
            @endif
            Enter the code below to activate your account.
        </p>

        @if (session('dev_otp_message'))
            <div class="dev-alert">⚠️ {!! session('dev_otp_message') !!}</div>
        @endif

        @if ($errors->any())
            <div
                style="background:#fdf0f0; border:1px solid #e74c3c; border-radius:8px; padding:10px 14px; color:#c0392b; font-size:13px; margin-bottom:16px; text-align:left;">
                {{ $errors->first() }}
            </div>
        @endif
        @if (session('error'))
            <div
                style="background:#fdf0f0; border:1px solid #e74c3c; border-radius:8px; padding:10px 14px; color:#c0392b; font-size:13px; margin-bottom:16px; text-align:left;">
                {{ session('error') }}
            </div>
        @endif
        @if (session('success'))
            <div
                style="background:#f0fdf4; border:1px solid #2ecc71; border-radius:8px; padding:10px 14px; color:#155724; font-size:13px; margin-bottom:16px; text-align:left;">
                {{ session('success') }}
            </div>
        @endif

        <form id="otpForm" action="{{ route('register.otp.verify') }}" method="POST">
            @csrf
            <input type="hidden" name="otp" id="hiddenOtp">

            <div class="otp-input-row">
                <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                    autocomplete="one-time-code" id="d1">
                <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                    id="d2">
                <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                    id="d3">
                <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                    id="d4">
                <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                    id="d5">
                <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                    id="d6">
            </div>

            <div class="timer-text">
                Code expires in <span id="countdown">10:00</span>
            </div>

            <button type="submit" class="btn-otp-verify" id="verifyBtn">
                Verify &amp; Create Account
            </button>
        </form>

        <div class="resend-row">
            Didn't receive the code?&nbsp;
            <form action="{{ route('register.otp.resend') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" id="resendBtn">Resend OTP</button>
            </form>
        </div>

        <a href="{{ route('register') }}" class="back-link">← Change Details / Start Over</a>
    </div>

    <script>
        // ── OTP digit inputs: auto-focus next, handle backspace ──
        const digits = ['d1', 'd2', 'd3', 'd4', 'd5', 'd6'].map(id => document.getElementById(id));
        const hidden = document.getElementById('hiddenOtp');
        const verifyBtn = document.getElementById('verifyBtn');

        digits.forEach((input, idx) => {
            input.addEventListener('input', (e) => {
                // Only allow digits
                input.value = input.value.replace(/\D/g, '');
                if (input.value && idx < 5) digits[idx + 1].focus();
                input.classList.toggle('filled', input.value.length > 0);
                syncHidden();
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && idx > 0) {
                    digits[idx - 1].focus();
                    digits[idx - 1].value = '';
                    digits[idx - 1].classList.remove('filled');
                    syncHidden();
                }
            });
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                pasted.split('').forEach((ch, i) => {
                    if (digits[i]) {
                        digits[i].value = ch;
                        digits[i].classList.add('filled');
                    }
                });
                const next = Math.min(pasted.length, 5);
                digits[next].focus();
                syncHidden();
            });
        });

        function syncHidden() {
            hidden.value = digits.map(d => d.value).join('');
        }

        // Auto-submit when all 6 digits entered
        function checkAutoSubmit() {
            if (digits.every(d => d.value.length === 1)) {
                setTimeout(() => document.getElementById('otpForm').submit(), 200);
            }
        }
        digits.forEach(d => d.addEventListener('input', checkAutoSubmit));

        // ── Countdown timer (10 minutes) ──
        const countdownEl = document.getElementById('countdown');
        let timeLeft = 600; // 10 minutes in seconds

        const timer = setInterval(() => {
            timeLeft--;
            if (timeLeft <= 0) {
                clearInterval(timer);
                countdownEl.textContent = 'Expired';
                countdownEl.classList.add('expired');
                verifyBtn.disabled = true;
                verifyBtn.textContent = 'OTP Expired — Please Resend';
                return;
            }
            const m = Math.floor(timeLeft / 60);
            const s = timeLeft % 60;
            countdownEl.textContent = `${m}:${s.toString().padStart(2, '0')}`;
        }, 1000);

        // Focus first digit on load
        digits[0].focus();
    </script>

</body>

</html>
