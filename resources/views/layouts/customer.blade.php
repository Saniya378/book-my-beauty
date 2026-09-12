<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Customer Portal - BookMyBeauty')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    @yield('styles')
</head>

<body>
    <nav class="navbar">
        <div style="padding: 0; margin: 0;">
            <h1 style="margin: 0;">BookMy<span>Beauty</span></h1>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('browse') }}">Browse Beauticians</a>
            <a href="{{ route('customer.dashboard') }}"
                class="{{ request()->routeIs('customer.dashboard') ? 'active-link' : '' }}">Dashboard</a>
            <a href="{{ route('customer.bookings') }}"
                class="{{ request()->routeIs('customer.bookings') ? 'active-link' : '' }}">My Bookings</a>
            <a href="{{ route('customer.favorites') }}"
                class="{{ request()->routeIs('customer.favorites') ? 'active-link' : '' }}">Favorites</a>

            @include('components.notification-bell')

            <form action="{{ route('logout') }}" method="POST" style="display: inline; margin-left: 15px;">
                @csrf
                <button type="submit" class="btn btn-outline"
                    style="padding: 7px 16px; font-size: 12px; color: var(--accent); border-color: var(--accent); background: transparent;">Logout</button>
            </form>
        </div>
    </nav>

    <div style="max-width: 1200px; margin: 30px auto; padding: 0 20px;">
        @include('components.alert')
        @yield('content')
    </div>

    <!-- Bella AI Chatbot Widget -->
    @include('components.chatbot-widget')

    @yield('scripts')
</body>

</html>
