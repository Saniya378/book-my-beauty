@extends('layouts.auth')

@section('title', 'Admin Sign In - BookMyBeauty')

@section('media-class', 'auth__media--login')

@section('headline', 'Administration console')

@section('intro', 'This entrance is reserved for platform administrators. Customer and beautician accounts sign in from the regular login page.')

@section('points')
    <li>
        <x-icon name="check" :size="16" />
        Manage bookings, users and reported content
    </li>
    <li>
        <x-icon name="check" :size="16" />
        Review revenue trends and platform analytics
    </li>
    <li>
        <x-icon name="check" :size="16" />
        Suspend or restore registered accounts
    </li>
@endsection

@section('form')
    <div class="page-head__title">Administrator sign in</div>
    <p class="auth__sub" style="margin-bottom: 22px;">Use your staff credentials to open the console.</p>

    @include('components.alert')

    <form action="{{ route('admin.login.submit') }}" method="POST">
        @csrf

        <div class="field">
            <label for="email">Administrator email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                placeholder="admin@bookmybeauty.com">
        </div>

        <div class="field">
            <label for="password">Password</label>
            <div class="pw">
                <input type="password" id="password" name="password" required placeholder="Your password">
                <button type="button" class="pw__toggle" data-target="password" aria-label="Show password">
                    <span class="pw__show"><x-icon name="eye" :size="18" /></span>
                    <span class="pw__hide"><x-icon name="close" :size="18" /></span>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn--accent btn--block">Enter console</button>
    </form>

    <p class="auth__foot">
        Not an administrator? <a href="{{ route('login') }}">Customer sign in</a>
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