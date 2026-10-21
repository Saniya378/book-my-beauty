<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Console - BookMyBeauty')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @yield('styles')
</head>

<body>
    <nav class="navbar">
        <a class="navbar__brand" href="{{ route('admin.dashboard') }}">
            <span class="navbar__mark">BookMy<span>Beauty</span></span>
            <span class="navbar__tag">Admin</span>
        </a>

        <div class="navbar__links">
            <a href="{{ route('admin.dashboard') }}"
                class="{{ request()->routeIs('admin.dashboard') ? 'active-link' : '' }}">Dashboard</a>
            <a href="{{ route('admin.analytics') }}"
                class="{{ request()->routeIs('admin.analytics') ? 'active-link' : '' }}">Analytics</a>
            <a href="{{ route('admin.bookings') }}"
                class="{{ request()->routeIs('admin.bookings') ? 'active-link' : '' }}">Bookings</a>
            <a href="{{ route('admin.users') }}"
                class="{{ request()->routeIs('admin.users') ? 'active-link' : '' }}">Users</a>

            @include('components.notification-bell')

            <div class="navbar__account">
                <span class="navbar__user">
                    <strong>{{ Auth::user()->name }}</strong>
                    Administrator
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

    <main class="admin-shell">
        @include('components.alert')
        @yield('content')
    </main>

    <footer class="admin-footer">
        &copy; {{ date('Y') }} BookMyBeauty &middot; Admin Console
    </footer>

    @yield('scripts')
</body>

</html>