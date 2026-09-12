@extends('layouts.admin')

@section('title', 'Admin Dashboard - BookMyBeauty')

@section('content')
    <!-- Header -->
    <div
        style="background: linear-gradient(135deg, var(--primary), var(--primary-darker)); color: white; padding: 28px 32px; border-radius: var(--border-radius); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid var(--accent); flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 4px;">Platform Overview</h2>
            <p style="font-size: 13px; opacity: 0.85; margin: 0;">Comprehensive administration console for BookMyBeauty</p>
        </div>
        <div style="text-align: right; font-size: 12px; opacity: 0.9;">
            Today is <strong style="color: var(--accent); font-size: 14px;">{{ date('l, d M Y') }}</strong>
        </div>
    </div>

    <!-- Stats Counter Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 15px; margin-bottom: 30px;">
        <div
            style="background: white; padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center;">
            <div style="font-size: 28px; font-weight: 700; color: var(--primary); font-family: 'Playfair Display', serif;">
                {{ $totalUsers }}</div>
            <div style="font-size: 11px; color: var(--text-gray); text-transform: uppercase; margin-top: 4px;">Total Users
            </div>
        </div>
        <div
            style="background: white; padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center;">
            <div style="font-size: 28px; font-weight: 700; color: #855b25; font-family: 'Playfair Display', serif;">
                {{ $totalBeauticians }}</div>
            <div style="font-size: 11px; color: var(--text-gray); text-transform: uppercase; margin-top: 4px;">Beauticians
            </div>
        </div>
        <div
            style="background: white; padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center;">
            <div style="font-size: 28px; font-weight: 700; color: #1565c0; font-family: 'Playfair Display', serif;">
                {{ $totalCustomers }}</div>
            <div style="font-size: 11px; color: var(--text-gray); text-transform: uppercase; margin-top: 4px;">Customers
            </div>
        </div>
        <div
            style="background: white; padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center;">
            <div style="font-size: 28px; font-weight: 700; color: var(--primary); font-family: 'Playfair Display', serif;">
                {{ $totalBookings }}</div>
            <div style="font-size: 11px; color: var(--text-gray); text-transform: uppercase; margin-top: 4px;">Total
                Bookings</div>
        </div>
        <div
            style="background: white; padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center;">
            <div style="font-size: 28px; font-weight: 700; color: #b78103; font-family: 'Playfair Display', serif;">
                {{ $pendingBookings }}</div>
            <div style="font-size: 11px; color: var(--text-gray); text-transform: uppercase; margin-top: 4px;">Pending
                Requests</div>
        </div>
        <div
            style="background: white; padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center;">
            <div style="font-size: 28px; font-weight: 700; color: #2e7d32; font-family: 'Playfair Display', serif;">Rs.
                {{ number_format($totalRevenue, 0) }}</div>
            <div style="font-size: 11px; color: var(--text-gray); text-transform: uppercase; margin-top: 4px;">Platform
                Revenue</div>
        </div>
    </div>

    <!-- Two-Column Layout -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px; align-items: start;">
        <!-- Recent Bookings Table -->
        <div style="background: white; border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary); margin: 0;">
                    Recent Platform Bookings</h3>
                <a href="{{ route('admin.bookings') }}"
                    style="font-size: 12px; color: var(--accent); text-decoration: none; font-weight: 600;">View All
                    Bookings &rarr;</a>
            </div>

            @if ($recentBookings->isEmpty())
                <p style="color: var(--text-gray); font-size: 13px; text-align: center; padding: 20px 0;">No bookings yet.
                </p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr
                                style="border-bottom: 2px solid #f0f0f0; text-align: left; color: var(--text-gray); font-size: 11px; text-transform: uppercase;">
                                <th style="padding: 10px;">ID</th>
                                <th style="padding: 10px;">Customer</th>
                                <th style="padding: 10px;">Beautician</th>
                                <th style="padding: 10px;">Service</th>
                                <th style="padding: 10px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentBookings as $b)
                                <tr style="border-bottom: 1px solid #f5f5f5;">
                                    <td style="padding: 12px 10px;">#{{ $b->id }}</td>
                                    <td style="padding: 12px 10px;"><strong>{{ $b->customer->name ?? 'User' }}</strong></td>
                                    <td style="padding: 12px 10px;">
                                        {{ $b->service->beautician->user->name ?? 'Beautician' }}</td>
                                    <td style="padding: 12px 10px;">{{ $b->service->service_name ?? 'Service' }}</td>
                                    <td style="padding: 12px 10px;">
                                        <span
                                            style="font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 10px;
                                    {{ $b->status === 'confirmed' ? 'background:#e8f5e9; color:#2e7d32;' : ($b->status === 'cancelled' ? 'background:#ffebee; color:#c62828;' : 'background:#fff8e1; color:#b78103;') }}">
                                            {{ $b->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Recent User Registrations -->
        <div style="background: white; border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; color: var(--primary); margin: 0;">New
                    Registrations</h3>
                <a href="{{ route('admin.users') }}"
                    style="font-size: 12px; color: var(--accent); text-decoration: none; font-weight: 600;">Manage Users</a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach ($recentUsers as $u)
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px solid #f5f5f5;">
                        <div>
                            <div style="font-size: 13px; font-weight: 600; color: var(--primary);">{{ $u->name }}
                            </div>
                            <div style="font-size: 11px; color: var(--text-gray);">{{ $u->email }}</div>
                        </div>
                        <span
                            style="font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 2px 7px; border-radius: 4px;
                    {{ $u->role === 'beautician' ? 'background:#e8f0fe; color:#1967d2;' : ($u->role === 'admin' ? 'background:#fce8e6; color:#c5221f;' : 'background:#f1f3f4; color:#5f6368;') }}">
                            {{ $u->role }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
