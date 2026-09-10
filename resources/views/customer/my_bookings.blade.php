@extends('layouts.customer')

@section('title', 'My Bookings - Beauty Portal')

@section('styles')
<style>
    .page-title {
        font-family: 'Playfair Display', serif;
        font-size: 24px;
        color: var(--primary);
        margin-bottom: 5px;
    }
    .booking-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--shadow);
    }
    .booking-table th {
        background: #1a1a2e;
        color: white;
        padding: 14px 18px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: left;
    }
    .booking-table td {
        padding: 16px 18px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
        vertical-align: middle;
    }
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .badge-pending { background: #fff8e1; color: #b78103; border: 1px solid #ffe082; }
    .badge-confirmed { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    .badge-cancelled { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
</style>
@endsection

@section('content')
<div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2 class="page-title">My Bookings</h2>
        <p style="color: var(--text-gray); font-size: 13px; margin: 0;">Track, review, or download receipts for all your beauty appointments.</p>
    </div>
    <a href="{{ route('browse') }}" class="btn" style="padding: 10px 22px; font-size: 13px;">+ Book New Appointment</a>
</div>

@if($bookings->isEmpty())
    <div style="background: white; padding: 50px 20px; text-align: center; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <div style="font-size: 40px; margin-bottom: 15px;">📅</div>
        <h3 style="font-family: 'Playfair Display', serif; font-size: 20px; color: var(--primary); margin-bottom: 8px;">No appointments yet</h3>
        <p style="color: var(--text-gray); font-size: 13px; margin-bottom: 20px;">You haven't booked any beauty treatments yet. Explore our verified artists today!</p>
        <a href="{{ route('browse') }}" class="btn" style="padding: 10px 24px; font-size: 13px;">Find a Beautician</a>
    </div>
@else
    <div style="overflow-x: auto;">
        <table class="booking-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Beautician</th>
                    <th>Service</th>
                    <th>Schedule</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $b)
                <tr>
                    <td><strong>#{{ $b->id }}</strong></td>
                    <td>
                        <strong style="color: var(--primary);">{{ $b->service->beautician->user->name ?? 'Beautician' }}</strong>
                        <div style="font-size: 11px; color: var(--text-gray);">📍 {{ $b->service->beautician->location ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <div>{{ $b->service->service_name ?? 'Service' }}</div>
                        <div style="font-size: 11px; color: var(--text-gray);">⏱ {{ $b->service->duration ?? 0 }} mins</div>
                    </td>
                    <td>
                        <div>📅 {{ $b->booking_date ? $b->booking_date->format('d M Y') : 'N/A' }}</div>
                        <div style="font-size: 11px; color: var(--text-gray);">🕒 {{ date('h:i A', strtotime($b->booking_time)) }}</div>
                    </td>
                    <td>
                        <strong style="color: #2e7d32;">Rs. {{ number_format($b->service->price ?? 0, 0) }}</strong>
                    </td>
                    <td>
                        <span class="badge badge-{{ $b->status }}">
                            {{ ucfirst($b->status) }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
                            <a href="{{ route('customer.bookings.receipt', $b->id) }}" class="btn btn-outline" style="padding: 5px 10px; font-size: 11px; text-decoration: none; border-radius: 4px;" title="Download PDF Receipt">
                                📥 PDF Receipt
                            </a>

                            @if($b->status !== 'cancelled')
                                @if($b->canBeCancelled())
                                    <form action="{{ route('customer.bookings.cancel', $b->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');" style="display:inline;">
                                        @csrf
                                        <button type="submit" style="background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; padding: 5px 10px; border-radius: 4px; font-size: 11px; cursor: pointer; font-weight: 600;">
                                            Cancel
                                        </button>
                                    </form>
                                @else
                                    <span style="font-size: 10px; color: #999;" title="Bookings can only be cancelled within 2 hours of creation">Past 2h window</span>
                                @endif
                            @endif

                            @if($b->status === 'confirmed' && $b->service && $b->service->beautician)
                                <a href="{{ route('customer.review', $b->service->beautician->id) }}" style="font-size: 11px; color: var(--accent); text-decoration: none; font-weight: 600; margin-left: 4px;">
                                    ★ Review
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
