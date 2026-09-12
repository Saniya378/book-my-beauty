@extends('layouts.customer')

@section('title', 'Customer Dashboard - BookMyBeauty')

@section('content')
    <!-- Header Banner -->
    <div
        style="background: linear-gradient(135deg, var(--primary), var(--primary-darker)); color: white; padding: 30px; border-radius: var(--border-radius); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid var(--accent); flex-wrap: wrap; gap: 20px;">
        <div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 26px; margin-bottom: 6px;">Hello,
                {{ Auth::user()->name }}! 👋</h2>
            <p style="font-size: 13px; opacity: 0.85; margin: 0;">Welcome to your beauty appointment hub. Manage your
                appointments, browse top artists, and discover new styles.</p>
        </div>
        <a href="{{ route('browse') }}" class="btn" style="padding: 11px 24px; font-size: 13px; font-weight: 600;">Browse
            Beauticians</a>
    </div>

    <!-- Stats Counter Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 35px;">
        <div
            style="background: white; padding: 22px 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center; border-top: 4px solid var(--primary);">
            <div style="font-size: 32px; font-weight: 700; color: var(--primary); font-family: 'Playfair Display', serif;">
                {{ $totalBookings }}</div>
            <div
                style="font-size: 12px; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">
                Total Bookings</div>
        </div>
        <div
            style="background: white; padding: 22px 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center; border-top: 4px solid #b78103;">
            <div style="font-size: 32px; font-weight: 700; color: #b78103; font-family: 'Playfair Display', serif;">
                {{ $pendingBookings }}</div>
            <div
                style="font-size: 12px; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">
                Pending Confirmation</div>
        </div>
        <div
            style="background: white; padding: 22px 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center; border-top: 4px solid #2e7d32;">
            <div style="font-size: 32px; font-weight: 700; color: #2e7d32; font-family: 'Playfair Display', serif;">
                {{ $confirmedBookings }}</div>
            <div
                style="font-size: 12px; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">
                Confirmed Appointments</div>
        </div>
        <div
            style="background: white; padding: 22px 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center; border-top: 4px solid #ef4444;">
            <div style="font-size: 32px; font-weight: 700; color: #ef4444; font-family: 'Playfair Display', serif;">
                {{ $favoriteCount }}</div>
            <div
                style="font-size: 12px; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">
                Saved Favorites</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;">
        <!-- Recent Bookings Table -->
        <div style="background: white; border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary); margin: 0;">
                    Recent Appointments</h3>
                <a href="{{ route('customer.bookings') }}"
                    style="font-size: 12px; color: var(--accent); text-decoration: none; font-weight: 600;">View All
                    &rarr;</a>
            </div>

            @if ($recentBookings->isEmpty())
                <p style="color: var(--text-gray); font-size: 13px; text-align: center; padding: 30px 0;">No appointments
                    booked yet.</p>
            @else
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr
                            style="border-bottom: 2px solid #f0f0f0; text-align: left; color: var(--text-gray); font-size: 11px; text-transform: uppercase;">
                            <th style="padding: 10px 0;">Beautician</th>
                            <th style="padding: 10px 0;">Service</th>
                            <th style="padding: 10px 0;">Date</th>
                            <th style="padding: 10px 0;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentBookings as $b)
                            <tr style="border-bottom: 1px solid #f5f5f5;">
                                <td style="padding: 12px 0;">
                                    <strong>{{ $b->service->beautician->user->name ?? 'Beautician' }}</strong></td>
                                <td style="padding: 12px 0;">{{ $b->service->service_name ?? 'Service' }}</td>
                                <td style="padding: 12px 0;">
                                    {{ $b->booking_date ? $b->booking_date->format('d M Y') : 'N/A' }}</td>
                                <td style="padding: 12px 0;">
                                    <span
                                        style="font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 12px;
                                {{ $b->status === 'confirmed' ? 'background:#e8f5e9; color:#2e7d32;' : ($b->status === 'cancelled' ? 'background:#ffebee; color:#c62828;' : 'background:#fff8e1; color:#b78103;') }}">
                                        {{ $b->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Quick Favorites -->
        <div style="background: white; border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary); margin: 0;">My
                    Favorites</h3>
                <a href="{{ route('customer.favorites') }}"
                    style="font-size: 12px; color: var(--accent); text-decoration: none; font-weight: 600;">View All</a>
            </div>

            @if ($recentFavorites->isEmpty())
                <p style="color: var(--text-gray); font-size: 13px; text-align: center; padding: 20px 0;">No favorites saved
                    yet.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @foreach ($recentFavorites as $fav)
                        @if ($fav->beautician)
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #f5f5f5;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="{{ $fav->beautician->photo_url }}"
                                        alt="{{ $fav->beautician->user->name ?? 'Beautician' }}"
                                        style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                    <div>
                                        <div style="font-weight: 600; font-size: 13px; color: var(--primary);">
                                            {{ $fav->beautician->user->name ?? 'Beautician' }}</div>
                                        <div style="font-size: 11px; color: var(--text-gray);">📍
                                            {{ $fav->beautician->location ?: 'Location' }}</div>
                                    </div>
                                </div>
                                <a href="{{ route('customer.book', $fav->beautician_id) }}" class="btn"
                                    style="padding: 5px 12px; font-size: 11px;">Book</a>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
