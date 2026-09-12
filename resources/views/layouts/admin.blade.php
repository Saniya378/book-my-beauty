<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Console - BookMyBeauty')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <!-- Chart.js for Admin Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('styles')
</head>

<body>
    <nav class="navbar" style="background: #111424; border-bottom: 2px solid var(--accent);">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="padding: 0; margin: 0;">
                <h1 style="margin: 0; font-size: 19px;">BookMy<span>Beauty</span></h1>
            </div>
            <span
                style="background: #c9a96e; color: #1a1a2e; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 1px;">Admin
                Console</span>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('admin.dashboard') }}"
                class="{{ request()->routeIs('admin.dashboard') ? 'active-link' : '' }}">Dashboard</a>
            <a href="{{ route('admin.users') }}"
                class="{{ request()->routeIs('admin.users') ? 'active-link' : '' }}">Users</a>
            <a href="{{ route('admin.bookings') }}"
                class="{{ request()->routeIs('admin.bookings') ? 'active-link' : '' }}">Bookings</a>
            <a href="{{ route('admin.analytics') }}"
                class="{{ request()->routeIs('admin.analytics') ? 'active-link' : '' }}">Analytics</a>

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

    @yield('scripts')
</body>

</html>
