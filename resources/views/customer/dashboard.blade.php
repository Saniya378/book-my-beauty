@extends('layouts.customer')

@section('title', 'Customer Dashboard - BookMyBeauty')

@section('content')
    <div class="banner">
        <div>
            <h1 class="banner__title">Hello, {{ Auth::user()->name }}</h1>
            <p class="banner__sub">Your appointments, favourites and receipts in one place.</p>
        </div>

        <div class="banner__actions">
            <a href="{{ route('browse') }}" class="btn btn--light">
                <x-icon name="search" :size="15" />
                Browse beauticians
            </a>
        </div>
    </div>

    <div class="kpi-grid">
        <div class="kpi">
            <span class="kpi__icon"><x-icon name="bookings" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ $totalBookings }}</div>
                <div class="kpi__label">Total bookings</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="pending" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ $pendingBookings }}</div>
                <div class="kpi__label">Awaiting confirmation</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="check-circle" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ $confirmedBookings }}</div>
                <div class="kpi__label">Confirmed visits</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="heart" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ $favoriteCount }}</div>
                <div class="kpi__label">Saved beauticians</div>
            </div>
        </div>
    </div>

    <div class="admin-grid admin-grid--split">
        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Recent appointments</h2>
                    <p class="panel__meta">Your latest bookings</p>
                </div>
                <a href="{{ route('customer.bookings') }}" class="panel__link">View all</a>
            </div>

            <div class="panel__body panel__body--flush">
                @if ($recentBookings->isEmpty())
                    <div class="empty">
                        <strong>No bookings yet</strong>
                        Find a beautician and your appointments will appear here.
                    </div>
                @else
                    <div class="table-scroll">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Beautician</th>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentBookings as $booking)
                                    <tr>
                                        <td>{{ $booking->service?->beautician?->user?->name ?? 'Beautician' }}</td>
                                        <td>{{ $booking->service?->service_name ?? 'Service' }}</td>
                                        <td>
                                            {{ $booking->booking_date?->format('j M Y') }}
                                            <span class="table__note">
                                                {{ date('g:i A', strtotime($booking->booking_time)) }}
                                            </span>
                                        </td>
                                        <td><span class="pill pill--{{ $booking->status }}">{{ $booking->status }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Favourites</h2>
                    <p class="panel__meta">Beauticians you saved</p>
                </div>
                <a href="{{ route('customer.favorites') }}" class="panel__link">View all</a>
            </div>

            <div class="panel__body panel__body--flush">
                <ul class="rows">
                    @forelse ($recentFavorites->where('beautician', '!=', null) as $favourite)
                        <li>
                            <img class="avatar" src="{{ $favourite->beautician->photo_url }}"
                                alt="{{ $favourite->beautician->user?->name }}">
                            <div class="rows__main">
                                <div class="rows__title">{{ $favourite->beautician->user?->name ?? 'Beautician' }}</div>
                                <div class="meta">
                                    <x-icon name="map-pin" :size="13" />
                                    {{ $favourite->beautician->location ?: 'Location on booking' }}
                                </div>
                            </div>
                            <a href="{{ route('customer.book', $favourite->beautician_id) }}" class="btn btn--sm">
                                Book
                            </a>
                        </li>
                    @empty
                        <li class="empty">
                            <strong>No favourites yet</strong>
                            Tap the heart on a profile to save it here.
                        </li>
                    @endforelse
                </ul>
            </div>
        </section>
    </div>
@endsection