@extends('layouts.admin')

@section('title', 'Dashboard - Admin Console')

@section('content')
    <div class="admin-head">
        <div>
            <h1 class="admin-head__title">Dashboard</h1>
            <p class="admin-head__subtitle">
                {{ now()->format('l, j F Y') }}
                @if ($pending > 0)
                    &middot; {{ $pending }} booking{{ $pending === 1 ? '' : 's' }} waiting for confirmation
                @endif
            </p>
        </div>

        <div class="admin-head__actions">
            <a href="{{ route('admin.analytics') }}" class="btn btn--ghost">
                <x-icon name="chart" :size="15" />
                Analytics
            </a>
            <a href="{{ route('admin.bookings') }}" class="btn">
                <x-icon name="bookings" :size="15" />
                All bookings
            </a>
        </div>
    </div>

    <div class="kpi-grid">
        <div class="kpi">
            <span class="kpi__icon"><x-icon name="revenue" :size="19" /></span>
            <div>
                <div class="kpi__value">Rs. {{ number_format($revenue, 0) }}</div>
                <div class="kpi__label">Confirmed revenue</div>
                <div class="kpi__note">Across {{ $confirmed }} confirmed bookings</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="bookings" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ $totalBookings }}</div>
                <div class="kpi__label">Total bookings</div>
                <div class="kpi__note">
                    {{ $totalBookings ? round($confirmed / $totalBookings * 100) : 0 }}% confirmed so far
                </div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="pending" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ $pending }}</div>
                <div class="kpi__label">Pending requests</div>
                <div class="kpi__note">Not confirmed by a beautician yet</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="cancelled" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ $cancelled }}</div>
                <div class="kpi__label">Cancelled bookings</div>
                <div class="kpi__note">Rejected or cancelled by either side</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="users" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ $totalUsers }}</div>
                <div class="kpi__label">Registered users</div>
                <div class="kpi__note">{{ $customers }} customers &middot; {{ $beauticians }} beauticians</div>
            </div>
        </div>

        <div class="kpi">
            <span class="kpi__icon"><x-icon name="rating" :size="19" /></span>
            <div>
                <div class="kpi__value">{{ number_format($avgRating, 1) }} <span class="rating">&#9733;</span></div>
                <div class="kpi__label">Average rating</div>
                <div class="kpi__note">From {{ $totalReviews }} customer reviews</div>
            </div>
        </div>
    </div>

    <div class="admin-grid admin-grid--split">
        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Recent bookings</h2>
                    <p class="panel__meta">Latest appointments placed on the platform</p>
                </div>
                <a href="{{ route('admin.bookings') }}" class="panel__link">View all</a>
            </div>

            <div class="panel__body panel__body--flush">
                @if ($recentBookings->isEmpty())
                    <div class="empty">
                        <strong>No bookings yet</strong>
                        Once customers start booking, the latest appointments appear here.
                    </div>
                @else
                    <div class="table-scroll">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Beautician</th>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentBookings as $booking)
                                    <tr>
                                        <td class="table__id">#{{ $booking->id }}</td>
                                        <td>{{ $booking->customer?->name ?? 'Removed user' }}</td>
                                        <td>{{ $booking->service?->beautician?->user?->name ?? 'Unknown' }}</td>
                                        <td>
                                            {{ $booking->service?->service_name ?? 'Service removed' }}
                                            <span class="table__note">Rs. {{ number_format($booking->service?->price ?? 0, 0) }}</span>
                                        </td>
                                        <td>
                                            {{ $booking->booking_date?->format('j M Y') }}
                                            <span class="table__note">{{ date('g:i A', strtotime($booking->booking_time)) }}</span>
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
                    <h2 class="panel__title">Needs a decision</h2>
                    <p class="panel__meta">Oldest pending requests first</p>
                </div>
                <a href="{{ route('admin.bookings', ['status' => 'pending']) }}" class="panel__link">All pending</a>
            </div>

            <div class="panel__body panel__body--flush">
                @forelse ($pendingRequests as $booking)
                    <ul class="rows">
                        <li>
                            <span class="avatar avatar--text">{{ strtoupper(mb_substr($booking->customer?->name ?? '?', 0, 1)) }}</span>

                            <div class="rows__main">
                                <div class="rows__title">{{ $booking->service?->service_name ?? 'Service removed' }}</div>
                                <div class="rows__meta">
                                    {{ $booking->customer?->name ?? 'Removed user' }} &middot;
                                    {{ $booking->booking_date?->format('j M') }},
                                    {{ date('g:i A', strtotime($booking->booking_time)) }}
                                </div>
                            </div>

                            <div class="table__actions">
                                <form action="{{ route('admin.bookings.status', $booking->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="confirmed">
                                    <button type="submit" class="action-btn action-btn--approve">Confirm</button>
                                </form>

                                <form action="{{ route('admin.bookings.status', $booking->id) }}" method="POST"
                                    onsubmit="return confirm('Cancel booking #{{ $booking->id }}?');">
                                    @csrf
                                    <input type="hidden" name="action" value="cancelled">
                                    <button type="submit" class="action-btn action-btn--danger">Cancel</button>
                                </form>
                            </div>
                        </li>
                    </ul>
                @empty
                    <div class="empty">
                        <strong>Nothing pending</strong>
                        Every booking has already been decided.
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <div class="admin-grid admin-grid--thirds">
        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Top beauticians</h2>
                    <p class="panel__meta">Ranked by confirmed bookings</p>
                </div>
            </div>

            <div class="panel__body panel__body--flush">
                @forelse ($topBeauticians as $index => $beautician)
                    <ul class="rows">
                        <li>
                            <span class="rank">{{ $index + 1 }}</span>
                            <img class="avatar" src="{{ $beautician->photo_url }}" alt="{{ $beautician->user?->name }}">
                            <div class="rows__main">
                                <div class="rows__title">{{ $beautician->user?->name ?? 'Removed user' }}</div>
                                <div class="rows__meta">{{ $beautician->location ?: 'Location not set' }}</div>
                            </div>
                            <div class="rows__end">
                                <div class="rows__value">{{ $beautician->bookings_count }}</div>
                                <div class="rating">
                                    {{ number_format($beautician->reviews_avg_rating ?? 0, 1) }} &#9733;
                                </div>
                            </div>
                        </li>
                    </ul>
                @empty
                    <div class="empty">
                        <strong>No beauticians yet</strong>
                        Rankings appear after beauticians confirm bookings.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Newest members</h2>
                    <p class="panel__meta">Most recent registrations</p>
                </div>
                <a href="{{ route('admin.users') }}" class="panel__link">All users</a>
            </div>

            <div class="panel__body panel__body--flush">
                <ul class="rows">
                    @forelse ($recentUsers as $user)
                        <li>
                            <span class="avatar avatar--text">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                            <div class="rows__main">
                                <div class="rows__title">{{ $user->name }}</div>
                                <div class="rows__meta">{{ $user->email }}</div>
                            </div>
                            <div class="rows__end">
                                <span class="pill pill--{{ $user->role }}">{{ $user->role }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="empty">No registrations yet.</li>
                    @endforelse
                </ul>
            </div>
        </section>

        <section class="panel">
            <div class="panel__head">
                <div>
                    <h2 class="panel__title">Latest reviews</h2>
                    <p class="panel__meta">What customers said recently</p>
                </div>
            </div>

            <div class="panel__body panel__body--flush">
                <ul class="rows">
                    @forelse ($recentReviews as $review)
                        <li class="rows--stacked">
                            <div>
                                <span class="rows__title">{{ $review->customer?->name ?? 'Removed user' }}</span>
                                <span class="rows__meta">on {{ $review->beautician?->user?->name ?? 'Removed user' }}</span>
                                <span class="rating">
                                    @for ($star = 1; $star <= 5; $star++)
                                        {!! $star <= $review->rating ? '&#9733;' : '&#9734;' !!}
                                    @endfor
                                </span>
                            </div>
                            <p class="rows__note">{{ $review->comment }}</p>
                            <span class="rows__meta">{{ $review->created_at?->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="empty">No reviews submitted yet.</li>
                    @endforelse
                </ul>
            </div>
        </section>
    </div>
@endsection