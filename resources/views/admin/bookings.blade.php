@extends('layouts.admin')

@section('title', 'Bookings - Admin Console')

@section('content')
    <div class="admin-head">
        <div>
            <h1 class="admin-head__title">Bookings</h1>
            <p class="admin-head__subtitle">Every appointment placed on the platform, with the option to confirm or cancel</p>
        </div>
    </div>

    <form action="{{ route('admin.bookings') }}" method="GET" class="filters">
        <input type="text" name="search" value="{{ $search }}"
            placeholder="Search by customer, beautician or service">

        <select name="status">
            <option value="">All statuses</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>

        <button type="submit" class="btn">
            <x-icon name="pending" :size="15" />
            Apply filters
        </button>
        <a href="{{ route('admin.bookings') }}" class="btn btn--ghost">Reset</a>
    </form>

    <section class="panel">
        <div class="table-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Beautician</th>
                        <th>Service</th>
                        <th>Date &amp; time</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td class="table__id">#{{ $booking->id }}</td>
                            <td>
                                {{ $booking->customer?->name ?? 'Removed user' }}
                                <span class="table__note">{{ $booking->customer?->email }}</span>
                            </td>
                            <td>{{ $booking->service?->beautician?->user?->name ?? 'Unknown' }}</td>
                            <td>{{ $booking->service?->service_name ?? 'Service removed' }}</td>
                            <td>
                                {{ $booking->booking_date?->format('j M Y') }}
                                <span class="table__note">{{ date('g:i A', strtotime($booking->booking_time)) }}</span>
                            </td>
                            <td>Rs. {{ number_format($booking->service?->price ?? 0, 0) }}</td>
                            <td><span class="pill pill--{{ $booking->status }}">{{ $booking->status }}</span></td>
                            <td>
                                <div class="table__actions">
                                    @if ($booking->status !== 'confirmed')
                                        <form action="{{ route('admin.bookings.status', $booking->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="confirmed">
                                            <button type="submit" class="action-btn action-btn--approve">Confirm</button>
                                        </form>
                                    @endif

                                    @if ($booking->status !== 'cancelled')
                                        <form action="{{ route('admin.bookings.status', $booking->id) }}" method="POST"
                                            onsubmit="return confirm('Cancel booking #{{ $booking->id }}?');">
                                            @csrf
                                            <input type="hidden" name="action" value="cancelled">
                                            <button type="submit" class="action-btn action-btn--danger">Cancel</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty">No bookings match this filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel__foot">
            {{ $bookings->links('vendor.pagination.custom') }}
        </div>
    </section>
@endsection