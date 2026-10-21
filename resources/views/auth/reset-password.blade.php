@extends('layouts.auth')

@section('title', 'Choose a New Password - BookMyBeauty')

@section('media-class', 'auth__media--login')

@section('headline', 'Set a new password')

@section('intro', 'Pick something you have not used before. Once saved you will be signed out on other devices for safety.')

@section('form')
    <div class="page-head__title">Choose a new password</div>
    <p class="auth__sub" style="margin-bottom: 22px;">Updating the password for <strong style="color: var(--primary)">{{ $email }}</strong></p>

    @include('components.alert')

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="field">
            <label for="password">New password</label>
            <div class="pw">
                <input type="password" id="password" name="password" required autofocus
                    placeholder="At least 6 characters">
                <button type="button" class="pw__toggle" data-target="password" aria-label="Show password">
                    <span class="pw__show"><x-icon name="eye" :size="18" /></span>
                    <span class="pw__hide"><x-icon name="close" :size="18" /></span>
                </button>
            </div>
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm new password</label>
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

        <button type="submit" class="btn btn--accent btn--block">Save new password</button>
    </form>

    <p class="auth__foot">
        <a href="{{ route('login') }}">
            <x-icon name="arrow-left" :size="14" />
            Back to sign in
        </a>
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