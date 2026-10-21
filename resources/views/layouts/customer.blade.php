<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Customer Portal - BookMyBeauty')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @yield('styles')
</head>

<body>
    <nav class="navbar">
        <a class="navbar__brand" href="{{ route('customer.dashboard') }}">
            <span class="navbar__mark">BookMy<span>Beauty</span></span>
            <span class="navbar__tag">My Account</span>
        </a>

        <div class="navbar__links">
            <a href="{{ route('browse') }}">Browse Beauticians</a>
            <a href="{{ route('customer.dashboard') }}"
                class="{{ request()->routeIs('customer.dashboard') ? 'navbar__active' : '' }}">Dashboard</a>
            <a href="{{ route('customer.bookings') }}"
                class="{{ request()->routeIs('customer.bookings') ? 'navbar__active' : '' }}">My Bookings</a>
            <a href="{{ route('customer.favorites') }}"
                class="{{ request()->routeIs('customer.favorites') ? 'navbar__active' : '' }}">Favorites</a>

            @include('components.notification-bell')

            <div class="navbar__account">
                <span class="navbar__user">
                    <strong>{{ Auth::user()->name }}</strong>
                    Customer
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