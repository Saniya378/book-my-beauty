@extends('layouts.auth')

@section('title', 'Verify Your Email - BookMyBeauty')

@section('media-class', 'auth__media--register')

@section('headline', 'One quick step before you start booking')

@section('intro', 'We send a six digit code to your email so every account on BookMyBeauty belongs to a real person.')

@section('points')
    <li>
        <x-icon name="check" :size="16" />
        The code expires after 10 minutes
    </li>
    <li>
        <x-icon name="check" :size="16" />
        Resend it as often as you need
    </li>
    <li>
        <x-icon name="check" :size="16" />
        Your details are never shared with beauticians
    </li>
@endsection

@section('form')
    <div class="steps">
        <div class="steps__dot steps__dot--done"><x-icon name="check" :size="14" /></div>
        <div class="steps__line steps__line--done"></div>
        <div class="steps__dot">2</div>
    </div>

    <div class="icon-tile" style="margin-bottom: 14px;"><x-icon name="mail" :size="20" /></div>

    <div class="page-head__title">Verify your email</div>
    <p class="auth__sub" style="margin-bottom: 4px;">
        Enter the 6 digit code we sent to
        @if (session('pending_email'))
            <strong style="color: var(--primary);">{{ session('pending_email') }}</strong>
        @endif
    </p>

    @include('components.alert')

    @if (session('dev_otp_message'))
        <div class="notice notice--warn" style="margin-bottom: 16px;">
            <span class="notice__icon"><x-icon name="warning" :size="17" /></span>
            <span>{!! session('dev_otp_message') !!}</span>
        </div>
    @endif

    <form id="otpForm" action="{{ route('register.otp.verify') }}" method="POST">
        @csrf
        <input type="hidden" name="otp" id="hiddenOtp">

        <div class="otp">
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code" id="d1">
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d2">
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d3">
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d4">
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d5">
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="d6">
        </div>

        <p class="otp-note" id="countdownNote">
            Code expires in <strong id="countdown">10:00</strong>
        </p>

        <button type="submit" class="btn btn--accent btn--block" id="verifyBtn" style="margin-top: 18px;">
            Verify and create account
        </button>
    </form>

    <div style="margin-top: 16px; text-align: center; font-size: 13px; color: var(--text-gray);">
        Did not receive the code?
        <form action="{{ route('register.otp.resend') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn--sm btn--ghost" id="resendBtn">Resend code</button>
        </form>
    </div>

    <p class="auth__foot">
        <a href="{{ route('register') }}">
            <x-icon name="arrow-left" :size="14" />
            Use a different email
        </a>
    </p>
@endsection

@section('scripts')
    <script>
        const digits = ['d1', 'd2', 'd3', 'd4', 'd5', 'd6'].map(id => document.getElementById(id));
        const hidden = document.getElementById('hiddenOtp');
        const verifyBtn = document.getElementById('verifyBtn');
        const countdown = document.getElementById('countdown');
        const countdownNote = document.getElementById('countdownNote');

        function syncHidden() {
            hidden.value = digits.map(digit => digit.value).join('');
        }

        digits.forEach((input, index) => {
            input.addEventListener('input', () => {
                input.value = input.value.replace(/\D/g, '');
                input.classList.toggle('is-filled', input.value.length > 0);
                if (input.value && index < 5) {
                    digits[index + 1].focus();
                }
                syncHidden();
            });

            input.addEventListener('keydown', event => {
                if (event.key === 'Backspace' && !input.value && index > 0) {
                    digits[index - 1].focus();
                    digits[index - 1].value = '';
                    digits[index - 1].classList.remove('is-filled');
                    syncHidden();
                }
            });

            input.addEventListener('paste', event => {
                event.preventDefault();
                const pasted = (event.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');

                pasted.split('').forEach((character, position) => {
                    if (digits[position]) {
                        digits[position].value = character;
                        digits[position].classList.add('is-filled');
                    }
                });

                digits[Math.min(pasted.length, 5)].focus();
                syncHidden();
            });
        });

        // submit as soon as all six boxes are filled
        digits.forEach(digit => digit.addEventListener('input', () => {
            if (digits.every(box => box.value.length === 1)) {
                setTimeout(() => document.getElementById('otpForm').submit(), 250);
            }
        }));

        let secondsLeft = 600;

        setInterval(() => {
            secondsLeft--;

            if (secondsLeft <= 0) {
                countdown.textContent = 'expired';
                countdownNote.classList.add('is-expired');
                verifyBtn.disabled = true;
                verifyBtn.textContent = 'Code expired, please resend';
                return;
            }

            const minutes = Math.floor(secondsLeft / 60);
            countdown.textContent = minutes + ':' + String(secondsLeft % 60).padStart(2, '0');
        }, 1000);

        digits[0].focus();
    </script>
@endsection