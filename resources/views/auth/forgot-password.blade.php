@extends('layouts.auth')

@section('title', 'Forgot Password - BookMyBeauty')

@section('media-class', 'auth__media--login')

@section('headline', 'It happens to everyone')

@section('intro', 'Enter the email address on your account and we will send you a link to choose a new password.')

@section('form')
    <div class="page-head__title">Reset password</div>
    <p class="auth__sub" style="margin-bottom: 22px;">We will email you a secure reset link.</p>

    @include('components.alert')

    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <div class="field">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                placeholder="name@example.com">
        </div>

        <button type="submit" class="btn btn--accent btn--block">
            Send reset link
            <x-icon name="arrow-right" :size="15" />
        </button>
    </form>

    <p class="auth__foot">
        Remembered it? <a href="{{ route('login') }}">Back to sign in</a>
    </p>
@endsection