<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beautician Studio - BookMyBeauty')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @yield('styles')
</head>

<body>
    <nav class="navbar">
        <a class="navbar__brand" href="{{ route('beautician.dashboard') }}">
            <span class="navbar__mark">BookMy<span>Beauty</span></span>
            <span class="navbar__tag">Studio</span>
        </a>

        <div class="navbar__links">
            <a href="{{ route('beautician.dashboard') }}"
                class="{{ request()->routeIs('beautician.dashboard') ? 'navbar__active' : '' }}">Dashboard</a>
            <a href="{{ route('beautician.bookings') }}"
                class="{{ request()->routeIs('beautician.bookings') ? 'navbar__active' : '' }}">Appointments</a>
            <a href="{{ route('beautician.services') }}"
                class="{{ request()->routeIs('beautician.services') ? 'navbar__active' : '' }}">Services</a>
            <a href="{{ route('beautician.availability') }}"
                class="{{ request()->routeIs('beautician.availability') ? 'navbar__active' : '' }}">Schedule</a>
            <a href="{{ route('beautician.portfolio') }}"
                class="{{ request()->routeIs('beautician.portfolio') ? 'navbar__active' : '' }}">Portfolio</a>
            <a href="{{ route('beautician.reviews') }}"
                class="{{ request()->routeIs('beautician.reviews') ? 'navbar__active' : '' }}">Reviews</a>
            <a href="{{ route('beautician.profile') }}"
                class="{{ request()->routeIs('beautician.profile') ? 'navbar__active' : '' }}">Profile</a>

            @include('components.notification-bell')

            <div class="navbar__account">
                <span class="navbar__user">
                    <strong>{{ Auth::user()->name }}</strong>
                    Beautician
                </span>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="navbar__signout">
                        <x-icon name="logout" :size="15" />
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="portal">
        @include('components.alert')
        @yield('content')
    </div>

    @include('components.footer')

    @include('components.chatbot-widget')

    @yield('scripts')
</body>

</html>