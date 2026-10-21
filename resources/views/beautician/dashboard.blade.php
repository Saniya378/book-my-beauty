@extends('layouts.beautician')

@section('title', 'Beautician Dashboard - BookMyBeauty')

@section('content')
    <div class="banner">
        <div style="display: flex; align-items: center; gap: 18px;">
            <img src="{{ $profile->photo_url }}" alt="{{ Auth::user()->name }}"
                style="width: 68px; height: 68px; border-radius: 50%; object-fit: cover; border: 3px solid var(--accent);">

            <div>
                <h1 class="banner__title">Welcome back, {{ Auth::user()->name }}</h1>
                <p class="banner__sub">
                    @if ($profile->location || $profile->phone)
                        {{ collect([$profile->location, $profile->phone])->filter()->join('  ·  ') }}
                    @else
                        Add your location and phone so customers can reach you.
                    @endif
                </p>
            </div>
        </div>

        <div class="banner__actions">
            <a href="{{ route('beautician.profile') }}" class="btn btn--light">
                <x-icon name="edit" :size="15" />
                Edit profile
            </a>
            <a href="{{ route('beautician.services') }}" class="btn btn--accent">
                <x-icon name="plus" :size="15" />
                Add service
            </a>
        </div>
    </div>

    @if (empty($profile->bio) || empty($profile->location) || empty($profile->phone))
        <div class="notice notice--warn" style="margin-bottom: 22px;">
            <span class="notice__icon"><x-icon name="warning" :size="17" /></span>
            <span>
                <strong>Your profile is incomplete.</strong>
                Add a bio, your salon location and a contact number so customers can find and reach you.
                <a href="{{ route('beautician.profile') }}" class="auth__link">Update now</a>
            </span>
        </div>
    @endif

    <div class="kpi-grid">
        <div class="kpi">
            <span class="kpi__icon"><x-icon name="bookings" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ $totalBookings }}</div>
                <div class="kpi__label">Total appointments</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="pending" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ $pendingBookings }}</div>
                <div class="kpi__label">Awaiting your confirmation</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="revenue" :size="19" /></span>
            <div>
                <div class="kpi__value">Rs. {{ number_format($totalRevenue, 0) }}</div>
                <div class="kpi__label">Confirmed earnings</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="rating" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ number_format($avgRating, 1) }} <span class="rating">&#9733;</span></div>
                <div class="kpi__label">Average rating</div>
                <div class="kpi__note">
                <a href="{{ route('beautician.reviews') }}" class="auth__link">
                    {{ $reviewCount }} customer review{{ $reviewCount === 1 ? '' : 's' }}
                </a>
            </div>
            </div>
        </div>
    </div>

    <section class="panel">
        <div class="panel__head">
            <div>
                <h2 class="panel__title">Recent appointment requests</h2>
                <p class="panel__meta">Confirm or decline the latest requests</p>
            </div>
            <a href="{{ route('beautician.bookings') }}" class="panel__link">View all</a>
        </div>

        <div class="panel__body panel__body--flush">
            @if ($recentBookings->isEmpty())
                <div class="empty">
                    <strong>No requests yet</strong>
                    Keep your services and availability up to date so customers can book you.
                </div>
            @else
                <div class="table-scroll">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Schedule</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentBookings as $booking)
                                <tr>
                                    <td>
                                        {{ $booking->customer?->name ?? 'Customer' }}
                                        <span class="table__note">{{ $booking->customer?->email }}</span>
                                    </td>
                                    <td>{{ $booking->service?->service_name ?? 'Service' }}</td>
                                    <td>
                                        {{ $booking->booking_date?->format('j M Y') }}
                                        <span class="table__note">{{ date('g:i A', strtotime($booking->booking_time)) }}</span>
                                    </td>
                                    <td>Rs. {{ number_format($booking->service?->price ?? 0, 0) }}</td>
                                    <td><span class="pill pill--{{ $booking->status }}">{{ $booking->status }}</span></td>
                                    <td>
                                        <div class="table__actions">
                                            @if ($booking->status === 'pending')
                                                <form action="{{ route('beautician.bookings.status', $booking->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="action" value="confirmed">
                                                    <button type="submit" class="action-btn action-btn--approve">Confirm</button>
                                                </form>

                                                <form action="{{ route('beautician.bookings.status', $booking->id) }}" method="POST"
                                                    onsubmit="return confirm('Decline this appointment?')">
                                                    @csrf
                                                    <input type="hidden" name="action" value="cancelled">
                                                    <button type="submit" class="action-btn action-btn--danger">Decline</button>
                                                </form>
                                            @else
                                                <span class="table__note">Managed</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
@endsection