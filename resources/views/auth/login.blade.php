@extends('layouts.auth')

@section('title', 'Login - BookMyBeauty')

@section('media-class', 'auth__media--login')

@section('headline', 'Welcome back to your beauty routine')

@section('intro', 'Sign in to book new appointments, follow your beautician’s schedule and keep every past visit in one place.')

@section('points')
    <li>
        <x-icon name="check" :size="16" />
        Track confirmations and download PDF receipts
    </li>
    <li>
        <x-icon name="check" :size="16" />
        Save favourite beauticians and rebook in two clicks
    </li>
    <li>
        <x-icon name="check" :size="16" />
        Ask Bella AI for styling and pricing help any time
    </li>
@endsection

@section('form')
    <div class="page-head__title">Sign In</div>
    <p class="auth__sub" style="margin-bottom: 22px;">Enter the email and password you registered with.</p>

    @include('components.alert')

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="field">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                placeholder="name@example.com">
        </div>

        <div class="field">
            <div class="field-row">
                <label for="password">Password</label>
                <a href="{{ route('password.request') }}" class="auth__link">Forgot password?</a>
            </div>

            <div class="pw">
                <input type="password" id="password" name="password" required placeholder="Enter your password">
                <button type="button" class="pw__toggle" data-target="password" aria-label="Show password">
                    <span class="pw__show"><x-icon name="eye" :size="18" /></span>
                    <span class="pw__hide"><x-icon name="close" :size="18" /></span>
                </button>
            </div>
        </div>

        <label class="check">
            <input type="checkbox" name="remember" value="1">
            Keep me signed in on this device
        </label>

        <button type="submit" class="btn btn--accent btn--block">Sign in</button>
    </form>

    <p class="auth__foot">
        No account yet? <a href="{{ route('register') }}">Create one</a>
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