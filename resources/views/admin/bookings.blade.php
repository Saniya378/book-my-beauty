@extends('layouts.admin')

@section('title', 'Manage Bookings - Admin Console')

@section('content')
<div style="background: linear-gradient(135deg, var(--primary), var(--primary-darker)); color: white; padding: 25px 30px; border-radius: var(--border-radius); margin-bottom: 25px; border-bottom: 3px solid var(--accent); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 4px;">System Bookings & Appointments</h2>
        <p style="font-size: 13px; opacity: 0.85; margin: 0;">Comprehensive oversight of all beauty salon bookings placed across the platform.</p>
    </div>
</div>

<!-- Filters -->
<div style="background: white; padding: 20px 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 25px;">
    <form action="{{ route('admin.bookings') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
        <div style="flex: 2; min-width: 200px;">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by customer, beautician, or service..." style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit; outline: none;">
        </div>

        <div style="flex: 1; min-width: 150px;">
            <select name="status" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px; font-family: inherit; outline: none;">
                <option value="">All Statuses</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <button type="submit" class="btn" style="padding: 10px 20px; font-size: 13px;">Filter Bookings</button>
        <a href="{{ route('admin.bookings') }}" class="btn btn-outline" style="padding: 10px 16px; font-size: 13px; text-decoration: none;">Reset</a>
    </form>
</div>

<!-- Bookings Table -->
<div style="background: white; border-radius: var(--border-radius); box-shadow: var(--shadow); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #1a1a2e; color: white; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                    <th style="padding: 14px 18px;">ID</th>
                    <th style="padding: 14px 18px;">Customer</th>
                    <th style="padding: 14px 18px;">Beautician</th>
                    <th style="padding: 14px 18px;">Service</th>
                    <th style="padding: 14px 18px;">Date & Time</th>
                    <th style="padding: 14px 18px;">Amount</th>
                    <th style="padding: 14px 18px;">Status</th>
                    <th style="padding: 14px 18px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 14px 18px;"><strong>#{{ $b->id }}</strong></td>
                    <td style="padding: 14px 18px;">
                        <strong>{{ $b->customer->name ?? 'User' }}</strong>
                        <div style="font-size: 11px; color: var(--text-gray);">{{ $b->customer->email ?? '' }}</div>
                    </td>
                    <td style="padding: 14px 18px;">
                        <strong>{{ $b->service->beautician->user->name ?? 'Beautician' }}</strong>
                    </td>
                    <td style="padding: 14px 18px;">{{ $b->service->service_name ?? 'Service' }}</td>
                    <td style="padding: 14px 18px;">
                        <div>📅 {{ $b->booking_date ? $b->booking_date->format('d M Y') : 'N/A' }}</div>
                        <div style="font-size: 11px; color: var(--text-gray);">🕒 {{ date('h:i A', strtotime($b->booking_time)) }}</div>
                    </td>
                    <td style="padding: 14px 18px; color: #2e7d32; font-weight: 700;">
                        Rs. {{ number_format($b->service->price ?? 0, 0) }}
                    </td>
                    <td style="padding: 14px 18px;">
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 4px 10px; border-radius: 12px;
                            {{ $b->status === 'confirmed' ? 'background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9;' : ($b->status === 'cancelled' ? 'background:#ffebee; color:#c62828; border:1px solid #ffcdd2;' : 'background:#fff8e1; color:#b78103; border:1px solid #ffe082;') }}">
                            {{ $b->status }}
                        </span>
                    </td>
                    <td style="padding: 14px 18px;">
                        <div style="display: flex; gap: 6px;">
                            @if($b->status !== 'confirmed')
                                <form action="{{ route('admin.bookings.status', $b->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="confirmed">
                                    <button type="submit" style="background: #2e7d32; color: white; border: none; padding: 5px 10px; border-radius: 4px; font-size: 11px; cursor: pointer;">Confirm</button>
                                </form>
                            @endif

                            @if($b->status !== 'cancelled')
                                <form action="{{ route('admin.bookings.status', $b->id) }}" method="POST" onsubmit="return confirm('Cancel this booking?');">
                                    @csrf
                                    <input type="hidden" name="action" value="cancelled">
                                    <button type="submit" style="background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; padding: 5px 10px; border-radius: 4px; font-size: 11px; cursor: pointer;">Cancel</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-gray);">
                        No bookings match your filter criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="padding: 15px 20px;">
       {{ $bookings->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection
