<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BookMyBeauty - Luxury Salon Booking & Services')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    @yield('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div style="padding: 0; margin: 0;">
            <h1 style="margin: 0;">BookMy<span>Beauty</span></h1>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('browse') }}">Browse Beauticians</a>

            @auth
                @if (Auth::user()->role === 'customer')
                    <a href="{{ route('customer.dashboard') }}">Dashboard</a>
                    <a href="{{ route('customer.bookings') }}">My Bookings</a>
                    <a href="{{ route('customer.favorites') }}">Favorites</a>
                @elseif(Auth::user()->role === 'beautician')
                    <a href="{{ route('beautician.dashboard') }}">Dashboard</a>
                    <a href="{{ route('beautician.bookings') }}">Appointments</a>
                    <a href="{{ route('beautician.services') }}">Services</a>
                @elseif(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                @endif

                @include('components.notification-bell')

                <form action="{{ route('logout') }}" method="POST" style="display: inline; margin-left: 15px;">
                    @csrf
                    <button type="submit" class="btn"
                        style="padding: 8px 18px; font-size: 13px; background: transparent; color: var(--accent); border: 1px solid var(--accent);">Logout
                        ({{ Auth::user()->name }})
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn" style="padding: 8px 20px; font-size: 13px;">Login</a>
                <a href="{{ route('register') }}" class="btn" style="padding: 8px 20px; font-size: 13px;">Register</a>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>


    <!-- Footer -->
    <footer
        style="background: var(--primary); color: white; padding: 50px 30px 25px; margin-top: 60px; border-top: 3px solid var(--accent);">
        <div
            style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1.5fr 1fr 1.5fr; gap: 30px; text-align: left;">
            <div>
                <h3
                    style="font-family: 'Playfair Display', serif; font-size: 22px; color: var(--accent); margin-bottom: 12px; text-align: left;">
                    BookMyBeauty</h3>
                <p style="font-size: 13px; color: rgba(255,255,255,0.7); line-height: 1.6; text-align: left;">Your
                    ultimate destination to find, compare, and book the most talented verified beauticians and stylists
                    in your area.</p>
            </div>
            <div>
                <h4
                    style="color: var(--accent); font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; text-align: left;">
                    Quick Links</h4>
                <ul style="list-style: none; padding: 0; font-size: 13px; line-height: 2; text-align: left;">
                    <li><a href="{{ route('home') }}"
                            style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a></li>
                    <li><a href="{{ route('browse') }}"
                            style="color: rgba(255,255,255,0.8); text-decoration: none;">Browse Beauticians</a></li>
                    <li><a href="{{ route('login') }}"
                            style="color: rgba(255,255,255,0.8); text-decoration: none;">Client Login</a></li>
                    <li><a href="{{ route('register') }}"
                            style="color: rgba(255,255,255,0.8); text-decoration: none;">Join as Beautician</a></li>
                </ul>
            </div>
            <div>
                <h4
                    style="color: var(--accent); font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; text-align: left;">
                    Need Assistance?</h4>
                <p style="font-size: 13px; color: rgba(255,255,255,0.7); margin-bottom: 10px; text-align: left;">Our AI
                    assistant Bella is available 24/7 on the bottom right of your screen to answer questions and help
                    book.</p>
                <span
                    style="display: inline-block; background: rgba(201,169,110,0.15); color: var(--accent); border: 1px solid var(--accent); padding: 5px 12px; border-radius: 20px; font-size: 11px;">Powered
                    by Google Gemini</span>
            </div>
        </div>
        <div
            style="max-width: 1200px; margin: 30px auto 0; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); text-align: center; font-size: 12px; color: rgba(255,255,255,0.5);">
            &copy; {{ date('Y') }} BookMyBeauty. All rights reserved. KIU Final Year Project.
        </div>
    </footer>

    <!-- Chatbot Widget -->
    @include('components.chatbot-widget')

    @yield('scripts')
</body>

</html>
