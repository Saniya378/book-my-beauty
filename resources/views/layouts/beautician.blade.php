<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beautician Portal - BookMyBeauty')</title>
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
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="{{ route('home') }}" style="text-decoration: none; padding: 0; margin: 0;">
                <h1 style="margin: 0;">Beauty <span>Portal</span></h1>
            </a>
            <span
                style="background: rgba(201,169,110,0.2); color: var(--accent); font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 12px; text-transform: uppercase; border: 1px solid var(--accent);">Beautician
                Studio</span>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('beautician.dashboard') }}"
                class="{{ request()->routeIs('beautician.dashboard') ? 'active-link' : '' }}">Dashboard</a>
            <a href="{{ route('beautician.bookings') }}"
                class="{{ request()->routeIs('beautician.bookings') ? 'active-link' : '' }}">Appointments</a>
            <a href="{{ route('beautician.services') }}"
                class="{{ request()->routeIs('beautician.services') ? 'active-link' : '' }}">Services</a>
            <a href="{{ route('beautician.availability') }}"
                class="{{ request()->routeIs('beautician.availability') ? 'active-link' : '' }}">Schedule</a>
            <a href="{{ route('beautician.portfolio') }}"
                class="{{ request()->routeIs('beautician.portfolio') ? 'active-link' : '' }}">Portfolio</a>
            <a href="{{ route('beautician.profile') }}"
                class="{{ request()->routeIs('beautician.profile') ? 'active-link' : '' }}">Profile</a>

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
