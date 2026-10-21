<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BookMyBeauty - Luxury Salon Booking & Services')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @yield('styles')
</head>

<body>
    <nav class="navbar">
        <a class="navbar__brand" href="{{ route('home') }}">
            <span class="navbar__mark">BookMy<span>Beauty</span></span>
        </a>

        <div class="navbar__links">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'navbar__active' : '' }}">Home</a>
            <a href="{{ route('browse') }}"
                class="{{ request()->routeIs('browse', 'beautician.view') ? 'navbar__active' : '' }}">Browse
                Beauticians</a>
            <a href="{{ route('about') }}"
                class="{{ request()->routeIs('about') ? 'navbar__active' : '' }}">About</a>
            <a href="{{ route('contact') }}"
                class="{{ request()->routeIs('contact') ? 'navbar__active' : '' }}">Contact</a>

            @auth
                <a href="{{ route(Auth::user()->role . '.dashboard') }}">Dashboard</a>

                @include('components.notification-bell')

                <div class="navbar__account">
                    <span class="navbar__user">
                        <strong>{{ Auth::user()->name }}</strong>
                        {{ ucfirst(Auth::user()->role) }}
                    </span>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="navbar__signout">
                            <x-icon name="logout" :size="15" />
                            Logout
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn--light btn--sm">Login</a>
                <a href="{{ route('register') }}" class="btn btn--accent btn--sm">Register</a>
            @endauth
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    @include('components.footer')

    @include('components.chatbot-widget')

    @yield('scripts')
</body>

</html>