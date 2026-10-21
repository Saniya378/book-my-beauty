@extends('layouts.customer')

@section('title', 'My Bookings - BookMyBeauty')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">My bookings</h1>
            <p class="page-head__sub">Track every appointment, download receipts and leave a review.</p>
        </div>

        <div class="page-head__actions">
            <a href="{{ route('browse') }}" class="btn">
                <x-icon name="plus" :size="15" />
                New appointment
            </a>
        </div>
    </div>

    @if ($bookings->isEmpty())
        <section class="panel">
            <div class="empty" style="padding: 55px 20px;">
                <span class="icon-tile" style="margin: 0 auto 16px;"><x-icon name="bookings" :size="20" /></span>
                <strong>No appointments yet</strong>
                <p style="margin: 6px 0 20px;">Explore verified beauticians and book your first treatment.</p>
                <a href="{{ route('browse') }}" class="btn btn--accent">Find a beautician</a>
            </div>
        </section>
    @else
        <section class="panel">
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Beautician</th>
                            <th>Service</th>
                            <th>Schedule</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            <tr>
                                <td class="table__id">{{ $booking->id }}</td>
                                <td>
                                    {{ $booking->service?->beautician?->user?->name ?? 'Beautician' }}
                                    <span class="meta" style="margin-top: 3px;">
                                        <x-icon name="map-pin" :size="12" />
                                        {{ $booking->service?->beautician?->location ?: 'Location on booking' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $booking->service?->service_name ?? 'Service' }}
                                    @if ($booking->service?->duration)
                                        <span class="table__note">{{ $booking->service->duration }} min</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $booking->booking_date?->format('j M Y') }}
                                    <span class="table__note">{{ date('g:i A', strtotime($booking->booking_time)) }}</span>
                                </td>
                                <td>Rs. {{ number_format($booking->service?->price ?? 0, 0) }}</td>
                                <td><span class="pill pill--{{ $booking->status }}">{{ $booking->status }}</span></td>
                                <td>
                                    <div class="table__actions">
                                        <a href="{{ route('customer.bookings.receipt', $booking->id) }}" class="action-btn"
                                            title="Download PDF receipt">
                                            <x-icon name="download" :size="13" />
                                            Receipt
                                        </a>

                                        @if ($booking->status !== 'cancelled' && $booking->canBeCancelled())
                                            <form action="{{ route('customer.bookings.cancel', $booking->id) }}" method="POST"
                                                onsubmit="return confirm('Cancel this booking?')">
                                                @csrf
                                                <button type="submit" class="action-btn action-btn--danger">Cancel</button>
                                            </form>
                                        @endif

                                        @if ($booking->status === 'confirmed' && $booking->service?->beautician)
                                            <a href="{{ route('customer.review', $booking->service->beautician->id) }}"
                                                class="action-btn">
                                                <x-icon name="review" :size="13" />
                                                Review
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="panel__foot">
                {{ $bookings->links('vendor.pagination.custom') }}
            </div>
        </section>
    @endif
@endsection