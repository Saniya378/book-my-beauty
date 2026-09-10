@extends('layouts.beautician')

@section('title', 'Client Appointments - Beautician Studio')

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-darker));
        color: white;
        padding: 25px 30px;
        border-radius: var(--border-radius);
        margin-bottom: 25px;
        border-bottom: 3px solid var(--accent);
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
</style>
@endsection

@section('content')
<div class="page-header">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 4px;">Manage Client Appointments</h2>
    <p style="font-size: 13px; opacity: 0.85; margin: 0;">Review, confirm, or cancel incoming appointments. Notifications are automatically sent to clients.</p>
</div>

@if($bookings->isEmpty())
    <div style="background: white; padding: 50px 20px; text-align: center; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <div style="font-size: 40px; margin-bottom: 15px;">📅</div>
        <h3 style="font-family: 'Playfair Display', serif; font-size: 20px; color: var(--primary); margin-bottom: 8px;">No appointments yet</h3>
        <p style="color: var(--text-gray); font-size: 13px;">When customers book your services, their appointments will appear here.</p>
    </div>
@else
    <div style="overflow-x: auto;">
        <table class="booking-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Date & Time</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $b)
                <tr>
                    <td><strong>#{{ $b->id }}</strong></td>
                    <td>
                        <strong>{{ $b->customer->name ?? 'Customer' }}</strong>
                        <div style="font-size: 11px; color: var(--text-gray);">✉️ {{ $b->customer->email ?? '' }}</div>
                    </td>
                    <td>{{ $b->service->service_name ?? 'Service' }}</td>
                    <td>
                        <div>📅 {{ $b->booking_date ? $b->booking_date->format('d M Y') : 'N/A' }}</div>
                        <div style="font-size: 11px; color: var(--text-gray);">🕒 {{ date('h:i A', strtotime($b->booking_time)) }}</div>
                    </td>
                    <td>
                        <strong style="color: #2e7d32;">Rs. {{ number_format($b->service->price ?? 0, 0) }}</strong>
                    </td>
                    <td>
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 4px 10px; border-radius: 12px;
                            {{ $b->status === 'confirmed' ? 'background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9;' : ($b->status === 'cancelled' ? 'background:#ffebee; color:#c62828; border:1px solid #ffcdd2;' : 'background:#fff8e1; color:#b78103; border:1px solid #ffe082;') }}">
                            {{ $b->status }}
                        </span>
                    </td>
                    <td>
                        @if($b->status === 'pending')
                            <div style="display: flex; gap: 8px;">
                                <form action="{{ route('beautician.bookings.status', $b->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="confirmed">
                                    <button type="submit" style="background: #2e7d32; color: white; border: none; padding: 6px 12px; border-radius: 4px; font-size: 11px; cursor: pointer; font-weight: 600;">Confirm</button>
                                </form>
                                <form action="{{ route('beautician.bookings.status', $b->id) }}" method="POST" onsubmit="return confirm('Cancel this appointment?');">
                                    @csrf
                                    <input type="hidden" name="action" value="cancelled">
                                    <button type="submit" style="background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; padding: 6px 12px; border-radius: 4px; font-size: 11px; cursor: pointer; font-weight: 600;">Cancel</button>
                                </form>
                            </div>
                        @elseif($b->status === 'confirmed')
                            <form action="{{ route('beautician.bookings.status', $b->id) }}" method="POST" onsubmit="return confirm('Cancel this confirmed appointment?');">
                                @csrf
                                <input type="hidden" name="action" value="cancelled">
                                <button type="submit" style="background: transparent; color: #c62828; border: 1px solid #ffcdd2; padding: 5px 10px; border-radius: 4px; font-size: 11px; cursor: pointer;">Cancel Appointment</button>
                            </form>
                        @else
                            <span style="font-size: 11px; color: #999;">Cancelled</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
