@extends('layouts.beautician')

@section('title', 'Beautician Studio - Beauty Portal')

@section('content')
<!-- Welcome Banner -->
<div style="background: linear-gradient(135deg, var(--primary), var(--primary-darker)); color: white; padding: 32px 35px; border-radius: var(--border-radius); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid var(--accent); flex-wrap: wrap; gap: 20px;">
    <div style="display: flex; align-items: center; gap: 20px;">
        <img src="{{ $profile->photo_url }}" alt="{{ Auth::user()->name }}" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent); background: white;">
        <div>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 26px; margin-bottom: 4px;">Welcome back, {{ Auth::user()->name }}!</h2>
            <p style="font-size: 13px; opacity: 0.85; margin: 0;">📍 {{ $profile->location ?: 'Location not set yet' }} &bull; 📞 {{ $profile->phone ?: 'Phone not set' }}</p>
        </div>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('beautician.profile') }}" class="btn btn-outline" style="padding: 10px 18px; font-size: 12px; color: white; border-color: white; background: transparent;">Edit Profile</a>
        <a href="{{ route('beautician.services') }}" class="btn" style="padding: 10px 18px; font-size: 12px;">+ Add Service</a>
    </div>
</div>

<!-- Profile incomplete alert if needed -->
@if(empty($profile->bio) || empty($profile->location) || empty($profile->phone))
<div style="background: #fff8e1; border-left: 4px solid #b78103; padding: 16px 20px; border-radius: 8px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <strong style="color: #b78103;">Complete your profile to attract more clients:</strong>
        <span style="font-size: 13px; color: #555; margin-left: 8px;">Please add your bio, salon/service location, and contact number.</span>
    </div>
    <a href="{{ route('beautician.profile') }}" class="btn" style="padding: 6px 14px; font-size: 11px;">Update Now</a>
</div>
@endif

<!-- Stats Counters -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div style="background: white; padding: 22px 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center; border-top: 4px solid var(--primary);">
        <div style="font-size: 32px; font-weight: 700; color: var(--primary); font-family: 'Playfair Display', serif;">{{ $totalBookings }}</div>
        <div style="font-size: 12px; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">Total Appointments</div>
    </div>

    <div style="background: white; padding: 22px 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center; border-top: 4px solid #b78103;">
        <div style="font-size: 32px; font-weight: 700; color: #b78103; font-family: 'Playfair Display', serif;">{{ $pendingBookings }}</div>
        <div style="font-size: 12px; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">Pending Requests</div>
    </div>

    <div style="background: white; padding: 22px 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center; border-top: 4px solid #2e7d32;">
        <div style="font-size: 32px; font-weight: 700; color: #2e7d32; font-family: 'Playfair Display', serif;">Rs. {{ number_format($totalRevenue, 0) }}</div>
        <div style="font-size: 12px; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">Confirmed Earnings</div>
    </div>

    <div style="background: white; padding: 22px 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center; border-top: 4px solid var(--accent);">
        <div style="font-size: 32px; font-weight: 700; color: var(--accent); font-family: 'Playfair Display', serif;">{{ number_format($avgRating, 1) }} ★</div>
        <div style="font-size: 12px; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">{{ $reviewCount }} Client Reviews</div>
    </div>
</div>

<!-- Recent Appointments Table -->
<div style="background: white; border-radius: var(--border-radius); box-shadow: var(--shadow); padding: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 20px; color: var(--primary); margin: 0;">Recent Appointment Requests</h3>
        <a href="{{ route('beautician.bookings') }}" class="btn" style="padding: 7px 18px; font-size: 12px;">Manage All Appointments &rarr;</a>
    </div>

    @if($recentBookings->isEmpty())
        <div style="text-align: center; padding: 40px 20px; color: var(--text-gray);">
            No appointments booked yet. Keep your portfolio and services updated!
        </div>
    @else
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="border-bottom: 2px solid #f0f0f0; text-align: left; color: var(--text-gray); font-size: 11px; text-transform: uppercase;">
                        <th style="padding: 12px 10px;">Customer</th>
                        <th style="padding: 12px 10px;">Service</th>
                        <th style="padding: 12px 10px;">Date & Time</th>
                        <th style="padding: 12px 10px;">Price</th>
                        <th style="padding: 12px 10px;">Status</th>
                        <th style="padding: 12px 10px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentBookings as $b)
                    <tr style="border-bottom: 1px solid #f5f5f5;">
                        <td style="padding: 14px 10px;">
                            <strong>{{ $b->customer->name ?? 'Customer' }}</strong>
                            <div style="font-size: 11px; color: var(--text-gray);">{{ $b->customer->email ?? '' }}</div>
                        </td>
                        <td style="padding: 14px 10px;">{{ $b->service->service_name ?? 'Service' }}</td>
                        <td style="padding: 14px 10px;">
                            <div>📅 {{ $b->booking_date ? $b->booking_date->format('d M Y') : 'N/A' }}</div>
                            <div style="font-size: 11px; color: var(--text-gray);">🕒 {{ date('h:i A', strtotime($b->booking_time)) }}</div>
                        </td>
                        <td style="padding: 14px 10px;">
                            <strong style="color: #2e7d32;">Rs. {{ number_format($b->service->price ?? 0, 0) }}</strong>
                        </td>
                        <td style="padding: 14px 10px;">
                            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 12px;
                                {{ $b->status === 'confirmed' ? 'background:#e8f5e9; color:#2e7d32;' : ($b->status === 'cancelled' ? 'background:#ffebee; color:#c62828;' : 'background:#fff8e1; color:#b78103;') }}">
                                {{ $b->status }}
                            </span>
                        </td>
                        <td style="padding: 14px 10px;">
                            @if($b->status === 'pending')
                                <div style="display: flex; gap: 6px;">
                                    <form action="{{ route('beautician.bookings.status', $b->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="confirmed">
                                        <button type="submit" style="background: #2e7d32; color: white; border: none; padding: 5px 10px; border-radius: 4px; font-size: 11px; cursor: pointer; font-weight: 600;">Confirm</button>
                                    </form>
                                    <form action="{{ route('beautician.bookings.status', $b->id) }}" method="POST" onsubmit="return confirm('Cancel this appointment?');">
                                        @csrf
                                        <input type="hidden" name="action" value="cancelled">
                                        <button type="submit" style="background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; padding: 5px 10px; border-radius: 4px; font-size: 11px; cursor: pointer; font-weight: 600;">Cancel</button>
                                    </form>
                                </div>
                            @else
                                <span style="font-size: 11px; color: var(--text-gray);">Managed</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
