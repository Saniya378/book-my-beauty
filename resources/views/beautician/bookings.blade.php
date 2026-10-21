@extends('layouts.beautician')

@section('title', 'Appointments - BookMyBeauty Studio')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-head__title">Appointments</h1>
            <p class="page-head__sub">Confirm or decline requests. Customers are notified automatically.</p>
        </div>
    </div>

    @if ($bookings->isEmpty())
        <section class="panel">
            <div class="empty" style="padding: 55px 20px;">
                <span class="icon-tile" style="margin: 0 auto 16px;"><x-icon name="bookings" :size="20" /></span>
                <strong>No appointments yet</strong>
                <p style="margin: 6px 0 0;">When a customer books one of your services it will show up here.</p>
            </div>
        </section>
    @else
        <section class="panel">
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Schedule</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            <tr>
                                <td class="table__id">{{ $booking->id }}</td>
                                <td>
                                    {{ $booking->customer?->name ?? 'Customer' }}
                                    @if ($booking->customer?->email)
                                        <span class="meta" style="margin-top: 3px;">
                                            <x-icon name="mail" :size="12" />
                                            {{ $booking->customer->email }}
                                        </span>
                                    @endif
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
                                        @elseif ($booking->status === 'confirmed')
                                            <form action="{{ route('beautician.bookings.status', $booking->id) }}" method="POST"
                                                onsubmit="return confirm('Cancel this confirmed appointment?')">
                                                @csrf
                                                <input type="hidden" name="action" value="cancelled">
                                                <button type="submit" class="action-btn action-btn--danger">Cancel</button>
                                            </form>
                                        @else
                                            <span class="table__note">Closed</span>
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