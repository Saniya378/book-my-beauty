@extends('layouts.auth')

@section('title', 'Create Account - BookMyBeauty')

@section('media-class', 'auth__media--register')

@section('headline', 'Join a community that takes beauty seriously')

@section('intro', 'Create a free account as a customer to book appointments, or as a beautician to publish your services and manage your calendar.')

@section('points')
    <li>
        <x-icon name="check" :size="16" />
        Customers: book, reschedule and review in a few taps
    </li>
    <li>
        <x-icon name="check" :size="16" />
        Beauticians: your own service list, schedule and portfolio
    </li>
    <li>
        <x-icon name="check" :size="16" />
        Email verification keeps every account genuine
    </li>
@endsection

@section('form')
    <div class="steps">
        <div class="steps__dot">1</div>
        <div class="steps__line"></div>
        <div class="steps__dot steps__dot--idle">2</div>
    </div>

    <div class="page-head__title">Create account</div>
    <p class="auth__sub" style="margin-bottom: 22px;">Step 1 of 2 &mdash; we will email you a 6 digit code to verify your address.</p>

    @include('components.alert')

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="field">
            <label for="name">Full name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                placeholder="e.g. Ayesha Khan">
        </div>

        <div class="field">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                placeholder="name@example.com">
        </div>

        <div class="field">
            <label for="role">I want to join as</label>
            <select id="role" name="role" required>
                <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>
                    Customer &mdash; book appointments
                </option>
                <option value="beautician" {{ old('role') === 'beautician' ? 'selected' : '' }}>
                    Beautician &mdash; offer services
                </option>
            </select>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <div class="pw">
                <input type="password" id="password" name="password" required placeholder="At least 6 characters">
                <button type="button" class="pw__toggle" data-target="password" aria-label="Show password">
                    <span class="pw__show"><x-icon name="eye" :size="18" /></span>
                    <span class="pw__hide"><x-icon name="close" :size="18" /></span>
                </button>
            </div>
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm password</label>
            <div class="pw">
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    placeholder="Repeat your password">
                <button type="button" class="pw__toggle" data-target="password_confirmation"
                    aria-label="Show password">
                    <span class="pw__show"><x-icon name="eye" :size="18" /></span>
                    <span class="pw__hide"><x-icon name="close" :size="18" /></span>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn--accent btn--block">
            Send verification code
            <x-icon name="arrow-right" :size="15" />
        </button>
    </form>

    <p class="auth__foot">
        Already registered? <a href="{{ route('login') }}">Sign in</a>
    </p>
@endsection

@section('scripts')
    <script>
        document.querySelectorAll('.pw__toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(button.dataset.target);
                var revealed = input.type === 'text';

                input.type = revealed ? 'password' : 'text';
                button.classList.toggle('is-on', !revealed);
                button.setAttribute('aria-label', revealed ? 'Show password' : 'Hide password');
            });
        });
    </script>
@endsection